# Telluris

A library to help manage your PHP app's environment. 

> This library is still under development and is not ready for production use.

## Installation

It is recommended that you install Telluris via Composer.

```shell
composer require cspray/telluris:dev-master
```

## User Guide

Let's take a look at how you can use Telluris to manage environment configurations and how you can use more advanced features.

### Environment Configuration

Here's an example for how you can setup a configuration for a 'staging' and 'production' environment. This includes making 
sure production values are kept secret. This is accomplished by not committing the `/config/secrets.json` file to your 
repository and creating them on the appropriate servers for the environment.

```
/config/environment.json
```

```json
{
    "staging": {
        "host": "localhost",
        "database": "my_db",
        "user": "my_db_user",
        "password": "1234"
    },
    "production": {
        "host": "secret(db.host)",
        "database": "secret(db.database)",
        "user": "secret(db.user)",
        "password": "secret(db.password)"
    }
}
```

```
/config/secrets.json
```

```json
{
    "db": {
        "host": "192.168.1.1",
        "database": "production_db",
        "user": "production_db_user",
        "password": "prod_password"
    }
}
```
