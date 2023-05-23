<?php

return [
    'front_url' => env('FRONT_URL'),
    'front_password_reset_url' => env('FRONT_URL') . '/auth/password-reset/',
    'loop_per_hour' => intval(env('LOOP_PER_HOUR', 1)),
];
