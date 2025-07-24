<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountItem extends Model
{
    /** @use HasFactory<\Database\Factories\DiscountItemFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'dis_percent',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'discount_item_id');
    }
}
