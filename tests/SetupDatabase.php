<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;


trait FreshDatabaseSetup {
    /**
    * If true, setup has run at least once.
    * @var boolean
    */
    protected static $setUpHasRunOnce = false;    /**
    * After the first run of setUp "migrate:fresh --seed"
    * @return void
    */
    public function setUp()
    {
        parent::setUp();
        if (!static::$setUpHasRunOnce) {
            Artisan::call('migrate:fresh');
            Artisan::call(
                'db:seed', ['--class' => 'DatabaseSeeder']
            );
            static::$setUpHasRunOnce = true;
         }
    }
}
