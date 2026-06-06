<?php

namespace App\Addons\Idea\Controllers\Admin;

use App\Addons\Idea\Models\IdeaTag;
use App\Http\Controllers\Controller;
use App\Models\Admin\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IdeaTagController extends Controller
{
    public function index()
    {
        $this->authorizeExtension();

        return view('idea_admin::tags.index', [
            'items' => IdeaTag::withCount('posts')->orderBy('name')->paginate(25),
            'item' => new IdeaTag,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeExtension();
        IdeaTag::create($this->validated($request));

        return back()->with('success', __('admin.flash.created'));
    }

    public function update(Request $request, IdeaTag $tag)
    {
        $this->authorizeExtension();
        $tag->update($this->validated($request, $tag));

        return back()->with('success', __('admin.flash.updated'));
    }

    public function destroy(IdeaTag $tag)
    {
        $this->authorizeExtension();
        $tag->delete();

        return back()->with('success', __('admin.flash.deleted'));
    }

    protected function validated(Request $request, ?IdeaTag $tag = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:80',
            'slug' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('addon_idea_tags', 'slug')->ignore($tag?->id),
            ],
            'color' => 'nullable|string|max:16',
            'is_public' => 'nullable',
        ]);
        $validated['is_public'] = $request->boolean('is_public');

        return $validated;
    }

    protected function authorizeExtension(): void
    {
        abort_unless(staff_has_permission(Permission::MANAGE_EXTENSIONS), 403);
    }
}
