<?php

namespace App\Application\Ports;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
