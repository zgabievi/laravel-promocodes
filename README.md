# Promocodes

> Updated for Laravel 5.3, works on previous versions too.

[![Latest Stable Version](https://poser.pugx.org/zgabievi/promocodes/version.png)](https://packagist.org/packages/zgabievi/promocodes) [![Total Downloads](https://poser.pugx.org/zgabievi/promocodes/d/total.png)](https://packagist.org/packages/zgabievi/promocodes) [![License](https://poser.pugx.org/zgabievi/promocodes/license)](https://packagist.org/packages/zgabievi/promocodes)

| PR0M0C0D35 |     |
|:----------:|:----|
| [![PR0M0C0D35](https://s15.postimg.org/ddh46kj3f/687474703a2f2f692e696d67736166652e6f72672f633135.png)](https://github.com/zgabievi/promocodes) | Promocodes generator for [Laravel 5.*](http://laravel.com/). Trying to be best package in this category. You are welcome to join the party, give me some advices :tada: and make pull requests. |

## Table of Contents
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [License](#license)

## Installation

Run composer command in your terminal.

    composer require zgabievi/promocodes
    
### Open `config/app.php` and follow steps below:

Find the `providers` and add our service provider.

```php
'providers' => [
    ...
    Gabievi\Promocodes\PromocodesServiceProvider::class
],
```

Find the `aliases` and add our facade.

```php
'aliases' => [
    ...
    'Promocodes' => Gabievi\Promocodes\Facades\Promocodes::class
],
```

## Configuration

Publish config & migration file using artisan command:

    php artisan vendor:publish
    
To create table for promocodes in database run:

    php artisan migrate
    
> Configuration parameters are well documented. There is no need to describe each parameter here.

> Check `config/promocodes.php` and read comments there if you need.

## Usage

- Generate as many codes as you wish and output them without saving. 
You will get array of codes in return

```php
Promocodes::output($amount = 1)
```

- Create as many codes as you wish, with same reward for each one.
They will be saved in database and you will get collection of them in return

```php
Promocodes::create($amount = 1, $reward = null)
```

- Check if given code exists and isn't used at all

```php
Promocodes::check($code)
```

- Apply, that given code is used. Update database record.
You will get reward value back or true/false

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
    
    ...
}
```

- Get all promocodes of current user

```php
User::promocodes()
```

> There is query scopes for promocodes: `fresh()`, `byCode($code)`

> `User::promocodes()->fresh()` - all not used codes of user

> `User::promocodes()->byCode($code)` - record which matches given code

- Create promocode(s) for current user. Works exactly same like `create` method of `Promocodes`

```php
User::promocode($amount = 1, $reward = null)
```

- Apply, that given code is used by current user. 
Second argument is callback function, which gives you reward value or true/false

```php
User::applyCode($code, $callback)
```

Example:

```php
$user = Auth::user();

$user->applyCode('ABCD-DCBA', function ($reward) use ($user) {
    return 'Congratulations, ' . $user->name . '! We have added ' . $reward . ' points on your account'.
});
```

## License

Promocodes is an open-sourced laravel package licensed under the MIT license