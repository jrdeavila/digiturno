<?php

namespace App\Observers;

class ClientObserver
{
    public function creating(\App\Models\Client $client)
    {
        // Add client to the current cache
        $clients = \Illuminate\Support\Facades\Cache::get('clients', []);
        $clients[] = $client;
        \Illuminate\Support\Facades\Cache::put('clients', $clients, 512);
    }

    public function updating(\App\Models\Client $client)
    {

        $clients = \Illuminate\Support\Facades\Cache::get('clients', []);
        $clients = array_map(function ($c) use ($client) {
            return $c->id === $client->id ? $client : $c;
        }, $clients);
        \Illuminate\Support\Facades\Cache::put('clients', $clients, 512);
    }

    public function deleting(\App\Models\Client $client)
    {
        $clients = \Illuminate\Support\Facades\Cache::get('clients', []);
        $clients = array_filter($clients, function ($c) use ($client) {
            return $c->id !== $client->id;
        });
        \Illuminate\Support\Facades\Cache::put('clients', $clients, 512);
    }

    public function restoring(\App\Models\Client $client)
    {
        $clients = \Illuminate\Support\Facades\Cache::get('clients', []);
        $clients[] = $client;
        \Illuminate\Support\Facades\Cache::put('clients', $clients, 512);
    }

    public function forceDeleted(\App\Models\Client $client)
    {
        $clients = \Illuminate\Support\Facades\Cache::get('clients', []);
        $clients = array_filter($clients, function ($c) use ($client) {
            return $c->id !== $client->id;
        });
        \Illuminate\Support\Facades\Cache::put('clients', $clients, 512);
    }
}
