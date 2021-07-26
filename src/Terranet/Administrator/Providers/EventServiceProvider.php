<?php

namespace Terranet\Administrator\Providers;

use App\Providers\EventServiceProvider as AppEventServiceProvider;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Terranet\Administrator\Traits\SessionGuardHelper;

class EventServiceProvider extends AppEventServiceProvider
{
    use SessionGuardHelper;

    /**
     * Register any other events for your application.
     *
     */
    public function boot()
    {
        parent::boot();

//        $this->handleMissingModuleParameter($events);

        $config = app('scaffold.config');

        if ($config->get('manage_passwords', true)) {
            if ($model = $this->fetchModel($config)) {
                $model::saving(function ($user) {
                    if (! empty($user->password) && $user->isDirty('password')) {
                        $user->password = bcrypt($user->password);
                    }
                });
            }
        }
    }

    /**
     * Handle the cases then custom controller missing Route's $module parameter as action argument
     * Ex: if GET route /admin/pages is handled by custom controller CustomPagesController@index
     * @param DispatcherContract $events
     */
//    protected function handleMissingModuleParameter(DispatcherContract $events)
//    {
//        $events->listen('router.matched', function (Route $route, Request $request) {
//            if ($route->getParameter('module'))
//                return true;
//
//            if ($resolver = app('scaffold.config')->get('resource.resolver')) {
//                $module = call_user_func_array($resolver, [$route, $request]);
//            } else {
//                $module = $request->segment(app('scaffold.config')->get('resource.segment', 2));
//            }
//
//            $route->setParameter('module', $module);
//
//            return $module;
//        });
//    }
}
