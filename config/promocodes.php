<?php

return [

    /*
     * Database table name that will be used in migration
     * You can remove this key - value pair and we will
     * use default database name: 'promocodes'
     */
    'table' => 'promocodes',

    /*
     * Database pivot table name for promocodes and users relation
     * use default database name: 'promocode_user'
     */
    'relation_table' => 'promocode_user',

    /*
     * List of characters, promo code generated from.
     * We have removed 1 (one) and I because with some
     * fonts you can't find deference between them
     */
    'characters' => '23456789ABCDEFGHJKLMNPQRSTUVWXYZ',

    /*
     * Promo code prefix.
     * This will be starting string of every promocode
     * You can set it to false or string
     *
     * Ex: foo
     * Output: foo-1234-1234
     */
    'prefix' => false,

    /*
     * Promo code suffix.
     * This will be ending string of every promocode
     * You can set it to false or string
     *
     * Ex: bar
     * Output: 1234-1234-bar
     */
    'suffix' => false,

    /*
     * Promo code mask.
     * Only asterisk will be replaced, so you can add
     * or remove as many asterisk as you with
     *
     * Ex: ***-**-***
     */
    'mask' => '****-****',

    /*
     * Promo code prefix and suffix separator.
     * Can be set any thing you wish
     */
    'separator' => '-',

    /**
     * User model
     */
    'user_model' => \App\User::class,
];
