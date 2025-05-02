<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        return view('others.support');
    }

    public function features()
    {
        return view('others.features');
    }

    public function about_us()
    {
        return view('others.about_us');
    }

    public function contact_us()
    {
        return view('others.contact_us');
    }
}
