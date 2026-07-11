@extends('layouts.front')
@section('title', __('idea::lang.title'))
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('shared/alerts')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <aside class="lg:col-span-4 lg:sticky lg:top-6 space-y-4">
                <form method="GET" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                    <input class="input-text" name="q" value="{{ request('q') }}" placeholder="{{ __('idea::lang.filters.search') }}">
                    <select class="input-text mt-3" name="sort">
                        @foreach($sorts as $value => $label)
                            <option value="{{ $value }}" @selected(request('sort', 'popular') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('ideas.index', array_filter(request()->except(['status', 'page']))) }}" class="inline-flex rounded-full px-3 py-1.5 text-xs font-medium {{ request('status') ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' : 'bg-indigo-600 text-white' }}">
                            {{ __('idea::lang.filters.all_statuses') }}
                        </a>
                        @foreach($statuses as $value => $label)
                            <a href="{{ route('ideas.index', array_merge(request()->except('page'), ['status' => $value])) }}" class="inline-flex rounded-full px-3 py-1.5 text-xs font-medium {{ request('status') === $value ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                    @if($tags->count() > 0)
                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('ideas.index', array_filter(request()->except(['tag', 'page']))) }}" class="inline-flex rounded-full border px-3 py-1.5 text-xs font-medium {{ request('tag') ? 'border-gray-200 text-gray-700 dark:border-gray-700 dark:text-gray-200' : 'border-indigo-600 text-indigo-600 dark:text-indigo-400' }}">
                                {{ __('idea::lang.filters.all_tags') }}
                            </a>
                            @foreach($tags as $tag)
                                <a href="{{ route('ideas.index', array_merge(request()->except('page'), ['tag' => $tag->slug])) }}" class="inline-flex rounded-full border px-3 py-1.5 text-xs font-medium {{ request('tag') === $tag->slug ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400' : 'border-gray-200 text-gray-700 dark:border-gray-700 dark:text-gray-200' }}">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <button class="btn btn-secondary w-full mt-4">{{ __('global.filter') }}</button>
                </form>

                @auth
                    <form method="POST" action="{{ route('ideas.store') }}" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                        @csrf
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('idea::lang.create.sidebar_title') }}</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.create.sidebar_subtitle') }}</p>
                        <div class="mt-4 space-y-4">
                            @include('shared/input', ['name' => 'title', 'label' => __('global.title'), 'value' => old('title')])
                            @include('shared/textarea', ['name' => 'description', 'label' => __('global.content'), 'value' => old('description'), 'rows' => 5])
	                            @if($tags->count() > 0)
	                                <div>
	                                    <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-400 mt-2">{{ __('idea::lang.tags') }}</label>
	                                    <div class="mt-2 flex flex-wrap gap-2">
	                                        @foreach($tags as $tag)
	                                            <label class="inline-flex cursor-pointer items-center">
	                                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="peer sr-only" @checked(in_array($tag->id, old('tags', [])))>
	                                                <span class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 peer-checked:border-transparent peer-checked:bg-[var(--tag-color)] peer-checked:text-white peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800" style="--tag-color: #{{ $tag->color }};">
	                                                    <span class="h-2 w-2 rounded-full bg-[var(--tag-color)]"></span>
	                                                    <span>{{ $tag->name }}</span>
	                                                </span>
	                                            </label>
	                                        @endforeach
	                                    </div>
	                                </div>
                            @endif
                            <button class="btn btn-primary w-full">{{ __('idea::lang.actions.submit') }}</button>
                        </div>
                    </form>
                @else
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-5">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('idea::lang.login.title') }}</h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.login.subtitle') }}</p>

                        <div class="mt-4">
                            @foreach ($providers ?? [] as $provider)
                                <a href="{{ route('socialauth.authorize', $provider->name) }}" class="@if(!$loop->first) mt-2 @endif w-full py-3 px-4 inline-flex justify-center items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800">
                                    <img src="{{ $provider->provider()->logo() }}" alt="{{ $provider->provider()->title() }}" class="w-5 h-5">
                                    {{ __('socialauth::messages.login_with', ['provider' => $provider->provider()->title()]) }}
                                </a>
                            @endforeach
                            @if (($providers ?? collect())->isNotEmpty())
                                <div class="py-4 flex items-center text-xs text-gray-400 uppercase before:flex-[1_1_0%] before:border-t before:border-gray-200 before:me-4 after:flex-[1_1_0%] after:border-t after:border-gray-200 after:ms-4 dark:text-gray-500 dark:before:border-gray-600 dark:after:border-gray-600">
                                    {{ trans('global.or') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @php($label = null) @php($value = null)

                                @include('shared.auth.login', ['redirect' => route('ideas.index'), 'captcha' => true])
                            </form>
                        </div>
                    </div>
                @endauth
            </aside>

            <div class="lg:col-span-8">
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="border-b border-gray-200 dark:border-gray-700 p-5">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('idea::lang.title') }}</h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.subtitle') }}</p>
                    </div>

                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($ideas as $idea)
                            <a href="{{ route('ideas.show', $idea) }}" class="block p-5 hover:bg-gray-50 dark:hover:bg-gray-900 transition">
                                <div class="flex gap-4">
                                    <div class="w-16 h-16 shrink-0 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-2 text-center">
                                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $idea->votes_count }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('idea::lang.votes') }}</div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex rounded-full bg-indigo-50 dark:bg-indigo-950 px-2 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-200">{{ __('idea::lang.statuses.' . $idea->status) }}</span>
                                            @foreach($idea->tags as $tag)
                                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium text-white" style="background-color: #{{ $tag->color }}">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                        <h2 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $idea->title }}</h2>
                                        <p class="mt-2 line-clamp-2 text-sm text-gray-600 dark:text-gray-400">{{ str($idea->description)->limit(220) }}</p>
                                        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                            <span>{{ trans_choice('idea::lang.comments_count', $idea->comments_count, ['count' => $idea->comments_count]) }}</span>
                                            <span>{{ $idea->customer?->excerptFullName() ?? __('idea::lang.deleted_user') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center text-gray-600 dark:text-gray-400">
                                {{ __('global.no_results') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6">
                    {{ $ideas->links('shared.layouts.pagination') }}
                </div>
            </div>

        </div>
    </div>
@endsection
