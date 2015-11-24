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
	 * This will be appended to mask
	 *
	 */
	'prefix'     => false,

	/**
	 * Promo code suffix.
	 * This will be prepended to mask
	 *
	 */
	'suffix'     => false,

	/**
	 * Promo code mask.
	 * Only asterisk will be replaced
	 *
	 */
	'mask'       => '****-****',

	/**
	 * Promo code prefix and suffix separator.
	 *
	 */
	'separator'  => '-',
];