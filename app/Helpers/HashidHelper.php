<?php
namespace App\Helpers;

use Hashids\Hashids;

class HashidHelper
{
    protected $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(
            config('hashids.salt'),
            config('hashids.length'),
            config('hashids.alphabet')
        );
    }

    /**
     * Encode ID menjadi hashid
     *
     * @param int|string $id
     * @return string
     */
    public function encode($id)
    {
        return $this->hashids->encode($id);
    }

    /**
     * Decode hashid menjadi ID asli
     *
     * @param string $hashid
     * @return int|null
     */
    public function decode($hashid)
    {
        $decoded = $this->hashids->decode($hashid);
        return !empty($decoded) ? $decoded[0] : null;
    }
}

