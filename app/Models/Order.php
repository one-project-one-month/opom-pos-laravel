<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    public function items(){
        return $this->hasMany(OrderItem::class);
<<<<<<< HEAD
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
     public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function product() {
        return $this->hasMany(Product::class);
=======
>>>>>>> 0b7984589e4fd3c43e63b4f5df8c1a607c343a98
    }
}
