@extends('admin/layouts/admin')
@section('title', __('idea::lang.admin.tags.title'))
@section('content')
<div class="container mx-auto">
    @include('admin/shared/alerts')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">
        <div class="lg:col-span-1">
            <form method="POST" action="{{ route('admin.ideas.tags.store') }}" class="card">
                @csrf
                <div class="card-heading">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __('idea::lang.admin.tags.create') }}</h2>
                    <button class="btn btn-primary">{{ __('admin.create') }}</button>
                </div>
                <div>
                    @include('admin/shared/input', ['name' => 'name', 'label' => __('global.name'), 'value' => old('name')])
                </div>
                <div>
                    @include('admin/shared/input', ['name' => 'slug', 'label' => __('global.slug'), 'value' => old('slug')])
                </div>
                <div>
                    @include('admin/shared/input', ['name' => 'color', 'label' => __('idea::lang.color'), 'value' => old('color', '2563eb')])
                </div>
                <div class="mt-4">
                    @include('admin/shared/checkbox', ['name' => 'is_public', 'label' => __('idea::lang.public'), 'checked' => true])
                </div>
            </form>
        </div>
    <div class="card lg:col-span-2">
        <div class="card-heading">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __('idea::lang.admin.tags.title') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.admin.tags.description') }}</p>
            </div>
        </div>
        <div class="space-y-3">
            @foreach($items as $tag)
            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <form method="POST" action="{{ route('admin.ideas.tags.update', $tag) }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    @csrf
                    @method('PUT')
                    <div>
                        @include('admin/shared/input', ['name' => 'name', 'label' => __('global.name'), 'value' => $tag->name])
                    </div>
                    <div>

                        @include('admin/shared/input', ['name' => 'slug', 'label' => __('global.slug'), 'value' => $tag->slug])
                    </div>
                    <div>
                        @include('admin/shared/input', ['name' => 'color', 'label' => __('idea::lang.color'), 'value' => $tag->color])
                    </div>
                    <div>@include('admin/shared/checkbox', ['name' => 'is_public', 'label' => __('idea::lang.public'), 'checked' => $tag->is_public])</div>
                    <div class="flex gap-2">
                        <button class="btn btn-secondary">{{ __('global.save') }}</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.ideas.tags.destroy', $tag) }}" class="confirmation-popup mt-2">
                    @csrf
                    @method('DELETE')
                    <button>
                        <span class="py-1 px-2 inline-flex justify-center items-center gap-2 rounded-lg border font-medium bg-red text-red-700 shadow-sm align-middle hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-blue-600 transition-all text-sm dark:bg-red-900 dark:hover:bg-red-800 dark:border-red-700 dark:text-white dark:hover:text-white dark:focus:ring-offset-gray-800">
                            <i class="bi bi-trash"></i>
                            {{ __('global.delete') }}
                        </span>
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        <div class="py-3">{{ $items->links('admin.shared.layouts.pagination') }}</div>
    </div>
</div>
</div>
@endsection
