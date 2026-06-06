@extends('layouts.front')
@section('title', __('idea::lang.create.title'))
@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        @include('shared/alerts')
        <form method="POST" action="{{ route('ideas.store') }}" class="card">
            @csrf
            <div class="card-heading">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('idea::lang.create.title') }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('idea::lang.create.subtitle') }}</p>
                </div>
                <button class="btn btn-primary">{{ __('idea::lang.actions.submit') }}</button>
            </div>
            <div class="card-body space-y-4">
                @include('shared/input', ['name' => 'title', 'label' => __('global.title'), 'value' => old('title')])
                @include('shared/textarea', ['name' => 'description', 'label' => __('global.content'), 'value' => old('description'), 'rows' => 8])
                @if($tags->count() > 0)
                    <div>
                        <label class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-400 mt-2">{{ __('idea::lang.tags') }}</label>
                        <div class="mt-2 flex flex-wrap gap-3">
                            @foreach($tags as $tag)
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, old('tags', [])))>
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium text-white" style="background-color: #{{ $tag->color }}">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection
