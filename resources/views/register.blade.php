@extends('main')
@section('tab_title')
    Register
@endsection
@section('content')
    <form class="form form-register" action="{{ route('do_register') }}" method="POST">
        @csrf
        <span>Welcome to the site! Please fill the fields below</span>
        <label for="username">
            Username:
            <input type="text" name="name" class="form-input">
        </label>
        <label for="email">
            E-mail:
            <input type="email" name="email" class="form-input">
        </label>
        <label for="password">
            Password:
            <input type="password" name="password" class="form-input">
        </label>
        <input type="submit" class="button" value="Register">
    </form>
@endsection