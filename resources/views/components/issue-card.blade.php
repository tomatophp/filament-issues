@props(['issue', 'isIgnored'])

<div class="w-full border border-slate-200 dark:border-slate-700 p-4 flex flex-col md:flex-row md:justify-between bg-white hover:bg-slate-50 dark:bg-slate-900 dark:hover:bg-slate-800 rounded-lg">
    <div class="flex justify-start gap-2">
        <div>
            <a href="{{ $issue->url }}" target="_blank" class="flex justify-start gap-2 text-xl">
                <div class="flex flex-col justify-start mt-1">
                    @if($issue->isPullRequest)
                        <x-icon name="bx-git-pull-request" class="w-5 h-5 text-success-500" />
                    @else
                        <x-icon name="bx-bullseye" class="w-5 h-5 text-danger-500" />
                    @endif
                </div>
                <div>
                    {{ $issue->title }}
                </div>
            </a>
            <div class="flex justify-start gap-2 text-xl mt-1">
                <div class="w-5">

                </div>
                <div class="text-slate-400 dark:text-slate-400 text-lg">
                    <span> #{{ $issue->number }} opend {{ $issue->createdAt->diffForHumans() }} in repository </span>
                    <a class="hover:text-primary-500" href="{{ $issue->repoUrl }}" target="_blank">
                        {{ $issue->repoName}}
                    </a>
                    <span> by </span>
                    <a class="hover:text-primary-500" href="{{ $issue->owner->url }}" target="_blank">
                        <img src="{{ $issue->owner->profilePictureUrl }}" class="inline-block w-4 h-4 rounded-full" alt="{{ $issue->owner->name }}">
                        {{ $issue->owner->name }}
                    </a>
                </div>
            </div>
            <div class="flex justify-start gap-2 text-xl mt-1">
                <div class="w-5">

                </div>
                <div>
                    <div>
                        @foreach ($issue->labels as $label)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold border bg-opacity-20" style="color: {{ $label->color }}; border-color: {{ $label->color }}; background-color: {{ $label->color . '30' }}">
                                    {{ $label->name }}
                                </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex flex-col justify-start lg:justify-center lg:items-center">
        @if($issue->commentCount > 0)
            <div class="flex justify-end gap-2">
                <div class="flex flex-col justify-center items-center">
                    <x-icon name="bxs-comment" class="w-5 h-5 text-primary-500" />
                </div>
                <div>
                    {{ $issue->commentCount }}
                </div>
            </div>
        @endif

        @foreach ($issue->reactions as $reaction)
            <div>
                @if ($reaction->pivot->count > 0)
                    <span class="inline-flex items-center px-2 py-1 text-sm border rounded bg-opacity-20 dark:border-slate-600">
                        {{ $reaction->emoji }} {{ $reaction->pivot->count }}
                    </span>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{--<div x-cloak class="w-full p-4 break-words bg-white dark:bg-slate-800 border rounded-lg shadow dark:border-slate-600 sm:py-5 sm:px-8" wire:loading.class="animate-pulse" x-data="{showMore: false}">--}}
{{--    <div class="flex flex-col items-start justify-start gap-2 sm:flex-row sm:justify-between">--}}
{{--        <div class="w-full lg:w-1/2 overflow-auto">--}}
{{--            <a href="{{ $issue->url }}" target="_blank" class="text-xl font-bold flex justify-start gap-2">--}}
{{--                <div class="flex flex-col justify-center items-center">--}}
{{--                    @if($issue->isPullRequest)--}}
{{--                        <x-icon name="bx-git-pull-request" class="w-5 h-5 text-primary-500" />--}}
{{--                    @else--}}
{{--                        <x-icon name="bx-bullseye" class="w-5 h-5 text-primary-500" />--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    {{ $issue->title }}--}}
{{--                </div>--}}
{{--            </a>--}}
{{--            <div class="ml-32">--}}
{{--                <a href="{{ $issue->repoUrl }}" target="_blank" class="text-slate-400">{{ $issue->repoName }}</a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}



{{--    <div class="my-3">--}}
{{--        @if($issue->commentCount > 0)--}}
{{--            <span class="inline-flex items-center px-2 py-1 text-sm border rounded bg-opacity-20 dark:border-slate-600">--}}
{{--                {{ $issue->commentCount }} {{ str('comment')->plural($issue->commentCount) }}--}}
{{--            </span>--}}
{{--        @endif--}}

{{--        @foreach ($issue->reactions as $reaction)--}}
{{--            @if ($reaction->pivot->count > 0)--}}
{{--                <span class="inline-flex items-center px-2 py-1 text-sm border rounded bg-opacity-20 dark:border-slate-600">--}}
{{--                    {{ $reaction->emoji }} {{ $reaction->pivot->count }}--}}
{{--                </span>--}}
{{--            @endif--}}
{{--        @endforeach--}}
{{--    </div>--}}

{{--    <div class="flex flex-col items-start justify-between gap-2 sm:flex-row sm:items-center mt-3">--}}
{{--        <a href="{{ $issue->owner->url }}" target="_blank"--}}
{{--            class="inline-block p-2 transition ease-out border rounded  dark:border-slate-700 dark:hover:bg-slate-900">--}}
{{--            <img src="{{ $issue->owner->profilePictureUrl }}" class="inline-block w-6 h-6 rounded-full"--}}
{{--                alt="{{ $issue->owner->name }}">--}}
{{--            <p class="inline">{{ $issue->owner->name }}</p>--}}
{{--        </a>--}}

{{--        <p class="text-sm text-slate-400">{{ $issue->createdAt->format('jS M Y @ H:i') }}</p>--}}
{{--    </div>--}}


{{--    <div class="flex justify-end gap-2 mt-3">--}}
{{--        <x-filament::button tag="a" href="{{ $issue->url }}" target="_blank">--}}
{{--            View Issue--}}
{{--        </x-filament::button>--}}
{{--    </div>--}}
{{--</div>--}}
