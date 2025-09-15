<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function addPost(Request $request)
    {
        $request->validate([
            'post_details' => 'required|string',
            'img'       => 'nullable',
            'lat'         => 'nullable',
            'lng'         => 'nullable',
            'category_id' => 'nullable|integer',
            'title'        => 'required|string'
        ]);

        $imagePath = null;
        // Case 1: Image is a file
        if ($request->hasFile('img') && $request->file('img')->isValid()) {
            $imagePath = $request->file('img')->store('posts', 'public');
        }
        // Case 2: Image is a URL
        elseif ($request->filled('img') && filter_var($request->image, FILTER_VALIDATE_URL)) {
            $imagePath = $request->image;
        }
        $post = Post::create([
            'user_id'     => $request->user()->id,
            'post_details' => $request->post_details,
            'img'       => $imagePath ?? '',
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'title'        => $request->title,
            'category_id' => $request->category_id
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Post added successfully',
            'post' => $post
        ], 201);
    }
    /**
     * Repost a post under the current user
     */
    public function rePost(Request $request)
    {
        $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'img'       => 'nullable',
            'lat'         => 'nullable',
            'lng'         => 'nullable',
            'category_id' => 'nullable|integer',
            'title'        => 'required|string'

        ]);

        $original = Post::find($request->post_id);

        if (!$original) {
            return response()->json([
                'status' => 404,
                'message' => 'Original post not found'
            ], 404);
        }
        $newPost = Post::create([
            'user_id'        => $request->user()->id,
            'parent_post_id' => $original->id,
            'title'        => $request->title,
            'post_detail'    => $original->post_detail,
            'img'          => $original->img ?? '',
            'lat'            => $original->lat,
            'lng'            => $original->lng,
            'category_id'    => $original->category_id
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Post re-posted successfully',
            'post' => $newPost
        ], 201);
    }

    /**
     *  Get all posts of the current user
     */

    public function myAllPosts(Request $request)
    {
        $user = $request->user();

        $posts = Post::with([
            'user:id,name,email',
            'comments.user:id,name,email'

        ])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'category' => $post->category->category_name ?? '',
                    'title' => $post->title ?? '',
                    'post_details' => $post->post_details ?? '',
                    'user_id' => $post->user->id,
                    'username' => $post->user->name,
                    'user_email' => $post->user->email,
                    'img' => $post->img ?? '',
                    'lat' => $post->lat    ?? '',
                    'lng' => $post->lng ?? '',
                    'status' => $post->status == 1 ? 'Active' : 'Inactive' ?? '',
                    'parent_post_id' => $post->parent_post_id ?? '',
                    'comments' => $post->comments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'user_id' => $comment->user->id,
                            'username' => $comment->user->name,
                            'user_email' => $comment->user->email,
                            'comment' => $comment->comment == 0 ? 'like' : 'dislike'
                        ];
                    })
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Posts fetched successfully',
            'data' => $posts
        ], 200);
    }


    /**
     * Get all posts liked by the current user
     */
    // public function likedPosts(Request $request)
    // {
    //     $user = $request->user();

    //     $likedPosts = Post::whereHas('likes', function ($query) use ($user) {
    //         $query->where('user_id', $user->id)->where('comment', 0);
    //     })->get();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Liked posts retrieved successfully',
    //         'data' => $likedPosts
    //     ], 200);
    // }

    /**
     * Get all posts liked by the current user
     */
    public function likedPosts(Request $request)
    {
        $user = $request->user();

        $likedPosts = Post::with('user') // eager load the post's owner
            ->whereHas('likes', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('comment', 0); // 0 = like
            })
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'category' => $post->category->category_name ?? '',
                    'title' => $post->title ?? '',
                    'post_details' => $post->post_details ?? '',
                    'user_id' => $post->user->id,
                    'username' => $post->user->name,
                    'user_email' => $post->user->email,
                    'img' => $post->img ?? '',
                    'lat' => $post->lat    ?? '',
                    'lng' => $post->lng ?? '',
                    'status' => $post->status == 1 ? 'Active' : 'Inactive' ?? '',
                    'parent_post_id' => $post->parent_post_id ?? '',
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Liked posts retrieved successfully',
            'data' => $likedPosts
        ], 200);
    }


    /**
     * Get all posts Diskliked by the current user
     */
    public function disklikedPosts(Request $request)
    {
        $user = $request->user();

        $likedPosts = Post::whereHas('likes', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('comment', 1);
        })->get();

        return response()->json([
            'status' => true,
            'message' => 'Liked posts retrieved successfully',
            'data' => $likedPosts
        ], 200);
    }
    /**
     * Current User Nearby all Post details with custom radius changes
     */
    // public function getNearbyPosts(Request $request)
    // {
    //     $lat = $request->input('lat');
    //     $lng = $request->input('lng');
    //     $radius = $request->input('radius', 2);

    //     if (!$lat || !$lng) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'lat and lng are required'
    //         ], 400);
    //     }

    //     $posts = Post::select('*')->selectRaw("
    //             ( 6371 * acos( cos( radians(?) ) *
    //             cos( radians(lat) ) *
    //             cos( radians(lng) - radians(?) ) +
    //             sin( radians(?) ) *
    //             sin( radians(lat) ) )
    //             ) AS distance
    //         ", [$lat, $lng, $lat])
    //         ->having("distance", "<=", $radius)
    //         ->orderBy("distance", "asc")
    //         ->get();

    //     return response()->json([
    //         'status' => 'success',
    //         'radius_km' => $radius,
    //         'posts' => $posts
    //     ]);
    // }

    public function getNearbyPosts(Request $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 2);

        if (!$lat || !$lng) {
            return response()->json([
                'status' => 'error',
                'message' => 'lat and lng are required'
            ], 400);
        }

        $posts = Post::with('user')
            ->select('*')
            ->selectRaw("
            ( 6371 * acos( cos( radians(?) ) *
            cos( radians(lat) ) *
            cos( radians(lng) - radians(?) ) +
            sin( radians(?) ) *
            sin( radians(lat) ) )
            ) AS distance
        ", [$lat, $lng, $lat])
            ->having("distance", "<=", $radius)
            ->orderBy("distance", "asc")
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'category' => $post->category->category_name ?? '',
                    'title' => $post->title ?? '',
                    'post_details' => $post->post_details ?? '',
                    'user_id' => $post->user->id ?? '',
                    'username' => $post->user->name ?? '',
                    'user_email' => $post->user->email ?? '',
                    'img' => $post->img ?? '',
                    'lat' => $post->lat    ?? '',
                    'lng' => $post->lng ?? '',
                    'status' => $post->status == 1 ? 'Active' : 'Inactive' ?? '',
                    'parent_post_id' => $post->parent_post_id ?? '',
                    'distance_km' => round($post->distance, 2),
                    'comments' => $post->comments->map(function ($comment) {
                        return [
                            'id' => $comment->id,
                            'user_id' => $comment->user->id,
                            'username' => $comment->user->name,
                            'user_email' => $comment->user->email,
                            'comment' => $comment->comment == 0 ? 'like' : 'dislike'
                        ];
                    }),

                ];
            });

        return response()->json([
            'status' => 'success',
            'radius_km' => $radius,
            'posts' => $posts
        ]);
    }

    /**
     * Post details by Ids
     */

    // public function postDetails(Request $request)
    // {


    //     $post = Post::find($request->post_id);

    //     if (!$post) {
    //         return response()->json([
    //             'status' => 404,
    //             'message' => 'Post not found'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Post details fetched successfully',
    //         'data' => $post
    //     ], 200);
    // }

    public function postDetails(Request $request)
    {
        $post = Post::with(['user', 'comments.user'])
            ->find($request->post_id);

        if (!$post) {
            return response()->json([
                'status' => 404,
                'message' => 'Post not found'
            ], 404);
        }

        $postData = [
            'id'          => $post->id,
            'category' => $post->category->category_name ?? '',
            'title' => $post->title ?? '',
            'post_details' => $post->post_details ?? '',
            'user_id' => $post->user->id ?? '',
            'username'    => $post->user->name ?? null,
            'user_email' => $post->user->email ?? '',
            'lat'         => $post->lat,
            'lng'         => $post->lng,
            'img' => $post->img ?? '',
            'created_at'  => $post->created_at,
            'comments'    => $post->comments->map(function ($comment) {
                return [
                    'id'       => $comment->id,
                    'user_id' => $comment->user->id ?? null,
                    'username' => $comment->user->name ?? null,
                    'user_email' => $comment->user->email ?? null,
                    'comment' => $comment->comment == 0 ? 'like' : 'dislike',
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,

                ];
            })
        ];

        return response()->json([
            'status' => 200,
            'message' => 'Post details fetched successfully',
            'data' => $postData
        ], 200);
    }

    /**
     * Search Category
     */
public function searchPosts(Request $request)
{
    $request->validate([
        'keyword' => 'required|string',
        'category_id' => 'nullable|integer|exists:categories,id'
    ]);

    $query = Post::query();

    // Filter by keyword (title or post_details)
    if ($request->filled('keyword')) {
        $keyword = $request->keyword;
        $query->where(function ($q) use ($keyword) {
            $q->where('title', 'LIKE', "%{$keyword}%")
              ->orWhere('post_details', 'LIKE', "%{$keyword}%");
        });
    }

    // Filter by category_id if provided
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $posts = $query->latest()->get();

    return response()->json([
        'status' => true,
        'message' => 'Posts fetched successfully',
        'data' => $posts
    ], 200);
}

}
