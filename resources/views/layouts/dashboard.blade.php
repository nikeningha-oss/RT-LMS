@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('container-class', '')

@section('content')
    <!-- Sidebar -->
    @include('layouts.sidebar')
    
    <!-- Main Content -->
    <div class="main-content-tracklane">
        <!-- Top Navigation -->
        @include('layouts.top-nav')
        
        <!-- Page Content -->
        @yield('dashboard-content')  
    </div>
@endsection