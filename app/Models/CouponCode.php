<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    // 用常量的方式定义支持的优惠卷类型
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED => '固定金额',
        self::TYPE_PERCENT => '比例',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    protected $dates = ['not_before', 'not_after'];

    protected $appends = ['description'];

    /**
     * 优惠码生成
     * @param $length
     * @return string
     */
    public static function findAvailableCode($length = 16)
    {
        do {
            $code = strtoupper(Str::random($length));
        } while(self::query()->where('code', $code)->exists());

        return $code;
    }

    /**
     * 类型、 折扣 和 最低金额 这三个字段的友好输出可以在多个地方使用
     * @return string
     */
    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            // 去掉后面的小数.00
            $str = '满'.str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str.'优惠'.str_replace('.00', '', $this->value).'%';
        }
        return $str.'减'.str_replace('.00', '', $this->value);
    }
}
