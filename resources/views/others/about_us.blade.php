@extends('layouts.app')
@section('title', 'About Us')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>About Us</h1>
    </div>

    <div class="content-container">
        <h1>Glinthub</h1>
        <p>
            Is a social media platform designed to provide entertaining and meaningful connections through personalized timelines, groups and content sharing. 
            By offering a fluid and engaging personal and group experience, it aims to bring it's users together as a healthy and ever-expanding community.
        </p>
        <p>
            It's being developed by a team of four FEUP students with the objective of building an intuitive and scalable social media platform that enables users to create and share content. 
            Our platform is designed to enable both individual and group interactions, as well as content sharing and socialization without the need for physical presence.
        </p>
        <p>
            It seeks to address the increasing demand for online socialization and community-building tools, especially in an era where physical presence is often not possible or practical. 
            Glinthub is driven by the goal of creating a healthy environment where people can communicate through both personal profiles and groups, in order to allow long distance social interactions, all while guaranteeing user privacy and control over personal information and credentials.
        </p>
    </div>
</div>
@endsection