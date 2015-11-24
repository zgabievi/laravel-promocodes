<?php

return [

	/**
	 * Promo codes Eloquent model.
	 *
	 */
	'model' => \App\Promocode::class,

	/**
	 * List of characters, promo code generated from.
	 *
	 */
	'characters' => '23456789ABCDEFGHJKLMNPQRSTUVWXYZ',

	/**
	 * Promo code prefix.
	 *
	 */
	'prefix'     => false,

	/**
	 * Promo code suffix.
	 *
	 */
	'suffix'     => false,

	/**
	 * Promo code mask.
	 *
	 */
	'mask'       => '****-****',

	/**
	 * Promo code prefix and suffix separator.
	 *
	 */
	'separator'  => '-',
];