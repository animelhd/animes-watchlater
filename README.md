## Laravel Watchlater

❤️ User watchlater feature for Laravel Application.

[![CI](https://github.com/overtrue/laravel-watchlater/workflows/CI/badge.svg)](https://github.com/overtrue/laravel-watchlater/actions)
[![Latest Stable Version](https://poser.pugx.org/overtrue/laravel-watchlater/v/stable.svg)](https://packagist.org/packages/overtrue/laravel-watchlater)
[![Latest Unstable Version](https://poser.pugx.org/overtrue/laravel-watchlater/v/unstable.svg)](https://packagist.org/packages/overtrue/laravel-watchlater)
[![Total Downloads](https://poser.pugx.org/overtrue/laravel-watchlater/downloads)](https://packagist.org/packages/overtrue/laravel-watchlater)
[![License](https://poser.pugx.org/overtrue/laravel-watchlater/license)](https://packagist.org/packages/overtrue/laravel-watchlater)

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

## Installing

```shell
composer require animelhd/animes-watchlater -vvv
```

### Configuration & Migrations

```php
php artisan vendor:publish --provider="Animelhd\AnimesWatchlater\WatchlaterServiceProvider"
```

## Usage

### Traits

#### `Animelhd\AnimesWatchlater\Traits\Watchlaterer`

```php

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Animelhd\AnimesWatchlater\Traits\Watchlaterer;

class User extends Authenticatable
{
    use Watchlaterer;

    <...>
}
```

#### `Animelhd\AnimesWatchlater\Traits\Watchlaterable`

```php
use Illuminate\Database\Eloquent\Model;
use Animelhd\AnimesWatchlater\Traits\Watchlaterable;

class Post extends Model
{
    use Watchlaterable;

    <...>
}
```

### API

```php
$user = User::find(1);
$post = Post::find(2);

$user->watchlater($post);
$user->unwatchlater($post);
$user->toggleWatchlater($post);
$user->getWatchlaterItems(Post::class)

$user->hasWatchlatered($post);
$post->hasBeenWatchlateredBy($user);
```

#### Get object watchlaterers:

```php
foreach($post->watchlaterers as $user) {
    // echo $user->name;
}
```

#### Get Watchlater Model from User.

Used Watchlaterer Trait Model can easy to get Watchlaterable Models to do what you want.
_note: this method will return a `Illuminate\Database\Eloquent\Builder` _

```php
$user->getWatchlaterItems(Post::class);

// Do more
$watchlaterPosts = $user->getWatchlaterItems(Post::class)->get();
$watchlaterPosts = $user->getWatchlaterItems(Post::class)->paginate();
$watchlaterPosts = $user->getWatchlaterItems(Post::class)->where('title', 'Laravel-Watchlater')->get();
```

### Aggregations

```php
// all
$user->watchlaters()->count();

// with type
$user->watchlaters()->withType(Post::class)->count();

// watchlaterers count
$post->watchlaterers()->count();
```

List with `*_count` attribute:

```php
$users = User::withCount('watchlaters')->get();

foreach($users as $user) {
    echo $user->watchlaters_count;
}


// for Watchlaterable models:
$posts = Post::withCount('watchlaterers')->get();

foreach($posts as $post) {
    echo $post->watchlaters_count;
}
```

### Attach user watchlater status to watchlaterable collection

You can use `Watchlaterer::attachWatchlaterStatus($watchlaterables)` to attach the user watchlater status, it will set `has_watchlatered` attribute to each model of `$watchlaterables`:

#### For model

```php
$post = Post::find(1);

$post = $user->attachWatchlaterStatus($post);

// result
[
    "id" => 1
    "title" => "Add socialite login support."
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_watchlatered" => true
 ],
```

#### For `Collection | Paginator | CursorPaginator | array`:

```php
$posts = Post::oldest('id')->get();

$posts = $user->attachWatchlaterStatus($posts);

$posts = $posts->toArray();

// result
[
  [
    "id" => 1
    "title" => "Post title1"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_watchlatered" => true
  ],
  [
    "id" => 2
    "title" => "Post title2"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_watchlatered" => false
  ],
  [
    "id" => 3
    "title" => "Post title3"
    "created_at" => "2021-05-20T03:26:16.000000Z"
    "updated_at" => "2021-05-20T03:26:16.000000Z"
    "has_watchlatered" => true
  ],
]
```

#### For pagination

```php
$posts = Post::paginate(20);

$user->attachWatchlaterStatus($posts);
```

### N+1 issue

To avoid the N+1 issue, you can use eager loading to reduce this operation to just 2 queries. When querying, you may specify which relationships should be eager loaded using the `with` method:

```php
// Watchlaterer
$users = User::with('watchlaters')->get();

foreach($users as $user) {
    $user->hasWatchlatered($post);
}

// with watchlaterable object
$users = User::with('watchlaters.watchlaterable')->get();

foreach($users as $user) {
    $user->hasWatchlatered($post);
}

// Watchlaterable
$posts = Post::with('watchlaters')->get();
// or
$posts = Post::with('watchlaterers')->get();

foreach($posts as $post) {
    $post->isWatchlateredBy($user);
}
```

### Events

| **Event**                                     | **Description**                             |
| --------------------------------------------- | ------------------------------------------- |
| `Animelhd\AnimesWatchlater\Events\Watchlatered`   | Triggered when the relationship is created. |
| `Animelhd\AnimesWatchlater\Events\Unwatchlatered` | Triggered when the relationship is deleted. |

## License

MIT
