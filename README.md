# laravel-promocodes

> Updated for Laravel 5.4, works on previous versions too.

[![Latest Stable Version](https://poser.pugx.org/zgabievi/promocodes/version?format=flat-square)](https://packagist.org/packages/zgabievi/promocodes)
[![Total Downloads](https://poser.pugx.org/zgabievi/promocodes/d/total?format=flat-square)](https://packagist.org/packages/zgabievi/promocodes)
[![License](https://poser.pugx.org/zgabievi/promocodes/license?format=flat-square)](https://packagist.org/packages/zgabievi/promocodes)
[![StyleCI](https://styleci.io/repos/46787184/shield)](https://styleci.io/repos/46787184)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/badges/build.png?b=master)](https://scrutinizer-ci.com/g/zgabievi/laravel-promocodes/build-status/master)

| PR0M0C0D35 |     |
|:----------:|:----|
| [![PR0M0C0D35](https://i.imgsafe.org/ff13c6de54.png)](https://github.com/zgabievi/promocodes) | Promocodes generator for [Laravel 5.*](http://laravel.com/). Trying to make the best package in this category. You are welcome to join the party, give me some advices :tada: and make pull requests. |

## Table of Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [License](#license)

## What's new?
- [Additional Data](#how-to-use-additional-data)

## Installation

Install this package via Composer:
```bash
$ composer require zgabievi/promocodes
```

### Open `config/app.php` and follow steps below:

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

Generate as many codes as you wish and output them without saving. 
You will get array of codes in return:

```php
Promocodes::output($amount = 1)
```

Create as many codes as you wish, with same reward for each one.
They will be saved in database and you will get collection of them in return:

```php
Promocodes::create($amount = 1, $reward = null, array $data = [])
```

Check if given code exists and isn't used at all:

```php
Promocodes::check($code)
```

Apply, that given code is used. Update database record. You will get promocode record back or true/false:

```php
Promocodes::apply($code)
```

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

Get all promocodes of current user:

```php
User::promocodes()
```

> There is query scopes for promocodes: `fresh()`, `byCode($code)`:
> - `User::promocodes()->fresh()` - all not used codes of user
> - `User::promocodes()->byCode($code)` - record which matches given code

Create promocode(s) for current user. Works exactly same like `create` method of `Promocodes`:

```php
User::createCode($amount = 1, $reward = null, array $data = [])
```

Apply, that given code is used by current user. 
Second argument is optional, if null, it will return promocode record or boolean, or you can pass callback function, which gives you reward or boolean value as argument:

```php
User::applyCode($code, $callback = null)
```

Example:

```php
$user = Auth::user();

$user->applyCode('ABCD-DCBA', function ($promocode) use ($user) {
    return 'Congratulations, ' . $user->name . '! We have added ' . $promocode->reward . ' points on your account'.
});
```

### How to use additional data?

1. Process of creation:

```php
Promocodes::create(1, 25, ['foo' => 'bar', 'baz' => 'qux']);
```

or

```php
User::createCode(1, 25, ['foo' => 'bar', 'baz' => 'qux']);
```

2. Getting data back:

```php
Promocodes::apply('ABC-DEF', function($promocode) {
    echo $pomocode->data['foo'];
});
```

or

```php
User::applyCode('ABC-DEF', function($promocode) {
    echo $pomocode->data['foo'];
});
```

## License

laravel-promocodes is licensed under a  [MIT License](https://github.com/zgabievi/laravel-promocodes/blob/master/LICENSE).

## TODO
- [x] Create tests to check funtionality
