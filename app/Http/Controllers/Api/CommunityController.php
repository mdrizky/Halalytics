<?php

namespace App\Http\Controllers\Api;

use App\Helpers\WordFilterHelper;
use App\Http\Controllers\Controller;
use App\Models\CommunityComment;
use App\Models\CommunityPost;
use App\Models\CommunityPostLike;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CommunityController extends Controller
{
    public function __construct(private readonly GamificationService $gamification)
    {
    }

    public function index(Request $request)
    {
        $viewerId = (int) $request->user()->id_user;

        $posts = CommunityPost::query()
            ->with('user:id_user,username,full_name,image,avatar_url')
            ->where('is_hidden', false)
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->input('category')))
            ->when(
                $request->input('sort') === 'popular',
                fn ($query) => $query->orderByDesc('likes_count')->orderByDesc('created_at'),
                fn ($query) => $query->orderByDesc('is_pinned')->orderByDesc('created_at')
            )
            ->paginate(15);

        $payload = $posts->getCollection()
            ->map(fn (CommunityPost $post) => $this->postPayload($post, $viewerId))
            ->values();

        return $this->successResponse(
            $payload,
            'Feed komunitas berhasil diambil.',
            200,
            [
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                ],
            ]
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string|max:5000',
            'category' => 'required|in:resep,diskusi,tips,progress,tanya',
            'image' => 'nullable|image|max:5120',
        ]);

        $content = WordFilterHelper::filter($validated['content']);
        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('community_posts', 'public')
            : null;

        $post = CommunityPost::create([
            'user_id' => $request->user()->id_user,
            'title' => $validated['title'] ?? null,
            'content' => $content,
            'image_path' => $imagePath,
            'category' => $validated['category'],
            'hashtags' => $this->extractHashtags($content),
        ]);

        $this->gamification->addPoints($request->user()->id_user, 10, 'Membuat postingan baru', 'post', $post->id);

        return $this->successResponse(
            $this->postPayload($post->fresh('user'), (int) $request->user()->id_user),
            'Postingan berhasil dibuat.',
            201
        );
    }

    public function show(Request $request, $id)
    {
        $viewerId = (int) $request->user()->id_user;

        $post = CommunityPost::query()
            ->with([
                'user:id_user,username,full_name,image,avatar_url',
                'comments' => fn ($query) => $query
                    ->whereNull('parent_id')
                    ->where('is_hidden', false)
                    ->with([
                        'user:id_user,username,full_name,image,avatar_url',
                        'replies' => fn ($replies) => $replies
                            ->where('is_hidden', false)
                            ->with('user:id_user,username,full_name,image,avatar_url')
                            ->latest(),
                    ])
                    ->latest(),
            ])
            ->findOrFail($id);

        $payload = $this->postPayload($post, $viewerId);
        $payload['comments'] = $post->comments
            ->map(fn (CommunityComment $comment) => $this->commentPayload($comment))
            ->values()
            ->all();

        return $this->successResponse($payload, 'Detail postingan berhasil diambil.');
    }

    public function likePost(Request $request, $id)
    {
        $post = CommunityPost::findOrFail($id);
        $userId = (int) $request->user()->id_user;

        $existing = CommunityPostLike::query()
            ->where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        $liked = false;

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');
        } else {
            CommunityPostLike::create([
                'post_id' => $post->id,
                'user_id' => $userId,
            ]);
            $post->increment('likes_count');
            $liked = true;

            if ((int) $post->user_id !== $userId) {
                $this->gamification->addPoints($post->user_id, 2, 'Postingan mendapat like', 'post_like', $post->id);
            }
        }

        return $this->successResponse([
            'liked' => $liked,
            'likes_count' => (int) $post->fresh()->likes_count,
        ], 'Status like postingan berhasil diperbarui.');
    }

    public function comment(Request $request, $postId)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:community_comments,id',
        ]);

        $post = CommunityPost::findOrFail($postId);
        $comment = CommunityComment::create([
            'post_id' => $post->id,
            'user_id' => $request->user()->id_user,
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => WordFilterHelper::filter($validated['content']),
        ]);

        $post->increment('comments_count');
        $this->gamification->addPoints($request->user()->id_user, 5, 'Menambahkan komentar', 'comment', $comment->id);

        if ((int) $post->user_id !== (int) $request->user()->id_user) {
            logger()->info('FCM notification placeholder for community comment', [
                'post_id' => $post->id,
                'owner_id' => $post->user_id,
                'comment_id' => $comment->id,
            ]);
        }

        return $this->successResponse(
            $this->commentPayload($comment->fresh('user')),
            'Komentar berhasil ditambahkan.',
            201
        );
    }

    public function reportPost(Request $request, $id)
    {
        $validated = $request->validate([
            'reason' => 'required|in:spam,sara,hoax,pornografi,lainnya',
            'description' => 'nullable|string|max:500',
        ]);

        DB::table('community_post_reports')->updateOrInsert(
            [
                'post_id' => $id,
                'user_id' => $request->user()->id_user,
            ],
            [
                'reason' => $validated['reason'],
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return $this->successResponse(null, 'Laporan post berhasil dikirim.');
    }

    public function userPosts(Request $request, $userId)
    {
        $viewerId = (int) $request->user()->id_user;

        $posts = CommunityPost::query()
            ->with('user:id_user,username,full_name,image,avatar_url')
            ->where('user_id', $userId)
            ->where('is_hidden', false)
            ->latest()
            ->get()
            ->map(fn (CommunityPost $post) => $this->postPayload($post, $viewerId));

        return $this->successResponse($posts, 'Postingan user berhasil diambil.');
    }

    public function leaderboard()
    {
        $leaders = DB::table('community_user_points')
            ->join('users', 'users.id_user', '=', 'community_user_points.user_id')
            ->select(
                'users.id_user',
                'users.username',
                'users.full_name',
                'users.image',
                'users.avatar_url',
                'community_user_points.total_points',
                'community_user_points.level'
            )
            ->orderByDesc('community_user_points.total_points')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $badges = DB::table('user_badges')
                    ->join('badges', 'badges.id', '=', 'user_badges.badge_id')
                    ->where('user_badges.user_id', $row->id_user)
                    ->select('badges.id', 'badges.name', 'badges.description', 'badges.icon_path', 'user_badges.earned_at')
                    ->orderByDesc('user_badges.earned_at')
                    ->get()
                    ->map(fn ($badge) => [
                        'id' => $badge->id,
                        'name' => $badge->name,
                        'description' => $badge->description,
                        'icon_url' => $this->assetUrl($badge->icon_path),
                        'earned_at' => isset($badge->earned_at) ? (string) $badge->earned_at : null,
                    ])
                    ->values()
                    ->all();

                return [
                    'user_id' => (int) $row->id_user,
                    'user_name' => $row->full_name ?: $row->username,
                    'user_photo' => $row->avatar_url ?: $row->image,
                    'total_points' => (int) $row->total_points,
                    'level' => $row->level,
                    'badges' => $badges,
                ];
            })
            ->values();

        return $this->successResponse($leaders, 'Leaderboard komunitas berhasil diambil.');
    }

    public function myStats(Request $request)
    {
        $stats = $this->gamification->getUserStats($request->user()->id_user);

        $badges = collect($stats['badges'] ?? [])
            ->map(fn ($badge) => [
                'name' => $badge->name ?? null,
                'description' => $badge->description ?? null,
                'icon_url' => $this->assetUrl($badge->icon_path ?? null),
                'earned_at' => isset($badge->earned_at) ? (string) $badge->earned_at : null,
            ])
            ->values()
            ->all();

        return $this->successResponse([
            'total_points' => (int) ($stats['total_points'] ?? 0),
            'level' => $stats['level'] ?? 'Pemula',
            'badges' => $badges,
        ], 'Statistik komunitas user berhasil diambil.');
    }

    public function posts(Request $request)
    {
        return $this->index($request);
    }

    public function postDetail(Request $request, $id)
    {
        return $this->show($request, $id);
    }

    public function createPost(Request $request)
    {
        return $this->store($request);
    }

    public function toggleLike(Request $request, $id)
    {
        return $this->likePost($request, $id);
    }

    public function addComment(Request $request, $id)
    {
        return $this->comment($request, $id);
    }

    private function postPayload(CommunityPost $post, int $viewerId): array
    {
        $userLevel = DB::table('community_user_points')
            ->where('user_id', $post->user_id)
            ->value('level') ?? 'Pemula';

        $userBadge = DB::table('user_badges')
            ->join('badges', 'badges.id', '=', 'user_badges.badge_id')
            ->where('user_badges.user_id', $post->user_id)
            ->orderByDesc('user_badges.earned_at')
            ->value('badges.name');

        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'user_name' => $post->user?->full_name ?? $post->user?->username ?? 'Pengguna',
            'user_photo' => $post->user?->avatar_url ?? $post->user?->image,
            'user_badge' => $userBadge,
            'user_level' => $userLevel,
            'title' => $post->title,
            'content' => $post->content,
            'image_url' => $this->assetUrl($post->image_path),
            'category' => $post->category,
            'hashtags' => $post->hashtags ?? [],
            'likes_count' => (int) $post->likes_count,
            'comments_count' => (int) $post->comments_count,
            'is_pinned' => (bool) $post->is_pinned,
            'is_liked_by_me' => CommunityPostLike::query()
                ->where('post_id', $post->id)
                ->where('user_id', $viewerId)
                ->exists(),
            'created_at' => optional($post->created_at)->toISOString(),
        ];
    }

    private function commentPayload(CommunityComment $comment): array
    {
        return [
            'id' => $comment->id,
            'post_id' => $comment->post_id,
            'user_id' => $comment->user_id,
            'user_name' => $comment->user?->full_name ?? $comment->user?->username ?? 'Pengguna',
            'user_photo' => $comment->user?->avatar_url ?? $comment->user?->image,
            'parent_id' => $comment->parent_id,
            'content' => $comment->content,
            'likes_count' => (int) $comment->likes_count,
            'replies' => $comment->relationLoaded('replies')
                ? $comment->replies->map(fn (CommunityComment $reply) => $this->commentPayload($reply))->values()->all()
                : [],
            'created_at' => optional($comment->created_at)->toISOString(),
        ];
    }

    private function extractHashtags(string $content): array
    {
        preg_match_all('/#([\pL\pN_]+)/u', $content, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    private function assetUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
