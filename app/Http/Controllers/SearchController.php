<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\GroupInviteNotification;

class SearchController extends Controller
{
    public function results(Request $request)
    {
        $query = strtolower($request->input('query'));
        $group = $request->input('group', 'users');

        $offset = $request->input('offset', 0);
        $limit = 15;

        $results = [];

        $tsquery = $query ? implode(' & ', array_map(function($word) {
            return "$word:*";
        }, explode(' ', $query))) : '';

        if ($group === 'users') {
            Log::info("Searching for users with query: $query");
            $results = User::join('profile', 'users.id', '=', 'profile.id')
                ->whereNotIn('users.id', [1, 2])
                ->where(function($q) use ($query, $tsquery) {
                    $q->whereRaw("profile.tsvectors @@ to_tsquery('english', ?)", [$tsquery])
                      ->orWhereRaw("users.name ILIKE ?", ['%' . $query . '%'])
                      ->orWhereRaw("? ILIKE '%' || users.name || '%'", [$query])
                      ->orWhereRaw("to_tsvector('english', users.name || ' ' || profile.full_name || ' ' || COALESCE(profile.bio, '')) ::text ILIKE ?", ['%' . $query . '%'])
                      ->orWhereRaw("? ILIKE '%' || to_tsvector('english', users.name || ' ' || profile.full_name || ' ' || COALESCE(profile.bio, '')) ::text || '%'", [$query]);
                })
                ->select('users.*', 'profile.full_name as profile_full_name', 'profile.profile_pic as profile_profile_pic', 'profile.bio as profile_bio')
                ->orderByRaw("ts_rank(profile.tsvectors, to_tsquery('english', ?)) DESC NULLS LAST", [$tsquery])
                ->get();
            Log::info("Found " . count($results) . " users");
        } elseif ($group === 'groups') {
            Log::info("Searching for groups with query: $query");
            $results = Group::select('id', 'name', 'description', 'author_id', 'created_at')
                ->where(function($q) use ($query, $tsquery) {
                    $q->whereRaw("tsvectors @@ to_tsquery(?)", [$tsquery])
                      ->orWhereRaw("name ILIKE ?", ['%' . $query . '%'])
                      ->orWhereRaw("? ILIKE '%' || name || '%'", [$query])
                      ->orWhereRaw("tsvectors::text ILIKE ?", ['%' . $query . '%'])
                      ->orWhereRaw("? ILIKE '%' || tsvectors::text || '%'", [$query]);
                })
                ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) DESC NULLS LAST", [$tsquery])
                ->get();
            Log::info("Found " . count($results) . " groups");
        } elseif ($group === 'posts') {
            Log::info("Searching for posts with query: $query");
            $results = Post::select('id', 'author_id', 'group_id', 'content', 'image_url', 'created_at', 'updated_at')
                ->where(function($q) use ($query, $tsquery) {
                    $q->whereRaw("tsvectors @@ to_tsquery(?)", [$tsquery])
                      ->orWhereRaw("content ILIKE ?", ['%' . $query . '%'])
                      ->orWhereRaw("? ILIKE '%' || content || '%'", [$query])
                      ->orWhereRaw("tsvectors::text ILIKE ?", ['%' . $query . '%'])
                      ->orWhereRaw("? ILIKE '%' || tsvectors::text || '%'", [$query]);
                })
                ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) DESC NULLS LAST", [$tsquery])
                ->get();
            Log::info("Found " . count($results) . " posts");
        } elseif ($group === 'comments') {
            Log::info("Searching for comments with query: $query");
            $results = Comment::select('id', 'author_id', 'post_id', 'content', 'created_at', 'updated_at')
                ->where(function($q) use ($query, $tsquery) {
                    $q->whereRaw("tsvectors @@ to_tsquery(?)", [$tsquery])
                      ->orWhereRaw("content ILIKE ?", ['%' . $query . '%'])
                      ->orWhereRaw("? ILIKE '%' || content || '%'", [$query])
                      ->orWhereRaw("tsvectors::text ILIKE ?", ['%' . $query . '%'])
                      ->orWhereRaw("? ILIKE '%' || tsvectors::text || '%'", [$query]);
                })
                ->orderByRaw("ts_rank(tsvectors, to_tsquery(?)) DESC NULLS LAST", [$tsquery])
                ->get();
            Log::info("Found " . count($results) . " comments");
        }

        if ($offset > 0) {
            return view('partials.searchResults', compact('results', 'query', 'group'))->render();
        }

        return view('pages.search', compact('results', 'query', 'group'));
    }

    public function searchMembers(Request $request)
    {
        $query = strtolower($request->input('query'));
        $groupId = $request->input('group_id');
    
        $tsquery = $query ? implode(' & ', array_map(function($word) {
            return "$word:*";
        }, explode(' ', $query))) : '';
    
        Log::info("Searching for members in group $groupId with query: $query");
        $results = User::join('group_users', 'users.id', '=', 'group_users.user_id')
            ->join('profile', 'users.id', '=', 'profile.id')
            ->where('group_users.group_id', $groupId)
            ->where(function($q) use ($query, $tsquery) {
                $q->whereRaw("profile.tsvectors @@ to_tsquery('english', ?)", [$tsquery])
                  ->orWhereRaw("users.name ILIKE ?", ['%' . $query . '%'])
                  ->orWhereRaw("? ILIKE '%' || users.name || '%'", [$query])
                  ->orWhereRaw("to_tsvector('english', users.name || ' ' || profile.full_name || ' ' || COALESCE(profile.bio, '')) ::text ILIKE ?", ['%' . $query . '%'])
                  ->orWhereRaw("? ILIKE '%' || to_tsvector('english', users.name || ' ' || profile.full_name || ' ' || COALESCE(profile.bio, '')) ::text || '%'", [$query]);
            })
            ->select('users.*', 'profile.full_name as profile_full_name', 'profile.profile_pic as profile_profile_pic', 'profile.bio as profile_bio')
            ->orderByRaw("ts_rank(profile.tsvectors, to_tsquery('english', ?)) DESC NULLS LAST", [$tsquery])
            ->get();
        Log::info("Found " . count($results) . " members");
    
        return response()->json($results);
    }
    
    public function searchInvites(Request $request)
    {
        $query = strtolower($request->input('query'));
        $groupId = $request->input('group_id');
    
        $tsquery = $query ? implode(' & ', array_map(function($word) {
            return "$word:*";
        }, explode(' ', $query))) : '';
    
        Log::info("Searching for invitees for group $groupId with query: $query");
    
        $group = Group::findOrFail($groupId);
        $groupMembers = $group->members->pluck('id')->toArray();
        $groupMembers[] = $group->author_id;
    
        $invitedUserIds = GroupInviteNotification::where('group_id', $groupId)
            ->with('notification')
            ->get()
            ->pluck('notification.receiver_id')
            ->toArray();
    
        $results = User::leftJoin('profile', 'users.id', '=', 'profile.id')
            ->whereNotIn('users.id', [1, 2])
            ->whereNotIn('users.id', $groupMembers)
            ->whereNotIn('users.id', $invitedUserIds)
            ->where('users.id', '!=', Auth::id())
            ->where(function($q) use ($query, $tsquery) {
                $q->whereRaw("profile.tsvectors @@ to_tsquery('english', ?)", [$tsquery])
                  ->orWhereRaw("users.name ILIKE ?", ['%' . $query . '%'])
                  ->orWhereRaw("? ILIKE '%' || users.name || '%'", [$query])
                  ->orWhereRaw("to_tsvector('english', users.name || ' ' || profile.full_name || ' ' || COALESCE(profile.bio, '')) ::text ILIKE ?", ['%' . $query . '%'])
                  ->orWhereRaw("? ILIKE '%' || to_tsvector('english', users.name || ' ' || profile.full_name || ' ' || COALESCE(profile.bio, '')) ::text || '%'", [$query]);
            })
            ->select('users.*', 'profile.full_name as profile_full_name', 'profile.profile_pic as profile_profile_pic', 'profile.bio as profile_bio')
            ->groupBy('users.id', 'profile.full_name', 'profile.profile_pic', 'profile.bio', 'profile.tsvectors')
            ->orderByRaw("ts_rank(profile.tsvectors, to_tsquery('english', ?)) DESC NULLS LAST", [$tsquery])
            ->get();
    
        Log::info("Found " . count($results) . " invitees");
    

        
        return response()->json($results);
    }
}