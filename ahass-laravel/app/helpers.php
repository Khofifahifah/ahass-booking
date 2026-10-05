<?php

function format_rupiah(int $amount): string
{
    return 'Rp '.number_format($amount, 0, ',', '.');
}

function status_label(string $status): string
{
    return config('ahass.statuses.'.$status, $status);
}
