<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\GroupPost;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // GET /api/posts — posts visible to the authenticated user
    public function index(Request $request)
    {
        $user   = $request->user();
        $userId = $user->id;

        // Group IDs this user belongs to
        $userGroupIds = $user->groups()->pluck('groups.id');

        $posts = GroupPost::where(function ($q) use ($userGroupIds) {
                // audience = group  AND  this user is in that group
                $q->where('audience', 'group')
                  ->whereIn('group_id', $userGroupIds);
            })
            ->orWhere(function ($q) use ($userGroupIds) {
                // audience = friends  AND  post's group is one of user's groups
                $q->where('audience', 'friends')
                  ->whereIn('group_id', $userGroupIds);
            })
            ->with(['user', 'group'])
            ->withCount(['likes', 'comments'])
            ->orderByDesc('created_at')
            ->get();

        // Attach liked_by_me flag
        $myLikedIds = PostLike::where('user_id', $userId)
            ->whereIn('post_id', $posts->pluck('id'))
            ->pluck('post_id')
            ->toArray();

        return response()->json([
            'posts' => $posts->map(fn ($p) => $this->serialize($p, $userId, $myLikedIds)),
        ]);
    }

    // POST /api/posts — create a post (group owner only)
    public function store(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer|exists:groups,id',
            'caption'  => 'nullable|string|max:500',
            'audience' => 'required|in:group,friends',
            'image'    => 'nullable|image|max:5120',
        ]);

        $user  = $request->user();
        $group = Group::findOrFail($request->group_id);

        if (!$group->members()->where('users.id', $user->id)->exists()) {
            return response()->json(['message' => 'You must be a member of this group to share a post.'], 403);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $file      = $request->file('image');
                $uploadDir = public_path('assets/uploads/posts');
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $filename  = 'post_' . now()->timestamp . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                $file->move($uploadDir, $filename);
                $imagePath = 'assets/uploads/posts/' . $filename;
            } catch (\Exception $e) {
                // Image storage failed — continue without image
                $imagePath = null;
            }
        }

        $post = GroupPost::create([
            'user_id'    => $user->id,
            'group_id'   => $request->group_id,
            'image_path' => $imagePath,
            'caption'    => $request->caption,
            'audience'   => $request->audience,
        ]);

        $post->loadCount(['likes', 'comments']);
        $post->load(['user', 'group']);

        return response()->json(['post' => $this->serialize($post, $user->id, [])], 201);
    }

    // POST /api/posts/{post}/like — toggle like
    public function toggleLike(Request $request, GroupPost $post)
    {
        $user     = $request->user();
        $existing = PostLike::where('post_id', $post->id)
                            ->where('user_id', $user->id)
                            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            PostLike::create(['post_id' => $post->id, 'user_id' => $user->id]);
            $liked = true;

            if ($post->user_id !== $user->id) {
                ActivityLog::create([
                    'user_id'  => $post->user_id,
                    'group_id' => $post->group_id,
                    'type'     => 'post_like',
                    'message'  => "{$user->name} liked your post",
                    'is_read'  => false,
                ]);
            }
        }

        return response()->json([
            'liked'       => $liked,
            'likes_count' => PostLike::where('post_id', $post->id)->count(),
        ]);
    }

    // GET /api/posts/{post}/comments
    public function comments(GroupPost $post)
    {
        $comments = $post->comments()->with('user')->orderBy('created_at')->get();
        return response()->json([
            'comments' => $comments->map(fn ($c) => [
                'id'         => $c->id,
                'comment'    => $c->comment,
                'user_name'  => $c->user->name,
                'user_photo' => $c->user->profile_photo_url,
                'created_at' => optional($c->created_at)->toDateTimeString(),
            ]),
        ]);
    }

    // POST /api/posts/{post}/comments
    public function addComment(Request $request, GroupPost $post)
    {
        $request->validate(['comment' => 'required|string|max:300']);
        $user = $request->user();

        $comment = PostComment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'comment' => $request->comment,
        ]);

        if ($post->user_id !== $user->id) {
            ActivityLog::create([
                'user_id'  => $post->user_id,
                'group_id' => $post->group_id,
                'type'     => 'post_comment',
                'message'  => "{$user->name} commented: \"{$request->comment}\"",
                'is_read'  => false,
            ]);
        }

        return response()->json([
            'comment' => [
                'id'         => $comment->id,
                'comment'    => $comment->comment,
                'user_name'  => $user->name,
                'user_photo' => $user->profile_photo_url,
                'created_at' => optional($comment->created_at)->toDateTimeString(),
            ],
        ], 201);
    }

    // DELETE /api/posts/{post}
    public function destroy(Request $request, GroupPost $post)
    {
        if ($post->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        if ($post->image_path) {
            $fullPath = public_path($post->image_path);
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }

        $post->delete();
        return response()->json(['message' => 'Post deleted.']);
    }

    // Serialize a post for API response
    private function serialize(GroupPost $post, int $currentUserId, array $myLikedIds): array
    {
        return [
            'id'             => $post->id,
            'user_id'        => $post->user_id,
            'user_name'      => $post->user->name ?? 'User',
            'user_photo'     => $post->user->profile_photo_url ?? null,
            'group_id'       => $post->group_id,
            'group_name'     => $post->group->name ?? '',
            'image_url'      => $post->image_path ? url($post->image_path) : null,
            'caption'        => $post->caption,
            'audience'       => $post->audience,
            'likes_count'    => $post->likes_count ?? 0,
            'comments_count' => $post->comments_count ?? 0,
            'liked_by_me'    => in_array($post->id, $myLikedIds),
            'is_own'         => $post->user_id === $currentUserId,
            'created_at'     => optional($post->created_at)->toDateTimeString(),
        ];
    }
}
