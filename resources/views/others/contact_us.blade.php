@extends('layouts.app')
@section('title', 'Contact Us')

@section('content')
<div class="page-container">
    <div class="header-container">
        <a href="{{ route('publicPosts') }}" class="back-button"><i class="fas fa-arrow-left"></i></a>
        <h1>Contact Us</h1>
    </div> 

    <div class="contact-info">
        <h1>Team Emails:</h1>
        <p class="contact-info-item">André Santos: <span class="email">up202207724@up.pt</span></p>
        <p class="contact-info-item">Guilherme Teixeira: <span class="email">up202204875@up.pt</span></p>
        <p class="contact-info-item">Júlio Santos: <span class="email">up202207975@up.pt</span></p>
        <p class="contact-info-item">Rafael Cunha: <span class="email">up202208957@up.pt</span></p>
    </div>
</div>
@endsection