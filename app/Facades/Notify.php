<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Notify extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Helpers\NotificationHelper::class;
    }
}
