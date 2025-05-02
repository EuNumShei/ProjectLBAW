<?php

namespace App\Http\Controllers;

use App\Models\UserBlocked;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserBlockedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $authProfile = Profile::findOrFail(Auth::id());
        $blockedAccounts = $authProfile->blockedUsers;

        Log::info($blockedAccounts);
        
        return view('settings.blocked_accounts', compact('blockedAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'blocked_user_id' => 'required|integer|exists:profile,id',
        ]);

        $validatedData['user_id'] = Auth::id();

        Log::info('Store method called with parameters:', [
            'user_id' => $validatedData['user_id'],
            'blocked_user_id' => $validatedData['blocked_user_id'],
        ]);

        $userBlocked = UserBlocked::create($validatedData);

        return redirect()->back()->with('success', 'User blocked successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($user_id, $blocked_user_id)
    {
        $userBlocked = UserBlocked::where('user_id', $user_id)->where('blocked_user_id', $blocked_user_id)->firstOrFail();
        return response()->json($userBlocked);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $user_id, $blocked_user_id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer|exists:profile,id',
            'blocked_user_id' => 'required|integer|exists:profiles,id',
        ]);

        $userBlocked = UserBlocked::where('user_id', $user_id)->where('blocked_user_id', $blocked_user_id)->firstOrFail();
        $userBlocked->update($validatedData);

        return response()->json($userBlocked);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $blocked_user_id)
    {
        $user_id = Auth::id();

        Log::info('Destroy method called with parameters:', [
            'user_id' => $user_id,
            'blocked_user_id' => $blocked_user_id,
        ]);

        $userBlocked = UserBlocked::where('user_id', $user_id)
            ->where('blocked_user_id', $blocked_user_id)
            ->delete();

        if($userBlocked){
            return redirect()->back()->with('success', 'User unblocked successfully.');   
        }
        else{
            return redirect()->back()->with('error', 'Error unblocking user.');
        }
    }
}