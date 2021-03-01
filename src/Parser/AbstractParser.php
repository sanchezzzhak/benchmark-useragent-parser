<?php

namespace App\Parser;

abstract class AbstractParser
{
    abstract public function parseUserAgent(string $useragent): array;

    abstract public function getFixtures(): array;

    abstract public static function getFixtureUseragent($data): string;
}