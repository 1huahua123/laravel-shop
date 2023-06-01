<?php

namespace App\Models;

use App\Exceptions\InvalidRequestException;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'description',
        'price',
        'stock'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }


    public function decreaseStock($amount)
    {
        if($amount < 0){
            throw new InvalidRequestException('减库存不可小于0');
        }

        return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    public function addStock($amount)
    {
        if($amount < 0){
            throw new InvalidRequestException('加库存不可小于0');
        }

        $this->increment('stock', $amount);
    }
}
