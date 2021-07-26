<?php namespace App\Http\Terranet\Administrator\Actions;

use App\Http\Terranet\Administrator\Traits\ArtworksActions;
use Terranet\Administrator\Services\Actions;

class Artworks extends Actions
{
    use ArtworksActions;

    /**
     * activate selected items
     *
     * @param       $eloquent
     * @param array $collection
     * @global
     * @return $this
     */
    public function removeSelected(\Illuminate\Database\Eloquent\Model $eloquent, array $collection = [])
    {
        return $eloquent->newQueryWithoutScopes()
            ->whereIn('id', $collection)
            ->delete();
    }
}