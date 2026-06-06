@extends('layouts.front')
@section('title', $idea->title)
@section('content')
    @php
        $markdown = new Parsedown();
        if (method_exists($markdown, 'setSafeMode')) {
            $markdown->setSafeMode(true);
        }
    @endphp
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('shared/alerts')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-2">
                @auth
                    <form method="POST" action="{{ route('ideas.vote', $idea) }}">
                        @csrf
                        <button class="w-full rounded-lg border border-indigo-200 dark:border-indigo-900 bg-indigo-50 dark:bg-indigo-950 p-4 text-center hover:bg-indigo-100 dark:hover:bg-indigo-900">
                            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-300">{{ $idea->votes_count }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $idea->hasVoted() ? __('idea::lang.actions.unvote') : __('idea::lang.actions.vote') }}</div>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block rounded-lg border border-gray-200 dark:border-gray-700 p-4 text-center">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $idea->votes_count }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ __('idea::lang.actions.login_to_vote') }}</div>
                    </a>
                @endauth
            </div>

            <div class="lg:col-span-10 space-y-6">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-700 dark:text-gray-200">{{ __('idea::lang.statuses.' . $idea->status) }}</span>
                        @foreach($idea->tags as $tag)
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium text-white" style="background-color: #{{ $tag->color }}">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    <h1 class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">{{ $idea->title }}</h1>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('idea::lang.by') }} {{ $idea->customer?->excerptFullName() ?? __('idea::lang.deleted_user') }}</p>
                    <div class="mt-6 prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! $markdown->text($idea->description ?: '') !!}
                    </div>

                    @if($idea->response)
                        <div class="mt-6 rounded-lg border border-green-200 dark:border-green-900 bg-green-50 dark:bg-green-950 p-5">
                            <h2 class="text-sm font-semibold text-green-800 dark:text-green-200">{{ __('idea::lang.official_response') }}</h2>
                            <div class="prose prose-sm dark:prose-invert mt-2 max-w-none text-green-900 dark:text-green-100">
                                {!! $markdown->text($idea->response) !!}
                            </div>
                        </div>
                    @endif

                    @auth
                        <form method="POST" action="{{ route('ideas.subscribe', $idea) }}" class="mt-4">
                            @csrf
                            <button class="btn btn-secondary">{{ $idea->isSubscribed() ? __('idea::lang.actions.unsubscribe') : __('idea::lang.actions.subscribe') }}</button>
                        </form>
                    @endauth
                </div>

                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('idea::lang.comments') }}</h2>
                    @auth
                        <form method="POST" action="{{ route('ideas.comments.store', $idea) }}" class="mt-4">
                            @csrf
                            @include('shared/textarea', ['name' => 'content', 'label' => __('idea::lang.add_comment'), 'value' => old('content'), 'rows' => 4])
                            <button class="btn btn-primary mt-3">{{ __('idea::lang.actions.comment') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-secondary mt-4">{{ __('idea::lang.actions.login_to_comment') }}</a>
                    @endauth

                    <div class="mt-6 space-y-4">
                        @forelse($comments as $comment)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $comment->customer?->excerptFullName() ?? $comment->admin?->username ?? __('idea::lang.deleted_user') }}
                                    · {{ $comment->created_at?->format('d/m/Y H:i') }}
                                </div>
                                <div class="prose prose-sm dark:prose-invert mt-2 max-w-none text-gray-700 dark:text-gray-300">
                                    {!! $markdown->text($comment->content) !!}
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('idea::lang.no_comments') }}</p>
                        @endforelse
                    </div>
                    <div class="mt-6">{{ $comments->links('shared.layouts.pagination') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
