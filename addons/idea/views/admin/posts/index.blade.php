@extends('admin/layouts/admin')
@section('title', __('idea::lang.admin.posts.title'))
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        <div class="card">
            <div class="card-heading">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __('idea::lang.admin.posts.title') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.admin.posts.description') }}</p>
                </div>
                <a class="btn btn-primary" href="{{ route('admin.ideas.posts.create') }}">{{ __('admin.create') }}</a>
            </div>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                <input class="input-text" name="q" value="{{ request('q') }}" placeholder="{{ __('idea::lang.filters.search') }}">
                <select class="input-text" name="status">
                    <option value="">{{ __('idea::lang.filters.all_statuses') }}</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select class="input-text" name="tag">
                    <option value="">{{ __('idea::lang.filters.all_tags') }}</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->slug }}" @selected(request('tag') === $tag->slug)>{{ $tag->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-secondary">{{ __('global.filter') }}</button>
            </form>
            <div class="border rounded-lg overflow-hidden dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                    <tr>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">#</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('global.title') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('global.status') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('idea::lang.votes') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('idea::lang.comments') }}</th>
                        <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('global.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr class="bg-white hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-800">
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->id }}</td>
                            <td class="px-6 py-3">
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item->title }}</div>
                                <div class="text-xs text-gray-500">{{ $item->customer?->email ?? __('idea::lang.deleted_user') }}</div>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.statuses.' . $item->status) }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->votes_count }}</td>
                            <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->comments_count }}</td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <a href="{{ route('admin.ideas.posts.show', $item) }}">
                                    <span class="px-1 py-1.5">
                                        <span class="py-1 px-2 inline-flex justify-center items-center gap-2 rounded-lg border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-slate-900 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-white dark:focus:ring-offset-gray-800">
                                            <i class="bi bi-eye-fill"></i>
                                            {{ __('global.show') }}
                                        </span>
                                    </span>
                                </a>
                                <form method="POST" action="{{ route('admin.ideas.posts.destroy', $item) }}" class="inline confirmation-popup">
                                    @csrf
                                    @method('DELETE')
                                    <button>
                                        <span class="py-1 px-2 inline-flex justify-center items-center gap-2 rounded-lg border font-medium bg-red text-red-700 shadow-sm align-middle hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-red-900 dark:hover:bg-red-800 dark:border-red-700 dark:text-white dark:hover:text-white dark:focus:ring-offset-gray-800">
                                            <i class="bi bi-trash"></i>
                                            {{ __('global.delete') }}
                                        </span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">{{ __('global.no_results') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="py-3">{{ $items->links('admin.shared.layouts.pagination') }}</div>
        </div>
    </div>
@endsection
