<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    /**
     * @var string[]
     */
    protected $dates = ['last_used_at'];

    /**
     * 与 User 模型关联，关联关系是一对多
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 创建了一个访问器，
     * 在之后的代码里可以直接通过 $address->full_address 来获取完整的地址，
     * 而不用每次都去拼接
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
