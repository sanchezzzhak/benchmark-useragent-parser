<?php

namespace App\Helper;

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
        $suffixes = ['byte', 'Kb', 'Mb', 'Gb', 'Tb'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[(int)floor($base)];
    }

}
