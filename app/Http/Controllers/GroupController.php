<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Profile;
use App\Models\GroupInviteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Auth\Access\AuthorizationException;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {
        $groups = Auth::user()->profile->groups;
        $groups2 = Auth::user()->profile->belongToGroups;
        $allGroups = $groups->merge($groups2);

        return view('groups.list', compact('allGroups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
        ]);
    
        $validatedData['author_id'] = Auth::id();
    
        $group = Group::create($validatedData);
    
        return redirect()->route('groups.show', ['id' => $group->id]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $offset = $request->input('offset', 0);
        $group = Group::findOrFail($id);
        $groupMembers = $group->members;
        $groupMembers->push($group->author);
    
        $allProfiles = Profile::whereNotIn('id', [1,2])->get();
            
        $invitedUserIds = GroupInviteNotification::where('group_id', $group->id)
            ->where('request_status', 'pending')
            ->with('notification')
            ->get()
            ->pluck('notification.receiver_id')
            ->toArray();
    
        $invitableUsers = $allProfiles->diff($groupMembers)->filter(function ($user) use ($invitedUserIds) {
            return !in_array($user->id, $invitedUserIds) && $user->id !== Auth::id();
        });
    
        $posts = $group->posts()->orderBy('created_at', 'desc')->skip($offset)->take(15)->get();
        Log::info($posts);

        if ($offset > 0) {
            Log::info('Loading more posts');
            Log::info($offset);
            Log::info($posts);
            return view('partials.feed', ['items' => $posts]);
        }
    

        try{
            $this->authorize('view', $group);
        } catch (AuthorizationException $e) {
            redirect()->back()->with('error', 'You do not have permission to view this group');
        }
        $this->authorize('view', $group);

        return view('groups.info', [
            'group' => $group,
            'invitableUsers' => $invitableUsers,
            'groupMembers' => $groupMembers,
            'posts' => $posts
        ]);
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        try{
            $this->authorize('update', Group::findOrFail($id));
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You do not have permission to update this group'], 403);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string|max:150',
        ]);


        $group = Group::findOrFail($id);
        $group->name = $validatedData['name'];
        $group->description = $validatedData['description'] ?? 'No description available';


        $group->save();

        return redirect()->route('groups.show', $group->id)->with('success', 'Group updated successfully.');
    }

    /**
     * Show the form for creating a new group.
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        try{
            $this->authorize('delete', Group::findOrFail($id));
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'You do not have permission to delete this group'], 403);
        }

        $group = Group::findOrFail($id);
        $group->delete();

        return response()->json(null, 204);
    }

    /**
    * Show the group view.
    */
    public function showGroup($id): View
    {
        $group = Group::findOrFail($id);
        return view('groups.info', [
            'group' => $group
        ]);
    }

    public function edit(string $id)
    {
        $group = Group::findOrFail($id);
        return view('groups.edit', ['group' => $group]);
    }
}