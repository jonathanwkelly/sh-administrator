<?php

namespace App\Http\Terranet\Administrator\Templates;

use Terranet\Administrator\Contracts\Services\TemplateProvider;
use Terranet\Administrator\Services\Template;

class Artworks extends Template implements TemplateProvider {

	/**
     * Scaffold index templates
     *
     * @param $partial
     * @return mixed array|string
     */
    public function edit($partial = 'index')
    {
        $partials = array_merge(
            parent::edit(null),
            [
                'index' => 'admin.artworks.custom_edit'
            ]
        );

        return (null === $partial ? $partials : $partials[$partial]);
    }

}