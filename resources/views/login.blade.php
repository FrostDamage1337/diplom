@extends('main')
@section('tab_title')
    Log In
@endsection
@section('content')
    <form class="form login-form" id="login-form" action="{{ route('authorize') }}" method="POST">
        @csrf
        <span>Welcome to the site. Please enter your credentials below.</span>
        <label for="username">
            Username:
            <input type="text" name="name" class="form-input">
        </label>
        <label for="username">
            Password:
            <input type="password" name="password" class="form-input">
        </label>
        <input type="submit" class="button" value="Log In">
    </form>
@endsection