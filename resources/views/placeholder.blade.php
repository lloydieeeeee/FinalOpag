@extends('layouts.app')
@section('title', 'Coming Soon')
@section('page-title', 'Coming Soon')
@section('content')
<div class="rounded-xl shadow-sm overflow-hidden">
    <video 
        class="w-full h-full object-cover"
        autoplay 
        muted 
        loop 
        playsinline>
        <source src="{{ asset('images/illit.mp4') }}" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>



@endsection