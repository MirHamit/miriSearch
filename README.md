# MirHamit/MiriSearch


[![Latest Version on Packagist](https://img.shields.io/packagist/v/vendor_slug/package_slug.svg?style=flat-square)](https://packagist.org/packages/mirhamit/mirisearch)
[![Total Downloads](https://img.shields.io/packagist/dt/vendor_slug/package_slug.svg?style=flat-square)](https://packagist.org/packages/mirhamit/mirisearch)

---
A Minimal Laravel Search Package

## Installation


open terminal and cd to your project root folder

install laravel

Install this package with composer
```bash
composer require mirhamit/miri-search
```

---
## Usage
Add MiriSearch to your user model
in this example we used User model, you can use in any model

```php
use MirHamit\MiriSearch\MiriSearch;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, MiriSearch;
...
}
```

Get your search
In this example we used in route and searched a signle user
```php
Route::get('search', function () {
    return User::miriSearch(['w1w@yahoo.com'], 0, 0)->paginate();
});
```

even you can search in a relation :

```php
    return User::search('w1w@yahoo.com')
        ->query(fn($query) => $query->with([
            'warehouse' => function ($query) {
                // $query->miriSearch(['code'=>'5488', 'phone'=>'+9891413'], 1, 1);
                // $query->miriSearch('Search', 1, 0);
                $query->miriSearch(['test', '6897'], 1, 1);
            }
        ]))
        ->paginate()

```
The second and third parameter of search accepts boolean
If you send 0 to second parameter, search will search with exact word of search

If you send 1 to second parameter, search will search with ``LIKE`` for example ``$query->where($searchableField, "LIKE", "%$value%");``


If you send 0 to third parameter, search will search with ``orWhere``

If you send 1 to third parameter, search will search with ``where``


---
## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---
## Security Vulnerabilities

Please review and check security vulnerabilities and report them in issues section.

---
## Credits

- [Həmid Musəvi](https://github.com/mirhamit)
- [All Contributors](../../contributors)

---
## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
