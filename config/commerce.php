<?php

return [
    'commission_rate' => (float) env('PLATFORM_COMMISSION_RATE', 10),
    'minimum_payout' => (float) env('MINIMUM_SELLER_PAYOUT', 10000),
];
