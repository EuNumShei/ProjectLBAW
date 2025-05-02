<?php

namespace App\Http\Controllers;

use App\Models\GroupUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use Illuminate\Auth\Access\AuthorizationException;

class GroupUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $groupUsers = GroupUser::all();
        return response()->json($groupUsers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:profile,id',
            'group_id' => 'required|integer|exists:group,id',
        ]);

        $groupUser = GroupUser::create($validatedData);

        return response()->json($groupUser, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $groupUser = GroupUser::findOrFail($id);
        return response()->json($groupUser);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:profile,id',
            'group_id' => 'required|integer|exists:group,id',
        ]);

        $groupUser = GroupUser::findOrFail($id);
        $groupUser->update($validatedData);

        return response()->json($groupUser);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userProfile = Auth::user()->profile;

        
        $group = Group::findOrFail($id);
            if ($group->author_id === $userProfile->id) {

                $firstMemberId = GroupUser::where('group_id', $id)
                ->orderBy('created_at', 'asc')
                ->first()
                ->user_id;

                $firstMember = GroupUser::where('group_id', $id)->where('user_id', $firstMemberId)->delete();

            if ($firstMember) {
                $group->author_id = $firstMemberId;
                $group->save();
                return redirect()->route('groups.index')->with('success', 'Left Group.');
            } else {
                $group->delete();
                return redirect()->route('groups.index')->with('success', 'Group deleted.');
            }
        }

        $deleted = GroupUser::where('group_id', $id)->where('user_id', $userProfile->id)->delete();
    
        if ($deleted) {
            return redirect()->back();
        }
        
        return redirect()->back()->with('error', 'Error Leaving Group');
    }

    public function removeUser(string $id, string $userId)
    {

        try {
            $this->authorize('isGroupAuthor', Group::findOrFail($id));
        } catch (AuthorizationException $e) {
            return response()->json(['kicked' => false], 200);
        }

        $deleted = GroupUser::where('group_id', $id)->where('user_id', $userId)->delete();
    
        if ($deleted) {
            return response()->json(['kicked' => true], 200);
        }
        
        return response()->json(['kicked' => false], 200);
    }
}