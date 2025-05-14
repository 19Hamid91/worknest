<?php

use Carbon\Carbon;

if (!function_exists('toIndoDate')) {
    function toIndoDate($date, $format = 'Y-m-d')
    {
        return Carbon::parse($date)
            ->timezone('Asia/Jakarta')
            ->format($format);
    }
}
