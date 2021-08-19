<?php

namespace App\Http\Terranet\Administrator\Modules;

use App\Models\Artwork;
use App\Models\ShopifyAccessToken;
use \App\Models\Artist;
use App\Lib\Shopify\Shopify;
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
 * Administrator Resource Artworks
 *
 * @package Terranet\Administrator
 */
class Artworks extends Resource implements Navigable, Filtrable, Editable, Validable, Sortable, Exportable
{
    use HasFilters, HasForm, HasSortable, ValidatesForm, AllowFormats;

    /**
     * The module Eloquent model
     *
     * @var string
     */
    protected $model = Artwork::class;

    public function title() {
        return trans("administrator::module.resources.artworks");
    }

    public function linkAttributes() {
        return ['icon' => 'fa fa-picture-o'];
    }

    public function filters() {

        $this->filters = array_merge(
            $this->scaffoldFilters(),
            [
                'collection' => \admin\filter\select(
                    'Collection',
                    ['' => '--All--'] + \App\Models\Collection::orderBy('name', 'asc')->pluck('name', 'id')->toArray(),
                    function($query, $value) { // filter query
                        return $query->whereIn(
                            'artworks.id',
                            \App\Models\ArtworkCollection::where('collection_id', $value)->pluck('artwork_id')->toArray()
                        );
                    }
                )
            ]
        );

        $this->addFilter('name', 'text', 'Search by Name');

        return $this->filters;
    }

    public function form() {

        $shopifyLib = \App::make('ShopifyAPI');

        $formFields = array_merge(
            $this->scaffoldForm(),
            [
                'asset_id' => [
                    'type' => 'text',
                    'class' => 'asset-picker',
                    'data-asset-value' => 'id',
                    'readonly' => 'readonly',
                    'label' => 'CMYK Source File',
                    'description' => 'Upload/select the hi-res image.',
                ],
                'rgb_asset_id' => [
                    'type' => 'text',
                    'class' => 'asset-picker',
                    'data-asset-value' => 'id',
                    'readonly' => 'readonly',
                    'label' => 'RGB Preview File',
                    'description' => 'Upload/select an image to be used to generate example images',
                ],
                'keywords' => [
                    'label' => 'Search Keywords',
                    'type' => 'textarea',
                    'description' => 'enter keywords, separated by a space',
                    'style' => 'height: 55px'
                ],
                'artists.artist_id' => [
                    'label' => 'Artists',
                    'type' => 'select',
                    'multiple' => TRUE,
                    'description' => 'ctrl+click/cmd+click to select multiple',
                    'options' => function() {
                        return Artist::artistsForAdmin();
                    }
                ],
                'collections.collection_id' => [
                    'label' => 'Collections',
                    'type' => 'select',
                    'multiple' => TRUE,
                    'description' => 'ctrl+click/cmd+click to select multiple',
                    'options' => function() {
                        return \App\Models\Collection::pluck('name', 'id');
                    }
                ],
                'shopifyProducts.shopify_product_id' => [
                    'label' => 'Substrates',
                    'type' => 'select',
                    'multiple' => TRUE,
                    'description' => 'Select the substrates onto which this artwork can be printed.<br><br>ctrl+click/cmd+click to select multiple',
                    'options' => $shopifyLib->products(['DATA' => ['collection_id' => env('SHOPIFY_SUBSTRATES_COLLECTION_ID')]])
                        ->filter(function ($item) {
                            if((strpos($item->name, 'Build-Your-Own') === false)) {
                                return $item;
                            }
                        })
                        ->pluck('name', 'id'),
                ],
                'orientation' => \admin\form\select('Orientation', [
                    'portrait' => 'Portrait',
                    'landscape' => 'Landscape'
                ]),
                'legacy_product_id' => [
                    'type' => 'text',
                    'readonly' => true
                ],
                'year' => [
                    'type' => 'text',
                    'readonly' => true
                ]
            ]
        );
        $formFields = array_merge(
            $formFields,
            [
                // 'relatedProducts.related_product_id' => [
                //     'label' => 'Related Products',
                //     'type' => 'select',
                //     'multiple' => TRUE,
                //     'description' => 'Manually define which items should be shown as related.<br><br>ctrl+click/cmd+click to select multiple',
                //     'options' => $shopifyLib->products()->pluck('name', 'id')
                // ],
                'generate_assets' => [
                    'type' => 'boolean',
                    'label' => 'Generate Assets',
                    'description' => 'Kick off the process to generate artwork example images',
                ]
            ]
        );

        return $formFields;
    }
}
