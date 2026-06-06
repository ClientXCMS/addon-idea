@extends('admin/layouts/admin')
@section('title', __('idea::lang.admin.comments.title'))
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        <div class="card">
            <div class="card-heading">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __('idea::lang.admin.comments.title') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.admin.comments.description') }}</p>
                </div>
            </div>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                <input class="input-text" name="q" value="{{ request('q') }}" placeholder="{{ __('idea::lang.filters.search') }}">
                <select class="input-text" name="status">
                    <option value="">{{ __('idea::lang.filters.all_comments') }}</option>
                    <option value="deleted" @selected(request('status') === 'deleted')>{{ __('idea::lang.filters.deleted_comments') }}</option>
                </select>
                <button class="btn btn-secondary">{{ __('global.filter') }}</button>
            </form>
            <div class="space-y-3">
                @forelse($items as $item)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item->post?->title }}</div>
                                <div class="text-xs text-gray-500">{{ $item->customer?->email ?? $item->admin?->email ?? __('idea::lang.deleted_user') }} · {{ $item->created_at?->format('d/m/Y H:i') }}</div>
                                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{!! nl2br(e($item->content)) !!}</p>
                            </div>
                            @if($item->trashed())
                                <form method="POST" action="{{ route('admin.ideas.comments.restore', $item->id) }}">
                                    @csrf
                                    <button class="btn btn-secondary">{{ __('idea::lang.actions.restore') }}</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.ideas.comments.destroy', $item) }}" class="confirmation-popup">
                                    @csrf
                                    @method('DELETE')
                                    <button>
                                        <span class="py-1 px-2 inline-flex justify-center items-center gap-2 rounded-lg border font-medium bg-red text-red-700 shadow-sm align-middle hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-red-900 dark:hover:bg-red-800 dark:border-red-700 dark:text-white dark:hover:text-white dark:focus:ring-offset-gray-800">
                                            <i class="bi bi-trash"></i>
                                            {{ __('global.delete') }}
                                        </span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('global.no_results') }}</p>
                @endforelse
            </div>
            <div class="py-3">{{ $items->links('admin.shared.layouts.pagination') }}</div>
        </div>
    </div>
@endsection
