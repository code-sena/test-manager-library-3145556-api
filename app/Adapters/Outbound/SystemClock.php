<?php

namespace App\Adapters\Outbound;

use App\Application\Ports\Clock;

class SystemClock implements Clock
{
    public function now(): \DateTimeImmutable
    {
        $offsetDays = (int) env('CLOCK_OFFSET_DAYS', 0);

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        if ($offsetDays !== 0) {
            $now = $now->modify("{$offsetDays} days");
        }

        return $now;
    }
}
