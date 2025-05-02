<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.info');
    }

    public function account()
    {
        return view('settings.account');
    }

    public function changePassword()
    {
        return view('settings.change_password');
    }

    public function deleteAccount()
    {
        return view('settings.delete_account');
    }

    public function profileSettings()
    {
        return view('settings.profile_settings');
    }

    public function blockedAccounts()
    {
        return view('settings.blocked_accounts');
    }
}
