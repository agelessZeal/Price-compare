<?php

namespace Vanguard\Support\Enum;

class ProductStatus
{
    const ACTIVE = 'Active';
    const BANNED = 'InActive';

    public static function lists()
    {
        return [
            self::ACTIVE => trans('app.'.self::ACTIVE),
            self::BANNED => trans('app.'. self::BANNED),
        ];
    }
}
