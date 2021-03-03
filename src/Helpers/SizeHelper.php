<?php


namespace App\Helpers;


use Symfony\Component\Console\Helper\Helper;


class SizeHelper extends Helper
{
    public function formatBytes(float $bytes, int $precision = 2): string
    {
        $base = log($bytes, 1024);
        $suffixes = ['', 'K', 'M', 'G', 'T'];

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[(int)floor($base)];
    }

    public function getName(): string
    {
        return 'sizeBytes';
    }
}