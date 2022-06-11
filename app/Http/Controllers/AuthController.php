<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends BaseController
{
    public function login()
    {
        if (!Auth::user()) {
            return view('login');
        } else {
            return back();
        }
    }

    public function register()
    {
        if (!Auth::user()) {
            return view('register');
        } else {
            return back();
        }
    }

    public function authorize(LoginRequest $request)
    {
        if (!$request->authenticate()) {
            return redirect()->route('login')->with([
                'alert' => [
                    'type' => 'danger',
                    'message' => 'No such registered user was found.'
                ]
            ]);
        }
        $request->session()->regenerate();
        
        return redirect()->route('index');
    }

    public function do_register(RegisterRequest $request)
    {
        $fields = $request->validated();

        User::create([
            'name' => $fields['name'],
            'password' => Hash::make($fields['password']),
            'email' => $fields['email'],
        ]);

        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function getBalance()
    {
        return Auth::user()->balance;
    }
}