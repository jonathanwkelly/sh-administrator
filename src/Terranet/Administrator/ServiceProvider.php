<?php

namespace Terranet\Administrator;

use App\User;
use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use Creativeorange\Gravatar\Facades\Gravatar;
use Creativeorange\Gravatar\GravatarServiceProvider;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\ServiceProvider as BreadcrumbsServiceProvider;
use Diglactic\Breadcrumbs\Manager as BreadcrumbsManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Pingpong\Menus\MenuFacade;
use Pingpong\Menus\MenusServiceProvider;
use Terranet\Administrator\Middleware\Web;
use Terranet\Administrator\Providers\ArtisanServiceProvider;
use Terranet\Administrator\Providers\ContainersServiceProvider;
use Terranet\Administrator\Providers\EventServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $baseDir = base_path('vendor/jonathanwkelly/sh-administrator');

        /*
         * Publish & Load routes
         */
        $packageRoutes = "{$baseDir}/publishes/routes.php";
        $publishedRoutes = app_path('Http/Terranet/Administrator/routes.php');
        $this->publishes([$packageRoutes => $publishedRoutes]);
        if (! $this->app->routesAreCached()) {
            $routesFile = file_exists($publishedRoutes) ? $publishedRoutes : $packageRoutes;

            /** @noinspection PhpIncludeInspection */
            require_once $routesFile;
        }

        /*
         * Publish & Load configuration
         */
        $this->publishes(["{$baseDir}/publishes/config.php" => config_path('administrator.php')], 'config');
        $this->mergeConfigFrom("{$baseDir}/publishes/config.php", 'administrator');

        /*
         * Publish & Load views, assets
         */
        $this->publishes(["{$baseDir}/publishes/public" => public_path('administrator')], 'public');
        $this->publishes(["{$baseDir}/publishes/views" => base_path('resources/views/vendor/administrator')], 'views');
        $this->loadViewsFrom("{$baseDir}/publishes/views", 'administrator');

        /*
         * Publish & Load translations
         */
        $this->publishes(
            ["{$baseDir}/publishes/translations" => base_path('resources/lang/vendor/administrator')],
            'translations'
        );
        $this->loadTranslationsFrom("{$baseDir}/publishes/translations", 'administrator');

        /*
         * Publish default Administrator Starter Kit: modules, dashboard panels, policies, etc...
         */
        $this->publishes(
            ["{$baseDir}/publishes/Modules" => app_path('Http/Terranet/Administrator/Modules')],
            'boilerplate'
        );
        $this->publishes(
            ["{$baseDir}/publishes/Dashboard" => app_path('Http/Terranet/Administrator/Dashboard')],
            'boilerplate'
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        if (guarded_auth()) {
            $this->configureAuth();
        } else {
            $this->fakeWebMiddleware();
        }

        $dependencies = [
            ArtisanServiceProvider::class,
            ContainersServiceProvider::class,
            EventServiceProvider::class,
            BreadcrumbsServiceProvider::class => [
                'Breadcrumbs' => BreadcrumbsManager::class,
            ],
            HtmlServiceProvider::class => [
                'Html' => HtmlFacade::class,
                'Form' => FormFacade::class,
            ],
            MenusServiceProvider::class => [
                'AdminNav' => MenuFacade::class,
            ],
            GravatarServiceProvider::class => [
                'Gravatar' => Gravatar::class,
            ],
        ];

        array_walk($dependencies, function ($package, $provider) {
            if (is_string($package) && is_numeric($provider)) {
                $provider = $package;
                $package = null;
            }

            if (! $this->app->getProvider($provider)) {
                $this->app->register($provider);

                if (is_array($package)) {
                    foreach ($package as $alias => $facade) {
                        class_alias($facade, $alias);
                    }
                }
            }
        });
    }

    protected function configureAuth()
    {
        if (! Config::has('auth.guards.admin')) {
            Config::set('auth.guards.admin', [
                'driver' => 'session',
                'provider' => 'admins',
            ]);
        }

        if (! Config::has('auth.providers.admins')) {
            Config::set('auth.providers.admins', [
                'driver' => 'eloquent',
                'model' => User::class,
            ]);
        }
    }

    /**
     * Laravel 5.1 does not come with 'web' middlware group
     * so for back compatibility with Laravel 5.1 & Laravel 5.2
     * we add this faked Middleware
     */
    protected function fakeWebMiddleware()
    {
        if (! app('Illuminate\Contracts\Http\Kernel')->hasMiddleware('web')) {
            app('router')->middleware('web', Web::class);
        }
    }
}
