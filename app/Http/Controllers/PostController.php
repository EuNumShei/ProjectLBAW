<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Share;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;
use App\Models\Profile;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        return response()->json($posts);
    }


    public function edit($id)
    {
        $post = Post::findOrFail($id);

        try {
            $this->authorize('update', $post);
        } catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to edit this post.');
        }


        return view('pages.postedit', compact('post'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to create a post.');
        }
    
        try {
            $this->authorize('create', Post::class);
        } catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to create a post.');
        }
    
        $dados = $request->validate([
            'content' => 'required|string|max:255',
            'image_url' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:5048',
            'group_id' => 'nullable|exists:group,id',
        ]);
    
        $post = new Post();
        $post->author_id = Auth::id();
        $post->content = $dados['content'];
    
        if ($request->hasFile('image_url')) {
            $file = $request->file('image_url');
            $path = $file->store('images', 'public');
            $post->image_url = $path;
            $request->image_url->move(public_path('images'), $path);
        }
    
        if (isset($dados['group_id'])) {
            $post->group_id = $dados['group_id'];
        }
    
        $post->save();
    
        return redirect()->back()->with('success', 'Post created successfully!');
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        try {
            $this->authorize('update', $post);
        } catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to edit this post.');
        }

        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        //if ($request->hasFile('image')) {
        //    $imagePath = $request->file('image')->store('images', 'public');
        //    $post->image_url = '/storage/' . $imagePath;
        //}
    
        $post->content = $validatedData['content'];
        $post->save();
    
        return redirect()->route('post', ['id' => $post->id])->with('success', 'Post updated successfully!');
    }

    public function show($id)
    {
        $post = Post::with('comments.author')->findOrFail($id);
    
        $profile2 = $post->author;
        
        try {
            $this->authorize('interact', $profile2);
            $this->authorize('isInGroup', $post);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', 'You are not authorized to perform this action.');
        }
    
        return view('pages.post', compact('post'));
    }

    public function home()
    {
        $posts = Post::orderBy('id', 'desc')->get();
        return view('pages.home', compact('posts'));
    }
    
    public function friendsAndGroupsPosts(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    
        $offset = $request->input('offset', 0);
        $user = Auth::user()->profile;
    
        $friendsIds = $user->friends()->pluck('friend_id')->toArray();
        $friendsOfIds = $user->friendsOf()->pluck('user_id')->toArray();
        $allFriendsIds = array_unique(array_merge($friendsIds, $friendsOfIds));
        
        $groupMembersIds = $user->groups()->with('members')->get()->pluck('members.*.id')->flatten()->unique()->toArray();
        $groupAuthorIds = $user->groups()->pluck('author_id')->toArray();
        $allGroupIds = array_merge($groupMembersIds, $groupAuthorIds);
    
        $friendsAndGroupsUserIds = array_unique(array_merge($allFriendsIds, $allGroupIds));
    
        //Log::info('Friends IDs: ' . implode(', ', $friendsIds));
        //Log::info('Group Members IDs: ' . implode(', ', $groupMembersIds));
        //Log::info('Friends and Groups User IDs: ' . implode(', ', $friendsAndGroupsUserIds));
    
        $notBlockedCond = function ($query) use ($user) {
            $query->whereDoesntHave('blockedUsers', function ($query) use ($user) {
                $query->where('blocked_user_id', $user->id);
            })->whereDoesntHave('blockedBy', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });
        };
    
        $shares = Share::whereIn('author_id', $friendsAndGroupsUserIds)
            ->whereHas('author', $notBlockedCond)
            ->whereHas('post.author', $notBlockedCond)
            ->with('author', 'post.author')
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take(30)
            ->get();
    
        Log::info('Shares count: ' . $shares->count());
    
        $posts = Post::whereIn('author_id', $friendsAndGroupsUserIds)
            ->whereHas('author', $notBlockedCond)
            ->orderBy('created_at', 'desc')
            ->with('author')
            ->skip($offset)
            ->take(30)
            ->get();
    
        Log::info('Posts count: ' . $posts->count());
    
        foreach ($shares as $share) {
            $sharedPost = $share->post;
            $sharedPost->shareAuthor = $share->author;
            $sharedPost->type = 'share';
            $sharedPost->author = $share->post->author;
            $sharedPost->created_at = $share->created_at;
            $sharedPost->share_id = $share->id;
            $posts->push((object) $sharedPost);
        }
    
        $items = $posts->merge($shares)->sortByDesc('created_at');
        $sorted = collect($posts)->sortByDesc('created_at');
    
        if ($offset > 0) {
            return view('partials.feed', ['items' => $sorted]);
        }
    
        return view('pages.home', ['items' => $sorted]);
    }
    
    public function publicPosts(Request $request)
    {
        $offset = $request->input('offset', 0);

        if (Auth::check()) {
            $blockedUsersIds = Auth::user()->profile->blockedUsers()->pluck('blocked_user_id')->toArray();
            $usersBlockedByIds = Auth::user()->profile->blockedBy()->pluck('user_id')->toArray();
            $allBlockedIds = array_merge($blockedUsersIds, $usersBlockedByIds);
    
            $posts = Post::whereNotIn('author_id', $allBlockedIds)
            ->whereNull('group_id')
            ->whereHas('author', function ($query) {
                $query->where('is_public', true);
            })
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take(30)
            ->get();
    
            $shares = Share::whereHas('author', function ($query) use ($allBlockedIds) {
                $query->whereNotIn('id', $allBlockedIds)
                      ->where('is_public', true);
            })->whereHas('post.author', function ($query) use ($allBlockedIds) {
                $query->whereNotIn('id', $allBlockedIds)
                      ->where('is_public', true);
            })->whereHas('post', function ($query) {
                $query->whereNull('group_id');
            })->orderBy('created_at', 'desc')->skip($offset)->take(30)->get();

            //Log::info($shares);

        } else {
            $posts = Post::whereNull('group_id')
            ->whereHas('author', function ($query) {
                $query->where('is_public', true);
            })
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take(30)
            ->get();
    
            $shares = Share::whereHas('post.author', function ($query) {
                $query->where('is_public', true);
            })->whereHas('post', function ($query) {
                $query->whereNull('group_id');
            })->orderBy('created_at', 'desc')->skip($offset)->take(30)->get();
        }
    
        foreach ($shares as $share) {
            $sharedPost = $share->post;
            if ($sharedPost->group_id) {
                continue;
            }
            $sharedPost->shareAuthor = $share->author;
            $sharedPost->type = 'share';
            $sharedPost->author = $share->post->author;
            $sharedPost->created_at = $share->created_at;
            $sharedPost->share_id = $share->id;
            $posts->push((object) $sharedPost);
        }
    
        /*foreach ($posts as $post) {
            Log::info($post->created_at);
        }*/

        $sorted = collect($posts)->sortByDesc('created_at')->take(30);


        if($offset > 0) {
            return view('partials.feed', ['items' => $sorted]);
        }

        return view('pages.home', ['items' => $sorted]);
    }



    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        try {
            $this->authorize('delete', $post);
        } catch (AuthorizationException $e) {
            return redirect()->route('home')->with('error', 'You are not authorized to delete this post.');
        }

        $post->delete();
        return redirect()->route('home')->with('success', 'Post deleted successfully!');
    }
}