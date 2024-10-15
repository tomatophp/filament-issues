<?php

namespace TomatoPHP\FilamentIssues\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Livewire\Wireable;

class Issue extends Model
{
    protected $table = 'git_issues';

    protected $fillable = [
        'id',
        'issue_id',
        'number',
        'repo_id',
        'repoName',
        'repoUrl',
        'title',
        'url',
        'body',
        'commentCount',
        'createdAt',
        'createdBy',
        'isPullRequest',
        'is_public',
        'is_trend',
    ];

    protected $casts = [
        'createdAt' => 'datetime',
        'isPullRequest' => 'boolean',
        'is_public' => 'boolean',
        'is_trend' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(IssueOwner::class, 'createdBy', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function repo(): BelongsTo
    {
        return $this->belongsTo(Repository::class, 'repo_id');
    }

    /**
     * @return BelongsToMany
     */
    public function reactions(): BelongsToMany
    {
        return $this->belongsToMany(Reaction::class, 'git_issues_has_reactions', 'issue_id', 'reaction_id')
            ->withPivot('count')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'git_issues_has_labels', 'issue_id', 'label_id')
            ->withTimestamps();
    }
}
