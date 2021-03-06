<?php

namespace Webographen\AdminLog;

use Statamic\Providers\AddonServiceProvider;
use Webographen\AdminLog\Listeners\AdminLogSubscriber;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        AdminLogSubscriber::class,
    ];

    public function boot()
    {
        parent::boot();
        
        $this->mergeConfigFrom(__DIR__.'/../config/admin-log.php', 'admin-log');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/admin-log.php' => config_path('admin-log.php'),
            ], 'admin-log');
        }

        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', ['--tag' => 'admin-log']);
        });

        $this->app->make('config')->set('logging.channels.adminlog', [
            'driver' => 'daily',
            'path' => storage_path('logs/'.config('admin-log.log-name', 'adminlog').'.log'),
            'level' => 'debug',
            'days' => config('admin-log.delete-after', 30),
        ]);
    }
}
