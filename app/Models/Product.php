<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'const_price',
        'stock',
        'brand_id',
        'category_id',
        'photo',
        'expired_at'
    ];

    public static function validationRules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'integer',
                $id ? Rule::unique('products')->ignore($id) : 'unique:products,sku'
            ],
            'price' => 'required|numeric|min:0',
            'const_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'brand_id' => 'required|integer|exists:brands,id',
            'category_id' => 'required|integer|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expired_at' => 'nullable|date|after:today'
        ];
    }

    public static function updatedValidationRules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => [
                'required',
                'integer',
                $id ? Rule::unique('products')->ignore($id) : 'unique:products,sku'
            ],
            'price' => 'required|numeric|min:0',
            'const_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'brand_id' => 'required|integer|exists:brands,id',
            'category_id' => 'required|integer|exists:categories,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expired_at' => 'nullable|date|after:today'
        ];
    }

    public function discountItem()
    {
        return $this->belongsTo(DiscountItem::class, 'discount_item_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
