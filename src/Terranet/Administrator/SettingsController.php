<?php

namespace Terranet\Administrator;

use Illuminate\Support\Arr;
use Terranet\Administrator\Requests\UpdateRequest;

class SettingsController extends ControllerAbstract
{
    /**
     * List settings by selected group [according to settings page name]
     *
     * @return $this
     */
    public function edit()
    {
        return view(app('scaffold.template')->layout('settings'), [
            'settings' => options_fetch()
        ]);
    }

    /**
     * Save settings per page
     *
     * @param UpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateRequest $request)
    {
        options_save(Arr::except(
            $request->all(),
            ['_token', 'save']
        ));

        return back()->with('messages', ['Settings saved successfully']);
    }

    public function index()
    {
        return $this->edit();
    }
}
