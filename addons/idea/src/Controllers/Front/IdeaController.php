<?php

namespace App\Addons\Idea\Controllers\Front;

use App\Addons\Idea\Models\IdeaComment;
use App\Addons\Idea\Models\IdeaPost;
use App\Addons\Idea\Models\IdeaSubscriber;
use App\Addons\Idea\Models\IdeaTag;
use App\Addons\Idea\Models\IdeaVote;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IdeaController extends Controller
{
    public function index(Request $request)
    {
        $query = IdeaPost::query()
            ->with(['customer', 'tags'])
            ->withCount(['comments', 'votes']);

        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%'.$search.'%')
                    ->orWhere('description', 'LIKE', '%'.$search.'%');
            });
        }

        if ($request->filled('status') && in_array($request->get('status'), IdeaPost::statuses())) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($query) => $query->where('slug', $request->get('tag'))->where('is_public', true));
        }

        match ($request->get('sort')) {
            'recent' => $query->latest(),
            'commented' => $query->orderByDesc('comments_count')->latest(),
            'updated' => $query->orderByDesc('updated_at'),
            default => $query->orderByDesc('votes_count')->latest(),
        };

        return view('idea::index', [
            'ideas' => $query->paginate(12)->appends($request->query()),
            'tags' => IdeaTag::where('is_public', true)->orderBy('name')->get(),
            'statuses' => IdeaPost::statusLabels(),
            'sorts' => [
                'popular' => __('idea::lang.sorts.popular'),
                'recent' => __('idea::lang.sorts.recent'),
                'commented' => __('idea::lang.sorts.commented'),
                'updated' => __('idea::lang.sorts.updated'),
            ],
            'providers' => $this->enabledProviders(),
        ]);
    }

    public function create()
    {
        return view('idea::create', [
            'tags' => IdeaTag::where('is_public', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:160',
            'description' => 'nullable|string|max:10000',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:addon_idea_tags,id',
        ]);

        $idea = IdeaPost::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'customer_id' => auth('web')->id(),
        ]);
        $idea->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('ideas.show', $idea)->with('success', __('idea::lang.flash.created'));
    }

    public function show(IdeaPost $idea)
    {
        $idea->load(['customer', 'tags', 'responseAdmin']);
        $comments = $idea->comments()
            ->with(['customer', 'admin'])
            ->oldest()
            ->paginate(20);

        return view('idea::show', [
            'idea' => $idea,
            'comments' => $comments,
        ]);
    }

    public function vote(IdeaPost $idea)
    {
        $vote = IdeaVote::where('post_id', $idea->id)->where('customer_id', auth('web')->id())->first();
        if ($vote) {
            $vote->delete();

            return back()->with('success', __('idea::lang.flash.vote_removed'));
        }

        IdeaVote::create([
            'post_id' => $idea->id,
            'customer_id' => auth('web')->id(),
        ]);

        return back()->with('success', __('idea::lang.flash.voted'));
    }

    public function comment(Request $request, IdeaPost $idea)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:5000',
        ]);

        IdeaComment::create([
            'post_id' => $idea->id,
            'customer_id' => auth('web')->id(),
            'content' => $validated['content'],
        ]);

        return back()->with('success', __('idea::lang.flash.commented'));
    }

    public function subscribe(IdeaPost $idea)
    {
        $subscriber = IdeaSubscriber::where('post_id', $idea->id)->where('customer_id', auth('web')->id())->first();
        if ($subscriber) {
            $subscriber->delete();

            return back()->with('success', __('idea::lang.flash.unsubscribed'));
        }

        IdeaSubscriber::create([
            'post_id' => $idea->id,
            'customer_id' => auth('web')->id(),
        ]);

        return back()->with('success', __('idea::lang.flash.subscribed'));
    }

    protected function enabledProviders(): Collection
    {
        if (! app('extension')->extensionIsEnabled('socialauth')) {
            return collect([]);
        }

        return \App\Addons\SocialAuth\Models\ProviderEntity::where('enabled', true)->get();
    }
}
