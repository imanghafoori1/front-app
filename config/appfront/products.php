<?php

return [
    /**
     * Exchange rate of the products.
     */
    'exchange_rate' => env('EXCHANGE_RATE', 0.85),

    /**
     * The email address which receives price notifications.
     */
    'price_notification_email' => env('PRICE_NOTIFICATION_EMAIL', 'admin@example.com'),
];
