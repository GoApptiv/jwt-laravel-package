# GoApptiv/JWT

JWT is used to verify and generate JWT Token.

## Installation

Add the following code in the composer to install this package into your Laravel Project

```composer
"require": {
        ...
        "goapptiv/jwt": "dev-master"

    }
```

```composer
 "repositories": [
        {
            "type": "git",
            "url": "https://github.com/sagar-goapptiv/jwt"
        }
    ]
```

Add the Token and Encryption method in your .env file.

```.env
TOKEN_SECRET_KEY=
TOKEN_ENCRYPTION_METHOD=
```

## Usage

```php
use GoApptiv\JWT\JWT;

JWT::decrypt($token)
```
