<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Services\ChancesService;
use App\Models\Box;
use App\Models\UserItem;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TestController extends BaseController
{
    public function balanceManipulations($box)
    {
        $user = Auth::user();

        if ($user->balance - $box->price < 0) {
            return false;
        } else {
            $user->balance = round(round($user->balance, 2) - round($box->price, 2), 2);
            $user->update();

            return true;
        }
    }

    public function index(ChancesService $service, Request $request)
    {
        $box_id = $request->input('box_id');
        $box = Box::with('items')->find($box_id);
        $box_items = $box->items->pluck('price')->toArray();
        asort($box_items);
        
        if (!$this->balanceManipulations($box)) {
            return response()->json([
                'alert' => [
                    'type' => 'danger',
                    'message' => 'You don\'t have enough money'
                ]
            ]);
        }

        $result = $service->calculateChances($box_items, $box->price);

        if (is_array($result)) {
            $result = array_combine($box->items->sortBy('price')->pluck('id')->toArray(), $result);
        }

        $number = rand(0, 100) + rand(0, 10) / 10 + rand(0, 10) / 100;

        if ($number > 100) {
            $number = 100;
        }
        
        $sum = 0;
        $won_id;
        asort($result);
        
        foreach ($result as $id => $chance) {
            $sum += $chance;

            if ($sum >= $number) {
                $won_id = $id;
                break;
            }
        }

        $item_won = Item::find($won_id + 1);
        $id = UserItem::create([
            'user_id' => Auth::id(),
            'item_id' => $item_won->id,
        ])->id;
        $item_won->user_item_id = $id;

        return $item_won;
    }
}