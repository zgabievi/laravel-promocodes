[![#StandWithUkraine](https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1)](https://supportukrainenow.org)
[![laravel-promocodes](https://banners.beyondco.de/laravel-promocodes.jpeg?theme=light&packageManager=composer+require&packageName=zgabievi%2Flaravel-promocodes&pattern=topography&style=style_2&description=Coupons+and+promotional+codes+generator.&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)](https://github.com/zgabievi/laravel-promocodes)

# laravel-promocodes

[![Packagist](https://img.shields.io/packagist/v/zgabievi/promocodes.svg)](https://packagist.org/packages/zgabievi/promocodes)
[![Packagist](https://img.shields.io/packagist/dt/zgabievi/promocodes.svg)](https://packagist.org/packages/zgabievi/promocodes)
[![license](https://img.shields.io/github/license/zgabievi/promocodes.svg)](https://packagist.org/packages/zgabievi/promocodes)

Coupons and promotional codes generator for [Laravel](https://laravel.com). Current release is only
for [Laravel 9.x](https://laravel.com/docs/9.x) and [PHP 8.1](https://www.php.net/releases/8.1/en.php). It's completely
rewritten, and if you are using previous version, you should change your code accordingly. Code is simplified now and it
should take you several minutes to completely rewrite usage.

> **Attention:**
> Current version is completely rewritten. If you are missing some functionality, that was possible to achieve in previous versions, fill free to open issue.
> Hope this new version will be easier to use, and it will provide better functionality for your needs.

## Installation

You can install the package via composer:

```bash
composer require zgabievi/laravel-promocodes
```

## Configuration

```bash
php artisan vendor:publish --provider="Zorb\Promocodes\PromocodesServiceProvider"
```

Now you can change configurations as you need:

```php
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
```

After you configure this file, run migrations:

```bash
php artisan migrate
```

Now you will need to use AppliesPromocode on your user model.

```php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zorb\Promocodes\Traits\AppliesPromocode;

class User extends Authenticatable {
    use AppliesPromocode;

    //
}
```

## Usage

It's very easy to use. Methods are combined, so that you can configure promocodes easily.

- [Reference](#reference)
- [Creating Promocodes](#creating-promocodes)
- [Generating Promocodes](#creating-promocodes)
- [Applying Promocode](#applying-promocode)
- [Expiring Promocode](#expiring-promocode)
- [Additional Methods](#additional-methods)

## Reference

| Name          | Explanation                                                                                                             |
|---------------|-------------------------------------------------------------------------------------------------------------------------|
| Mask          | Astrisks will be replaced with random symbol                                                                            |
| Characters    | Allowed symbols to use in mask replacement                                                                              |
| Multi use     | Define if single code can be used multiple times, by the same user                                                      |
| Unlimited     | Generated code will have unlimited usages                                                                               |
| Bound to user | Define if promocode can be used only one user, if user is not assigned initially, first user will be bound to promocode |
| User          | Define user who will be initially bound to promocode                                                                    |
| Count         | Amount of unique promocodes should be generated                                                                         |
| Usages        | Define how many times can promocode be used                                                                             |
| Expiration    | DateTime when promocode should be expired. Null means that promocode will never expire                                  |
| Details       | Array of details which will be retrieved upon apply                                                                     |

## Creating Promocodes

### Using class

Combine methods as you need. You can skip any method that you don't need, most of them already have default values.

```php
use Zorb\Promocodes\Facades\Promocodes;

Promocodes::mask('AA-***-BB') // default: config('promocodes.code_mask')
          ->characters('ABCDE12345') // default: config('promocodes.allowed_symbols')
          ->multiUse() // default: false
          ->unlimited() // default: false
          ->boundToUser() // default: false
          ->user(User::find(1)) // default: null
          ->count(5) // default: 1
          ->usages(5) // default: 1
          ->expiration(now()->addYear()) // default: null
          ->details([ 'discount' => 50 ]) // default: []
          ->create();
```

### Using helper

There is a global helper function which will do the same as promocodes class. You can use named arguments magic from php
8.1.

```php
createPromocodes(
    mask: 'AA-***-BB', // default: config('promocodes.code_mask')
    characters: 'ABCDE12345', // default: config('promocodes.allowed_symbols')
    multiUse: true, // default: false
    unlimited: true, // default: false
    boundToUser: true, // default: false
    user: User::find(1), // default: null
    count: 5, // default: 1
    usages: 5, // default: 1
    expiration: now()->addYear(), // default: null
    details: [ 'discount' => 50 ] // default: []
);
```

### Using command

There is also the command for creating promocodes. Parameters are optional here too.

```bash
php artisan promocodes:create\
  --mask="AA-***-BB"\
  --characters="ABCDE12345"\
  --multi-use\
  --unlimited\
  --bound-to-user\
  --user=1\
  --count=5\
  --usages=5\
  --expiration="2022-01-01 00:00:00"
```

### Generating Promocodes

If you want to output promocodes and not save them to database, you can call generate method instead of create.

```php
use Zorb\Promocodes\Facades\Promocodes;

Promocodes::mask('AA-***-BB') // default: config('promocodes.code_mask')
          ->characters('ABCDE12345') // default: config('promocodes.allowed_symbols')
          ->multiUse() // default: false
          ->unlimited() // default: false
          ->boundToUser() // default: false
          ->user(User::find(1)) // default: null
          ->count(5) // default: 1
          ->usages(5) // default: 1
          ->expiration(now()->addYear()) // default: null
          ->details([ 'discount' => 50 ]) // default: []
          ->generate();
```

### Applying Promocode

### Using class

Combine methods as you need. You can skip any method that you don't need.

```php
use Zorb\Promocodes\Facades\Promocodes;

Promocodes::code('ABC-DEF')
          ->user(User::find(1)) // default: null
          ->apply();
```

### Using helper

There is a global helper function which will do the same as promocodes class.

```php
applyPomocode(
    'ABC-DEF',
    User::find(1) // default: null
);
```

### Using command

There is also the command for applying promocode.

```bash
php artisan promocodes:apply ABC-DEF --user=1
```

#### Exceptions

While trying to apply promocode, you should be aware of exceptions. Most part of the code throws exceptions, when there is a problem:

```php
// Zorb\Promocodes\Exceptions\*

PromocodeAlreadyUsedByUserException - "The given code `ABC-DEF` is already used by user with id 1."
PromocodeBoundToOtherUserException - "The given code `ABC-DEF` is bound to other user, not user with id 1."
PromocodeDoesNotExistException - "The given code `ABC-DEF` doesn't exist." | "The code was not event provided."
PromocodeExpiredException - "The given code `ABC-DEF` already expired."
PromocodeNoUsagesLeftException - "The given code `ABC-DEF` has no usages left."
UserHasNoAppliesPromocodeTrait - "The given user model doesn't have AppliesPromocode trait."
UserRequiredToAcceptPromocode - "The given code `ABC-DEF` requires to be used by user, not by guest."
```

#### Events

There are two events which are fired upon applying.

```php
// Zorb\Promocodes\Events\*

GuestAppliedPromocode // Fired when guest applies promocode
    // It has public variable: promocode

UserAppliedPromocode // Fired when user applies promocode
    // It has public variable: promocode
    // It has public variable: user
```

### Expiring Promocode

### Using helper

There is a global helper function which will expire promocode.

```php
expirePromocode('ABC-DEF');
```

### Using command

There is also the command for expiring promocode.

```bash
php artisan promocodes:expire ABC-DEF
```

## Trait Methods

If you added AppliesPromocode trait to your user model, you will have some additional methods on user.

```php
$user = User::find(1);

$user->appliedPromocodes // Returns promocodes applied by user
$user->boundPromocodes // Returns promocodes bound to user
$user->applyPromocode('ABC-DEF') // Applies promocode to user
```

## Additional Methods

```php
Promocodes::all(); // To retrieve all (available/not available) promocodes
Promocodes::available(); // To retrieve valid (available) promocodes
Promocodes::notAvailable(); // To retrieve invalid (not available) promocodes
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](https://github.com/zgabievi/laravel-promocodes/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Zura Gabievi](https://github.com/zgabievi)
- [All Contributors](https://github.com/zgabievi/laravel-promocodes/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/zgabievi/laravel-promocodes/blob/master/LICENSE.md)
for more information.
