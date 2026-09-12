<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $table = 'inventory_item';
    protected $primaryKey = 'inventory_item_id';
    public $timestamps = false;

    protected $fillable = [
        'branch_id',
        'product_id',
        'quantity_on_hand',
        'last_restocked',
        'inventory_status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
