<?php

namespace App\Helpers;

use Symfony\Component\Console\Helper\Helper;

class ParserHelper extends Helper
{
    public function getName()
    {
        return 'parser';
    }

    public function formatBytes(float $bytes, int $precision = 2): string
    {
        $base = log($bytes, 1024);
        $suffixes = ['', 'K', 'M', 'G', 'T'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[(int)floor($base)];
    }

}
