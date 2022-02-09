# GoApptiv/JWT

JWT Package is used to verify and generate JWT Token.

## Installation

Add the following code in the composer to install this package into your Laravel Project

```composer
"require": {
        ...
        "goapptiv/jwt": "^2.0"

    }
```

```composer
 "repositories": [
        {
            "type": "git",
            "url": "https://github.com/GoApptiv/jwt-laravel-package"
        }
    ]
```

Add the Token Key in your .env file.

```.env
TOKEN_SECRET_KEY=
```

## Usage

Decrypting a Token

```php
use GoApptiv\JWT\JWT;

JWT::decrypt($token);
```

Encrypting Data

```php
use GoApptiv\JWT\JWT;

JWT::encrypt($data);
```
