@extends('admin/layouts/admin')
@section('title', $item->exists ? __('idea::lang.admin.posts.edit') : __('idea::lang.admin.posts.create'))
@section('content')
<div class="container mx-auto">
    @include('admin/shared/alerts')
    <form method="POST" action="{{ $item->exists ? route('admin.ideas.posts.update', $item) : route('admin.ideas.posts.store') }}" class="card">
        @csrf
        @if($item->exists)
        @method('PUT')
        @endif
        <div class="card-heading">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ $item->exists ? __('idea::lang.admin.posts.edit') : __('idea::lang.admin.posts.create') }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.admin.posts.description') }}</p>
            </div>
            <button class="btn btn-primary">{{ __('global.save') }}</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                @include('admin/shared/input', ['name' => 'title', 'label' => __('global.title'), 'value' => old('title', $item->title)])

            </div>
            <div>

                @include('admin/shared/input', ['name' => 'slug', 'label' => __('global.slug'), 'value' => old('slug', $item->slug)])
            </div>
            <div>

                @include('admin/shared/select', ['name' => 'status', 'label' => __('global.status'), 'options' => $statuses, 'value' => old('status', $item->status ?: 'open')])
            </div>
        </div>
        <div class="mt-4">
            @include('admin/shared/textarea', ['name' => 'description', 'label' => __('global.content'), 'value' => old('description', $item->description), 'rows' => 8])
        </div>
        <div class="mt-4">
            @include('admin/shared/textarea', ['name' => 'response', 'label' => __('idea::lang.official_response'), 'value' => old('response', $item->response), 'rows' => 5])
        </div>
        @if($tags->count() > 0)
        <div class="mt-4">
            <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-400">{{ __('idea::lang.tags') }}</label>
            <div class="mt-2 flex flex-wrap gap-3">
                @foreach($tags as $tag)
                @include('admin/shared/checkbox', ['name' => 'tags[]', 'label' => $tag->name, 'value' => $tag->id, 'checked' => in_array($tag->id, old('tags', $item->tags->pluck('id')->all()))])
                @endforeach
            </div>
        </div>
        @endif
    </form>
</div>
@endsection