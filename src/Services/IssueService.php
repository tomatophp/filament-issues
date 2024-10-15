<?php

declare(strict_types=1);

namespace TomatoPHP\FilamentIssues\Services;

use TomatoPHP\FilamentIssues\Clients\GitHub;
use TomatoPHP\FilamentIssues\Exceptions\GitHubRateLimitException;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use TomatoPHP\FilamentIssues\Jobs\FetchIssuesByRepo;
use TomatoPHP\FilamentIssues\Models\Issue;
use TomatoPHP\FilamentIssues\Models\IssueOwner;
use TomatoPHP\FilamentIssues\Models\Label;
use TomatoPHP\FilamentIssues\Models\Reaction;
use TomatoPHP\FilamentIssues\Models\Repository;

final readonly class IssueService
{


    /**
     * @return array
     */
    public function getAll(): array
    {
        return app(RepoService::class)
            ->reposToCrawl()
            ->flatMap(fn (Repository $repo) => dispatch(new FetchIssuesByRepo($repo)))
            ->toArray();
    }


    /**
     * @param Repository $repo
     * @param bool $forceRefresh
     * @return array
     * @throws GitHubRateLimitException
     */
    public function getIssuesForRepo(Repository $repo, bool $forceRefresh = false): array
    {
        $fetchedIssues = $this->getIssuesFromGitHubApi($repo);

        return collect($fetchedIssues)
            ->filter(fn (Issue $issue): bool => $this->shouldIncludeIssue($issue))
            ->all();
    }

    private function parseIssue(Repository $repo, array $fetchedIssue): Issue
    {
        $issue = Issue::query()
            ->where('repo_id', $repo->id)
            ->where('issue_id', $fetchedIssue['id'])
            ->first();

        $owner = $this->getIssueOwner($fetchedIssue);

        if(!$issue){
            $issue = new Issue();
            $issue->issue_id = $fetchedIssue['id'];
            $issue->repo_id = $repo->id;
            $issue->number = $fetchedIssue['number'];
            $issue->repoName = $repo->repo;
            $issue->repoUrl = 'https://github.com/'.$repo->repo;
            $issue->title = $fetchedIssue['title'];
            $issue->url = $fetchedIssue['html_url'];
            $issue->body = $fetchedIssue['body'];
            $issue->commentCount = $fetchedIssue['comments']??0;
            $issue->createdAt = Carbon::parse($fetchedIssue['created_at']);
            $issue->createdBy = $owner->id;
            $issue->isPullRequest = ! empty($fetchedIssue['pull_request']);
            $issue->save();
        }
        else {
            $issue->commentCount = $fetchedIssue['comments']??0;
            $issue->body = $fetchedIssue['body'];
            $issue->save();
        }

        $this->getIssueLabels($fetchedIssue, $issue);
        $this->getIssueReactions($fetchedIssue, $issue);


        return $issue;
    }

    private function shouldIncludeIssue(Issue $fetchedIssue): bool
    {
        return ! $fetchedIssue->isPullRequest
            && $this->includesAtLeastOneLabel($fetchedIssue, (array) config('repos.labels'));
    }

    private function includesAtLeastOneLabel(Issue $fetchedIssue, array $labels): bool
    {
        $issueLabels = Arr::pluck($fetchedIssue->labels, 'name');

        return array_intersect($issueLabels, $labels) !== [];
    }

    private function getIssueOwner(array $fetchedIssue): IssueOwner
    {
        // Set avatar size to 48px
        $fetchedIssue['user']['avatar_url'] .= (parse_url($fetchedIssue['user']['avatar_url'], PHP_URL_QUERY) ? '&' : '?').'s=48';

        $owner = IssueOwner::query()
            ->where('name', $fetchedIssue['user']['login'])
            ->where('url', $fetchedIssue['user']['html_url'])
            ->first();

        if(!$owner){
            $owner = new IssueOwner();
            $owner->name = $fetchedIssue['user']['login'];
            $owner->url = $fetchedIssue['user']['html_url'];
            $owner->profilePictureUrl = $fetchedIssue['user']['avatar_url'];
            $owner->save();
        }

        return $owner;
    }

    private function getIssueLabels(array $fetchedIssue, Issue $issue): array
    {
        $labels =  collect($fetchedIssue['labels'])
            ->map(function (array $label): Label {
                $checkLabel = Label::query()
                    ->where('name', $label['name'])
                    ->where('color', '#'.$label['color'])
                    ->first();

                if(!$checkLabel){
                    $checkLabel = new Label();
                    $checkLabel->name = $label['name'];
                    $checkLabel->color = '#'.$label['color'];
                    $checkLabel->save();
                }

                return $checkLabel;

            })->toArray();

        $issue->labels()->sync(collect($labels)->pluck('id'));

        return $labels;
    }

    private function getIssueReactions(array $fetchedIssue, Issue $issue): array
    {
        $emojis = config('filament-issues.reactions');

        $reactions = collect($fetchedIssue['reactions'])
            ->only(array_keys($emojis))
            ->map(function (int $count, string $content) use ($emojis, $issue): Reaction {
                $reaction = Reaction::query()
                    ->where('content', $content)
                    ->where('emoji', $emojis[$content])
                    ->first();

                if(!$reaction){
                    $reaction = new Reaction();
                    $reaction->content = $content;
                    $reaction->emoji = $emojis[$content];
                    $reaction->save();
                }

                $issue->reactions()->attach($reaction->id, ['count' => $count]);

                return $reaction;
            })
            ->values()
            ->all();

        return $reactions;
    }

    /**
     * @return array<Issue>
     *
     * @throws GitHubRateLimitException
     */
    private function getIssuesFromGitHubApi(Repository $repo): array
    {

        $result = app(GitHub::class)
            ->client()
            ->get('repos/'.$repo->repo.'/issues');

        if (! $result->successful()) {
            return $this->handleUnsuccessfulIssueRequest($result, $repo->repo);
        }

        $fetchedIssues = $result->json();

        return collect($fetchedIssues)
            ->map(fn (array $fetchedIssue): Issue => $this->parseIssue($repo, $fetchedIssue))
            ->all();
    }

    /**
     * @throws GitHubRateLimitException
     */
    private function handleUnsuccessfulIssueRequest(Response $response, string $fullRepoName): array
    {
        return match ($response->status()) {
            404 => $this->handleNotFoundResponse($fullRepoName),
            403 => $this->handleForbiddenResponse($response, $fullRepoName),
            default => [],
        };
    }

    private function handleNotFoundResponse(string $fullRepoName): array
    {
        report($fullRepoName.' is not a valid GitHub repo.');

        return [];
    }

    /**
     * @throws GitHubRateLimitException
     */
    private function handleForbiddenResponse(Response $response, string $fullRepoName): array
    {
        if ($response->header('X-RateLimit-Remaining') === '0') {
            throw new GitHubRateLimitException('GitHub API rate limit reached!');
        }

        report($fullRepoName.' is a forbidden GitHub repo.');

        return [];
    }
}
