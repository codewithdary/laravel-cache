<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PagesController extends Controller
{
    public function index()
    {
        //Cache::put('cachekey', 4, now()->addDay());

        // add() - similar to put but won't set it if the value already exists
        //Cache::add('cachekey', 'Key 2');

        //forever() - No need to add a expiration date because it will not expire
        //Cache::forever('cachekey2', 'Key 2');

        // forget() wipes the key's value
        //Cache::forget('cachekey2');

        // flush() reomves all caches
        //Cache::flush();

        // has() returns a boolean indicating whether or not there's a value at the provided key.
        //if(Cache::has('cachekey')) {
        //    dd('Cache exists');
        //}

        // increment() allows you to increase an integer value in the cache
        //Cache::increment('cachekey', 1);

        // decrement() allows you to decrease an integer value in the cache
        //Cache::decrement('cachekey', 1);

        return view('home');
    }
}
