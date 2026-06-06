<?php

namespace App\Addons\Idea\Controllers\Admin;

use App\Addons\Idea\Models\IdeaComment;
use App\Http\Controllers\Controller;
use App\Models\Admin\Permission;
use Illuminate\Http\Request;

class IdeaCommentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeExtension();

        $query = IdeaComment::withTrashed()->with(['post', 'customer', 'admin']);
        if ($request->filled('q')) {
            $query->where('content', 'LIKE', '%'.$request->get('q').'%');
        }
        if ($request->get('status') === 'deleted') {
            $query->onlyTrashed();
        }

        return view('idea_admin::comments.index', [
            'items' => $query->latest()->paginate(25)->appends($request->query()),
        ]);
    }

    public function destroy(IdeaComment $comment)
    {
        $this->authorizeExtension();
        $comment->deleted_by_admin_id = auth('admin')->id();
        $comment->save();
        $comment->delete();

        return back()->with('success', __('admin.flash.deleted'));
    }

    public function restore(int $comment)
    {
        $this->authorizeExtension();
        $comment = IdeaComment::withTrashed()->findOrFail($comment);
        $comment->deleted_by_admin_id = null;
        $comment->restore();

        return back()->with('success', __('admin.flash.updated'));
    }

    protected function authorizeExtension(): void
    {
        abort_unless(staff_has_permission(Permission::MANAGE_EXTENSIONS), 403);
    }
}
