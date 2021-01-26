# laravel-promocodes

[![Packagist](https://img.shields.io/packagist/v/zgabievi/promocodes.svg)](https://packagist.org/packages/zgabievi/promocodes)
[![Packagist](https://img.shields.io/packagist/dt/zgabievi/promocodes.svg)](https://packagist.org/packages/zgabievi/promocodes)
[![license](https://img.shields.io/github/license/zgabievi/promocodes.svg)](https://packagist.org/packages/zgabievi/promocodes)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/badges/build.png?b=master)](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/build-status/master)

> Promocodes generator for [Laravel 5.*](http://laravel.com/). Trying to make the best package in this category. You are welcome to join the party, give me some advices :tada: and make pull requests.

[![laravel-promocodes](https://banners.beyondco.de/Promocodes.jpeg?theme=light&packageName=zgabievi%2Flaravel-promocodes&pattern=topography&style=style_1&description=Promotional+codes+generator+for+Laravel&md=1&showWatermark=0&fontSize=100px&images=tag)](https://github.com/zgabievi/laravel-promocodes)

## Table of Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Basic Methods](#usage)
    - [User Trait](#promocodes-can-be-related-to-users)
    - [Additional Data](#how-to-use-additional-data)
- [Testing](#testing)
- [License](#license)

### What's new?
- [Additional Data](#how-to-use-additional-data)

## Installation

Install this package via Composer:
```bash
$ composer require zgabievi/promocodes
```

> If you are using Laravel 5.5 or later, then installation is done. Otherwise follow the next steps.

#### Open `config/app.php` and follow steps below:

Find the `providers` array and add our service provider.

```php
'providers' => [
    // ...
    Gabievi\Promocodes\PromocodesServiceProvider::class
],
```

Find the `aliases` array and add our facade.

```php
'aliases' => [
    // ...
    'Promocodes' => Gabievi\Promocodes\Facades\Promocodes::class
],
```

## Configuration

Publish config & migration file using Artisan command:
```bash
$ php artisan vendor:publish
```

To create table for promocodes in database run:
```bash
$ php artisan migrate
```

> Configuration parameters are well documented. There is no need to describe each parameter here.

> Check `config/promocodes.php` and read comments there if you need.

## Usage

Generate as many codes as you wish and output them without saving to database.

You will get array of codes in return:

```php
Promocodes::output($amount = 1);
```

#### Parameters

| name  | type | description | required? |
| ----- | ---- | ----------- | --------- |
| $amount | number | Number of items to be generated | NO |

---

Create as many codes as you wish. Set reward (amount).

Attach additional data as array. Specify for how many days should this codes stay alive.

By default generated code will be multipass (several users will be able to use this code once).

They will be saved in database and you will get collection of them in return:

```php
Promocodes::create($amount = 1, $reward = null, array $data = [], $expires_in = null, $quantity = null, $is_disposable = false);
```

If you want to create code that will be used only once, here is method for you.

```php
Promocodes::createDisposable($amount = 1, $reward = null, array $data = [], $expires_in = null, $quantity = null);
```

#### Parameters

| name  | type | description | default | required? |
| ----- | ---- | ----------- | ------- | --------- |
| $amount | integer | Number of promocodes to generate | 1 | NO |
| $reward | float | Number of reward that user gets (ex: 30 - can be used as 30% sale on something) | null | NO |
| $data | array | Any additional information to get from promocode | [] | NO |
| $expires_in | integer | Number of days to keed promocode valid | null | NO |
| $quantity | integer | How many times can promocode be used? | null | NO |
| $is_disposable | boolean | If promocode is one-time use only | false | NO |

---

Check if given code exists, is usable and not yet expired.

Returns `Promocode` object if valid, or `false` if not.

```php
Promocodes::check($code);
```

#### Parameters

| name  | type | description | required? |
| ----- | ---- | ----------- | --------- |
| $code | string | Code to be checked for validity | YES |

---

If you want to check if user tries to use promocode for second time you can call `Promocodes::isSecondUsageAttempt` and pass `Promocode` object as an argument. As an answer you will get boolean value

---

Redeem or apply code. Redeem is alias for apply method.

User should be authenticated to redeem code or this method will throw an exception (`\Gabievi\Promocodes\Exceptions\UnauthenticatedException`).

Also if authenticated user will try to apply code twice, it will throw an exception (`\Gabievi\Promocodes\Exceptions\AlreadyUsedException`)

Returns `Promocode` object if applied, or `false` if not.

```php
Promocodes::redeem($code);
Promocodes::apply($code);
```

#### Parameters

| name  | type | description | required? |
| ----- | ---- | ----------- | --------- |
| $code | string | Code to be applied by authenticated user | YES |

---

Get the collection of valid promotion codes.

```php
Promocodes::all();
```

---

You can immediately expire code by calling *disable* function. Returning boolean status of update.

```php
Promocodes::disable($code);
```

#### Parameters

| name  | type | description | required? |
| ----- | ---- | ----------- | --------- |
| $code | string | Code to be set as invalid | YES |

---

And if you want to delete expired, or non-usable codes you can erase them.

This method will remove redundant codes from database and their relations to users.

```php
Promocodes::clearRedundant();
```

---

### Promocodes can be related to users

If you want to use user relation open `app/User.php` and make it `Rewardable` as in example:

```php
namespace App;

use Illuminate\Notifications\Notifiable;
use Gabievi\Promocodes\Traits\Rewardable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, Rewardable;

    // ...
}
```
---

Redeem or apply code are same. *redeemCode* is alias of *applyCode*

Pass promotion code you want to be applied by current user.

```php
User::redeemCode($code, $callback = null);
User::applyCode($code, $callback = null);
```

Example (usage of callback):

```php
$redeemMessage = $user->redeemCode('ABCD-DCBA', function ($promocode) use ($user) {
    return 'Congratulations, ' . $user->name . '! We have added ' . $promocode->reward . ' points on your account';
});

// Congratulations, Zura! We have added 10 points on your account
```

### How to use additional data?

1. Process of creation:

```php
Promocodes::create(1, 25, ['foo' => 'bar', 'baz' => 'qux']);
```

2. Getting data back:

```php
Promocodes::redeem('ABC-DEF', function($promocode) {
    echo $promocode->data['foo'];
});

// bar
```

or

```php
User::redeemCode('ABC-DEF', function($promocode) {
    echo $promocode->data['foo'];
});

// bar
```

## Testing

Finally it's here. I've written some test to keep this package healthy and stable

![laravel-promocodes tests](https://user-images.githubusercontent.com/1515299/29971701-4971da9e-8f3a-11e7-9f68-f7677400ef16.png)

## License

laravel-promocodes is licensed under a [MIT License](https://github.com/zgabievi/laravel-promocodes/blob/master/LICENSE).
