<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserItem;
use Illuminate\Support\Facades\Auth;

class ItemsController extends Controller
{
    public function index()
    {
        $items = User::with('items')->find(Auth::id());

        return view('items', [
            'user' => $items
        ]);
    }

    public function sellItem($user_item_id)
    {
        $user_item = UserItem::find($user_item_id);
        $user = Auth::user();

        if ($user->id != $user_item->user_id) {
            return back()->with([
                'alert' => [
                    'type' => 'danger',
                    'message' => 'Item doesn\'t exist in your backpack.'
                ]
            ]);
        }

        $user->balance = $user->balance + round($user_item->item->price * 1.1, 2);
        $user->update();
        $user_item->forceDelete();

        return back()->with([
            'alert' => [
                'type' => 'success',
                'message' => 'Item was sucessfully sold! Check your new balance.'
            ]
        ]);
    }
}
