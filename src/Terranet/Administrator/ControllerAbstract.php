<?php

namespace Terranet\Administrator;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Terranet\Administrator\Middleware\Authenticate;
use Terranet\Administrator\Middleware\AuthProvider;
use Terranet\Administrator\Middleware\Badges;
use Terranet\Administrator\Middleware\Resources;

class ControllerAbstract extends BaseController
{
    public function __construct()
    {
        if (! guarded_auth()) {
            $this->middleware(AuthProvider::class);
        }

        $this->middleware(Authenticate::class);

        $this->middleware(Resources::class);

        $this->middleware(Badges::class);
    }

    /**
     * Authorize a given action against a set of arguments.
     *
     * @param mixed       $ability
     * @param mixed|array $arguments
     *
     * @return bool
     */
    public function authorize($ability, $arguments = [])
    {
        if (!$response = app('scaffold.actions')->authorize($ability, $arguments)) {
            throw $this->createGateUnauthorizedException(
                $ability,
                $arguments,
                trans('administrator::errors.unauthorized')
            );
        }

        return $response;
    }

    /**
     * Remember previous page.
     */
    public function rememberPreviousPage()
    {
        if (URL::current() !== URL::previous()) {
            session()->set('admin_previous_url', URL::previous());
        }
    }

    protected function redirectTo($module, $key = null)
    {
        if (Request::get('save')) {
            return redirect(route('scaffold.edit', $this->toMagnetParams(['module' => $module, 'id' => $key])));
        }

        if (Request::get('save_return')) {
            if ($previous = $this->getPreviousUrl()) {
                $this->forgetPreviousUrl();

                return redirect()->to($previous);
            }

            return redirect(route('scaffold.index', $this->toMagnetParams(['module' => $module])));
        }

        return redirect(route('scaffold.create', $this->toMagnetParams(['module' => $module])));
    }

    /**
     * Get previous page.
     *
     * @return mixed
     */
    protected function getPreviousUrl()
    {
        return session('admin_previous_url');
    }

    /**
     * Forget previous url.
     */
    protected function forgetPreviousUrl()
    {
        session()->forget('admin_previous_url');
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function toMagnetParams(array $data = [])
    {
        return app('scaffold.magnet')->with($data)->toArray();
    }
}
