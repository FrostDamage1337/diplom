<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Item;
use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_in_box', 'box_id', 'item_id');
    }
}
