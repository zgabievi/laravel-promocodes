# laravel-promocodes

> Use version 0.5.1 for Laravel 5.1

[![Latest Stable Version](https://poser.pugx.org/zgabievi/promocodes/version?format=flat-square)](https://packagist.org/packages/zgabievi/promocodes)
[![Total Downloads](https://poser.pugx.org/zgabievi/promocodes/d/total?format=flat-square)](https://packagist.org/packages/zgabievi/promocodes)
[![License](https://poser.pugx.org/zgabievi/promocodes/license?format=flat-square)](https://packagist.org/packages/zgabievi/promocodes)
[![StyleCI](https://styleci.io/repos/46787184/shield)](https://styleci.io/repos/46787184)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/badges/build.png?b=master)](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/build-status/master)

> Promocodes generator for [Laravel 5.*](http://laravel.com/). Trying to make the best package in this category. You are welcome to join the party, give me some advices :tada: and make pull requests.

## Table of Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Basic Methods](#usage)
    - [User Trait](#promocodes-can-be-related-to-users)
    - [Additional Data](#how-to-use-additional-data)
- [License](#license)

### What's new?
- [Additional Data](#how-to-use-additional-data)

## Installation

Install this package via Composer:
```bash
$ composer require zgabievi/promocodes
```

> If you are using Laravel 5.5, than installation is done. Otherwise follow next steps.

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

---

Create as many codes as you wish. Set reward (amount). 

Attach additional data as array. Specify for how many days should this codes stay alive.

By default generated code will be multipass (several users will be able to use this code once).

They will be saved in database and you will get collection of them in return:

```php
Promocodes::create($amount = 1, $reward = null, array $data = [], $expires_in = null);
```

If you want to create code that will be used only once, here is method for you.

```php
Promocodes::createDisposable($amount = 1, $reward = null, array $data = [], $expires_in = null);
```

---

Check if given code exists, is usable and not yet expired. 

This code may throw `\Gabievi\Promocodes\Exceptions\InvalidPromocodeExceprion` if there is not such promocode in database, with give code.

Returns `Promocode` object if valid, or `false` if not.

```php
Promocodes::check($code);
```

---

Redeem or apply code. Redeem is alias for apply method. 

User should be authenticated to redeem code or this method will thow an exception (`\Gabievi\Promocodes\Exceptions\UnauthenticatedExceprion`). 

Also if authenticated user will try to apply code twice, it will throw an exception (`\Gabievi\Promocodes\Exceptions\AlreadyUsedExceprion`)

Returns `Promocode` object if applied, or `false` if not.

```php
Promocodes::redeem($code);
Promocodes::apply($code);
```

---

You can imediately expire code by calling *disable* function. Returning boolean status of update.

```php
Promocodes::disable($code);
```

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
    echo $pomocode->data['foo'];
});

// bar
```

or

```php
User::redeemCode('ABC-DEF', function($promocode) {
    echo $pomocode->data['foo'];
});

// bar
```

## License

laravel-promocodes is licensed under a [MIT License](https://github.com/zgabievi/laravel-promocodes/blob/master/LICENSE).
