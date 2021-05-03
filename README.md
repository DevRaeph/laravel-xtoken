<p align="center"><img src="https://i.postimg.cc/8cp7GcZ7/Unbenannt-1.jpg" alt="Social Card of laravel-xtoken"></p>

# DevRaeph / laravel-xtoken
[![Total Downloads]](https://packagist.org/packages/devraeph/laravel-xtoken)
[![Latest Stable Version]](https://packagist.org/packages/devraeph/laravel-xtoken)
[![Issues]](https://github.com/DevRaeph/laravel-xtoken/issues)

A simple package with which you can issue JWT tokens 
and also verify them. Usable for multiple models, thanks to existing trait.

## Installation

Package is available on [Packagist](https://packagist.org/packages/devraeph/laravel-xtoken),
you can install it using [Composer](https://getcomposer.org).

```shell
composer require devraeph/laravel-xtoken
```

Migrate Database for token table 
```shell
php artisan migrate
```
Publish Config
```shell
php artisan vendor:publish --provider="DevRaeph\XToken\JWTTokenServiceProvider" --tag="config"
```
## Documentation

Model-Trait `HasXToken` <br>
Example: <br>
```php

namespace App\Models;
use DevRaeph\XToken\Traits\HasXToken;

class User extends Authenticatable
{
    use HasFactory, HasXToken;

    ...
}
```

Issuing a Token:<br>
```php

use DevRaeph\XToken\Facades\Tokenizer;
use Carbon\CarbonImmutable;

$myJWT = Tokenizer::setModel(/* MODEL eg USER */)
        ->setIssuedBy(/* Default is env("APP_URL") */)
        ->setExpiresAt(CarbonImmutable::now()->addDays(15))
        ->createToken();      
        
return $myJWT->toArray(); //Return Token Array
return $myJWT->toJson();  //Return Token Json
```

Using Middelware in Routes to parse Token:<br>
```php
/*
 * Use exsisting `xToken` middleware
 */
Route::group(['middleware' => ['api','xToken']], function () {
    Route::post("verify",function (){
        echo "Checked Token -> Valid";
    });
});
```

Get TokenClaim -> ID of Model
```php
use App\Models\User;
use DevRaeph\XToken\Facades\TokenizerClaim;

$myClaim = TokenizerClaim::setRequest($request)
    ->get();
    
$myUser = User::whereId($myClaim)->first();
```

[Total Downloads]: https://img.shields.io/packagist/dt/devraeph/laravel-xtoken
[Latest Stable Version]: https://img.shields.io/packagist/v/devraeph/laravel-xtoken
[Issues]: https://img.shields.io/github/issues/DevRaeph/laravel-xtoken
