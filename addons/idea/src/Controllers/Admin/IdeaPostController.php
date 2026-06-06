<?php

namespace App\Addons\Idea\Controllers\Admin;

use App\Addons\Idea\Models\IdeaPost;
use App\Addons\Idea\Models\IdeaTag;
use App\Http\Controllers\Controller;
use App\Models\Admin\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IdeaPostController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeExtension();

        $query = IdeaPost::with(['customer', 'tags'])->withCount(['comments', 'votes']);

        if ($request->filled('q')) {
            $query->where('title', 'LIKE', '%'.$request->get('q').'%');
        }
        if ($request->filled('status') && in_array($request->get('status'), IdeaPost::statuses())) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($query) => $query->where('slug', $request->get('tag')));
        }

        return view('idea_admin::posts.index', [
            'items' => $query->latest()->paginate(25)->appends($request->query()),
            'statuses' => IdeaPost::statusLabels(),
            'tags' => IdeaTag::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $this->authorizeExtension();

        return view('idea_admin::posts.show', [
            'item' => new IdeaPost,
            'statuses' => IdeaPost::statusLabels(),
            'tags' => IdeaTag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeExtension();
        $validated = $this->validated($request);

        $payload = $this->postPayload($validated);
        if (! empty($payload['response'])) {
            $payload['response_at'] = now();
            $payload['response_admin_id'] = auth('admin')->id();
        }
        $idea = IdeaPost::create($payload);
        $idea->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('admin.ideas.posts.show', $idea)->with('success', __('admin.flash.created'));
    }

    public function show(IdeaPost $idea)
    {
        $this->authorizeExtension();
        $idea->load(['customer', 'tags', 'responseAdmin', 'comments.customer', 'comments.admin']);

        return view('idea_admin::posts.show', [
            'item' => $idea,
            'statuses' => IdeaPost::statusLabels(),
            'tags' => IdeaTag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, IdeaPost $idea)
    {
        $this->authorizeExtension();
        $validated = $this->validated($request, $idea);
        $payload = $this->postPayload($validated);

        if (! empty($payload['response']) && $payload['response'] !== $idea->response) {
            $payload['response_at'] = now();
            $payload['response_admin_id'] = auth('admin')->id();
        }
        if (empty($payload['response'])) {
            $payload['response_at'] = null;
            $payload['response_admin_id'] = null;
        }

        $idea->update($payload);
        $idea->tags()->sync($validated['tags'] ?? []);

        return back()->with('success', __('admin.flash.updated'));
    }

    public function destroy(IdeaPost $idea)
    {
        $this->authorizeExtension();
        $idea->delete();

        return redirect()->route('admin.ideas.posts.index')->with('success', __('admin.flash.deleted'));
    }

    protected function validated(Request $request, ?IdeaPost $idea = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:160',
            'slug' => [
                'nullable',
                'string',
                'max:180',
                Rule::unique('addon_idea_posts', 'slug')->ignore($idea?->id),
            ],
            'description' => 'nullable|string|max:10000',
            'status' => ['required', Rule::in(IdeaPost::statuses())],
            'response' => 'nullable|string|max:10000',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:addon_idea_tags,id',
        ]);
    }

    protected function postPayload(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'response' => $validated['response'] ?? null,
        ];
    }

    protected function authorizeExtension(): void
    {
        abort_unless(staff_has_permission(Permission::MANAGE_EXTENSIONS), 403);
    }
}
