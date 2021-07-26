<?php

namespace App\Http\Terranet\Administrator\Modules;

use App\Models\Collection;
use App\Traits\Columns\CollectionColumns;
use Terranet\Administrator\Contracts\Module\Editable;
use Terranet\Administrator\Contracts\Module\Exportable;
use Terranet\Administrator\Contracts\Module\Filtrable;
use Terranet\Administrator\Contracts\Module\Navigable;
use Terranet\Administrator\Contracts\Module\Sortable;
use Terranet\Administrator\Contracts\Module\Validable;
use Terranet\Administrator\Resource;
use Terranet\Administrator\Traits\Module\AllowFormats;
use Terranet\Administrator\Traits\Module\HasFilters;
use Terranet\Administrator\Traits\Module\HasForm;
use Terranet\Administrator\Traits\Module\HasSortable;
use Terranet\Administrator\Traits\Module\ValidatesForm;

/**
 * Administrator Resource Collections
 *
 * @package Terranet\Administrator
 */
class Collections extends Resource implements Navigable, Filtrable, Editable, Validable, Sortable, Exportable
{
    use HasFilters, HasForm, HasSortable, ValidatesForm, AllowFormats, CollectionColumns;

    /**
     * The module Eloquent model
     *
     * @var string
     */
    protected $model = Collection::class;

    public function linkAttributes() {
        return ['icon' => 'fa fa-folder'];
    }

    public function filters() {

        $this->addFilter('name', 'text', 'Search by Name');

        return $this->filters;
    }

    public function form() {

        return array_merge(
            $this->scaffoldForm(),
            [
                'banner_asset_url' => [
                    'type' => 'text',
                    'class' => 'asset-picker',
                    'data-asset-value' => 'url',
                    'readonly' => 'readonly',
                    'label' => 'Banner Asset',
                    'description' => 'Upload/select the banner image for the Collections list view. Images should be sized to 1111x190',
                ],
                'cover_asset_url' => [
                    'type' => 'text',
                    'class' => 'asset-picker',
                    'data-asset-value' => 'url',
                    'readonly' => 'readonly',
                    'label' => 'Cover Image',
                    'description' => 'Upload/select the image that should represent this Collection in the list view. Be sure to use the 300px wide crop preset when exporting your selection.',
                ]
            ]
        );
    }
}