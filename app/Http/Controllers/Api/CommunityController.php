<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityComment;
use App\Models\CommunityPostLike;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    public function __construct(private GamificationService $gamification) {}

    public function posts(Request $request)
    {
        $query = CommunityPost::with('user:id_user,username,full_name,avatar_url')
            ->where('is_hidden', false);

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $query->orderByDesc('is_pinned')->orderByDesc('created_at');

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    public function postDetail($id)
    {
        $post = CommunityPost::with([
            'user:id_user,username,full_name,avatar_url',
            'comments' => fn($q) => $q->with('user:id_user,username,full_name,avatar_url', 'replies.user:id_user,username,full_name,avatar_url')
                ->whereNull('parent_id')->where('is_hidden', false)->latest(),
        ])->findOrFail($id);

        $post->is_liked = CommunityPostLike::where('post_id', $id)
            ->where('user_id', Auth::id())->exists();

        return response()->json(['success' => true, 'data' => $post]);
    }

    public function createPost(Request $request)
    {
        $request->validate([
            'content'  => 'required|string|max:5000',
            'category' => 'required|in:resep,diskusi,tips,progress,tanya',
        ]);

        // Word filter sederhana
        $badWords = ['bangsat', 'anjing', 'babi', 'kontol', 'memek'];
        $content = $request->content;
        foreach ($badWords as $word) {
            $content = str_ireplace($word, str_repeat('*', strlen($word)), $content);
        }

        $post = CommunityPost::create([
            'user_id'    => Auth::id(),
            'title'      => $request->title,
            'content'    => $content,
            'image_path' => $request->hasFile('image')
                ? $request->file('image')->store('community_posts', 'public') : null,
            'category'   => $request->category,
            'hashtags'   => $request->hashtags,
        ]);

        $this->gamification->addPoints(Auth::id(), 10, 'Membuat postingan baru', 'post', $post->id);

        return response()->json(['success' => true, 'message' => 'Postingan berhasil dibuat', 'data' => $post]);
    }

    public function toggleLike($postId)
    {
        $existing = CommunityPostLike::where('post_id', $postId)
            ->where('user_id', Auth::id())->first();

        if ($existing) {
            $existing->delete();
            CommunityPost::where('id', $postId)->decrement('likes_count');
            return response()->json(['success' => true, 'message' => 'Like dihapus', 'data' => ['liked' => false]]);
        }

        CommunityPostLike::create(['post_id' => $postId, 'user_id' => Auth::id()]);
        CommunityPost::where('id', $postId)->increment('likes_count');

        $this->gamification->addPoints(Auth::id(), 2, 'Menyukai postingan', 'like', $postId);

        return response()->json(['success' => true, 'message' => 'Liked!', 'data' => ['liked' => true]]);
    }

    public function addComment(Request $request, $postId)
    {
        $request->validate([
            'content'   => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:community_comments,id',
        ]);

        $comment = CommunityComment::create([
            'post_id'   => $postId,
            'user_id'   => Auth::id(),
            'parent_id' => $request->parent_id,
            'content'   => $request->content,
        ]);

        CommunityPost::where('id', $postId)->increment('comments_count');
        $this->gamification->addPoints(Auth::id(), 5, 'Mengomentari postingan', 'comment', $comment->id);

        return response()->json(['success' => true, 'data' => $comment->load('user:id_user,username,full_name,avatar_url')]);
    }

    public function reportPost(Request $request, $postId)
    {
        $request->validate([
            'reason' => 'required|in:spam,sara,hoax,pornografi,lainnya',
        ]);

        DB::table('community_post_reports')->updateOrInsert(
            ['post_id' => $postId, 'user_id' => Auth::id()],
            [
                'reason'      => $request->reason,
                'description' => $request->description,
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Laporan dikirim ke admin']);
    }

    public function leaderboard()
    {
        $data = $this->gamification->getLeaderboard(20);
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function myStats()
    {
        $stats = $this->gamification->getUserStats(Auth::id());
        return response()->json(['success' => true, 'data' => $stats]);
    }
}
