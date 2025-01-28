<?php

namespace App\Helpers;

use Vinkla\Hashids\Facades\Hashids;

class HashidsHelper
{
    public static function decodeId($encoded)
    {
        $result = Hashids::decode((string) $encoded);

        if (count($result) != 1) {
            return (int) 0;
        }

        return $result[0];
    }
}
