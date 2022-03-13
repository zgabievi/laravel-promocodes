<?php

return [
    'models' => [
        'promocodes' => [
            'model' => \Zorb\Promocodes\Models\Promocode::class,
            'table_name' => 'promocodes',
            'foreign_id' => 'promocode_id',
        ],

        'users' => [
            'model' => \App\Models\User::class,
            'table_name' => 'users',
            'foreign_id' => 'user_id',
        ],

        'pivot' => [
            'model' => \Zorb\Promocodes\Models\PromocodeUser::class,
            'table_name' => 'promocode_user',
        ],
    ],
    'code_mask' => '****-****',
    'allowed_symbols' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789',
];
