## Sessions in Laravel

The following documentation is based on my [Laravel Cache for Beginners](https://www.youtube.com/watch?v=UjA-16diixc) tutorial we’re going to cover the basics of cache in Laravel. <br> <br>
•	Author: [Code With Dary](https://github.com/codewithdary) <br>
•	Twitter: [@codewithdary](https://twitter.com/codewithdary) <br>
•	Instagram: [@codewithdary](https://www.instagram.com/codewithdary/) <br>

## Usage <br>
Setup your coding environment <br>
```
git clone git@github.com:codewithdary/laravel8-tailwindcss2.git
cd laravel8-tailwindcss2
composer install
cp .env.example .env 
php artisan key:generate
php artisan cache:clear && php artisan config:clear 
php artisan serve 
```

## Database Setup <br>
We will be performing database tests which (obviously) needs to interact with the database. Make sure that your database credentials are up and running.
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_cache
DB_USERNAME=root
DB_PASSWORD=
```

Next up, we need to create the database which will be grabbed from the ```DB_DATABASE``` environment variable.
```
mysql;
create database laravel_cache;
exit;
```
## Cache in Laravel

In Laravel, the cache is the act of transparently storing data for future use in an attempt to make applications run faster.

You can definitely say that a cache looks like a session. You definitely use them in the same exact way, since you need to provide a key to store them.

Of course, there are differences. ```Sessions``` are used to store data between page requests while a cache is used to cache data per application. Therefore, you usually store stuff like database queries and API calls in your cache.

## Cache Configuration

You can find the cache configuration file inside the ```/config/cache.php``` file. In here, you’ll find some settings that you can change up. I do recommend you to create an environment variable rather than changing the default value setting.

The most important setting is the ```default```, since you’re going to set the default cache driver. 

Before we continue on, let’s focus on the different cache driver options.

### File
The first one is the file driver, which is also the default one.The file driver will create a new encrypted file per cache in ```/storage/framework/cache``` folder.

Keep in mind that the key will be encrypted, even if you try to retrieve content from your cache.

It’s also recommended to use the file driver for development, since it’s faster than adding your cache in the database.

### Array
The second available driver is the array driver. This is just a simple array where you will store your cache. The advantage of using the array driver is the fact that you don’t need to configure a driver.

```
CACHE_DRIVER=array
```

### Database
The third available driver is the database driver. This will store your cache in memory for the current PHP process.

In order to use the database driver, you got to make sure that you create a database table through artisan.
```
php artisan cache:table
```

There is a huge downside of storing your cache in the database. The database driver can obviously be overloaded when you got a ton of cache on your site. In my personal opinion, I don’t recommend using the database driver in real life examples.
```
CACHE_DRIVER=database
```

### Redis & Memcached
Redis and Memcached are both more complex than the previous ones. The Redis driver uses an in-memory-based caching technology, while Memcached does exactly the same, but it requires a bit more server maintenance.
```
CACHE_DRIVER=redis
CACHE_DRIVER=memcached
```

## Accessing the Cache
There are a few different ways to access a cache. The first option is to use the Cache Façade.
```ruby
Cache::get(‘users’);
```

You can also get an instance from the container
```ruby
Route::get(‘users’), function(Illuminate\Contracts\Cache\Repository $cache) {
	Return $cache->get(‘users’);
});
```

The last available method is to use the global ```cache()``` helper.
```ruby
$users = cache()->get(‘users’);
```

I personally prefer to use the Cache Façade, but don’t be bothered using any of the other available methods, since the output will be exactly the same.

## Available Methods

### get($key, $fallbackValue)
The ```get()``` method is used to pull values for any given key.
```ruby
Cache::get('users');
```

### pull($key, $fallbackValue)
The ```pull()``` method is exactly the same as the ```get()``` method, except it removes the cached value after retrieving it.
 ```ruby
Cache::pull('users');
```

### put($key, $value, $secondsOrExpiration)
The put method will set the value of the specified key for a given number of seconds. 
```ruby
Cache::put(‘user’, ‘Code With Dary’, now()->addDay());
```

### add($key, $value)
The ```add()``` method is similar to the ```put()``` method but if the value already exists in the cache, it won’t set it. Keep in mind that it will also return a Boolean indicating whether or not the value was actually added.
```ruby
Cache::add(‘user’, 'Code With Dary');
```

### forever($key, $value)
The ```forever()``` method saves a value to the cache for a specific key. It’s similar to the ```put()``` method, except the value will never expire since you won’t set the expiration time.
```ruby
Cache::forever(user, 'Dary');
```

### has($key)
The ```has()``` method will check whether or not there’s a value at the provided key. It will return a true if the value exists, and false if the value does not exist.
```ruby
if(Cache::has(user)) {
    dd('Cache exists');
}
```

### increment($key, $amount)
The ```increment()``` method speaks for itself. It will increase the value in the cache. If there is no value given, it will treat the value as it was a 0.
```ruby
Cache::increment(‘user’, 1);
```

### decrement($key, $amount)
The ```decrement()``` method also speaks for itself. It will decrease the value in the cache. If there is no value given, it will treat the value as it was a 0.
```ruby
Cache::decrement('user', 1);
```

### forget($key)
The ```forget()``` method removes a previously set cache value.	
```ruby
Cache::forget('user');
```

### flush()
The ```flush()``` method removes every cache value, even those set by the framework itself.
```ruby
Cache::flush();
```

## Example
I personally don’t like storing cache in the database so instead of showing you that, let’s create a simple example where we’re going to store 1000 posts inside the cache, so we don’t need to grab the values from the database every single time we try to find all posts.

Let’s start off by pulling in the authentication scaffolding.
```
composer require laravel/ui
php artisan ui vue –auth
npm install && npm run dev
```

For the posts, we need to create a model, factory, migration and resource controller.
```
php artisan make:model Post -fmr
```

Let’s define the posts migration in the ```/database/migrations/create_posts_table.php``` file:
```ruby
public function up()
{
    Schema::create('posts', function (Blueprint $table){
        $table->increments('id');
        $table->string('title');
        $table->longText('description');
        $table->timestamps();
        $table->unsignedBigInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users');
    });
}
```

Let’s also set up our factory in the ```/database/factory/PostFactory.php``` file, which will be pretty simple because we only have a ```user_id```, ```title```, and ```description``` that needs to be set:
```ruby
public function definition()
{
    return [
        'user_id' => 1,
        'title' => $this->faker->sentence(),
        'description' => $this->faker->paragraph()
    ];
}
```

Before you can run tinker to run your factory, you got to make sure that you migrate your tables and create a new user, since ```user_id``` is already been set to 1.

The next step is to run our factory through tinker.
```
php artisan tinker
```

In here, we got to make sure that we call our model, chain the count method of and pass in an integer of the amount of rows we’d like to create, and chain the create method to finish it off.
```
App\Models\Post:factory()->count(1000)->create();
```

The output should be 1000 new rows inside your database. We got to make srue that we have an event and listener setup because it will fetch data from Laravels cache.
```
php artisan make:event PostCreated
php artisan make:listener PostCacheListener
```

Let’s change up the ```handle()``` method inside the ```/app/Listeners/PostCacheListener.php``` file.
```ruby
public function handle($event)
{
    Cache::forget('posts');

    Cache::forever('posts', Post::all());
}```
We got to make sure that we remove the cache, even when it hasn’t been set. Then, we got to make sure that we create a new cache which will last forever. The values will be grabbed from the Post model.

We got to make sure that we hook our event into our model, which can easily be done with the property ```$dispatchesEvents``` in the ```Post``` model.
```ruby
protected $dispatchesEvents = [
    'created' => PostCreated::class
];
```

When using Events and Listeners, you got to make sure that you register them inside the ```/app/Providers/EventServiceProvider.php``` file, in the property ```protected $listen```
```ruby
protected $listen = [
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],
    PostCreated::class => [
        PostCacheListener::class
    ]
];
```

We’re almost done. We got to make sure that we dispatch the event, then get all posts and put it inside the cache inside our ```\app\Controllers\PostController.php``` file.
```ruby
public function index()
{
    Event::dispatch(new PostCreated());

    $posts = cache('posts', function () {
        return Post::get();
    });

    return view('index', $posts);
}
```

The last step if defining the ```/blog``` endpoint inside the ```/routes/web.php``` file and creating a new folder inside the ```/resources/views/``` folder, called ```/blog```, and add a new file called ```/index.blade.php``` in there. 
```ruby
Route::get('/', [PagesController::class, 'index']);
Route::get('/blog', [PostController::class, 'index']);
```

If we navigate to the browser and change our endpoint to ```/blog```, the ```/resources/views/blog/index.blade.php``` file is being called, but the most important thing is the cache folder that has been created inside the ```/storage/framework/cache/data``` folder.

Right here, you’ll find a big JSON file which holds all posts inside that me as the user, has fetched.

# Credits due where credits due…
Thanks to [Laravel](https://laravel.com/) for giving me the opportunity to make this tutorial on [Sessions](https://laravel.com/docs/8.x/cache).
