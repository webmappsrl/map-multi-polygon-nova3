<?php

namespace Wm\MapMultiPolygonNova3;

use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class MapMultiPolygonNova3 extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'map-multi-polygon-nova3';
    public $zone = [];

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        parent::resolve($resource, $attribute = null);
        $this->zone = $this->geometryToGeojson($this->value);
        if (!is_null($this->zone)) {
            $this->withMeta(['geojson' => $this->zone['geojson']]);
            $this->withMeta(['center' => $this->zone['center']]);
        }
    }
    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return void
     */
    protected function fillAttributeFromRequest(
        NovaRequest $request,
        $requestAttribute,
        $model,
        $attribute
    ) {
        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = $this->geojsonToGeometry($request[$requestAttribute]);
        }
    }

    public function geometryToGeojson($geometry)
    {
        $coords = null;
        if (!is_null($geometry)) {
            $g = DB::select("SELECT st_asgeojson('$geometry') as g")[0]->g;
            $c = json_decode(DB::select("SELECT st_asgeojson(ST_Centroid('$geometry')) as g")[0]->g);
            $coords['geojson'] = $g;
            $coords['center'] = [$c->coordinates[1], $c->coordinates[0]];
        }
        return $coords;
    }

    public function geojsonToGeometry($geojson)
    {
        $query = "SELECT ST_AsText(ST_GeomFromGeoJSON('$geojson')) As wkt";
        return DB::select($query)[0]->wkt;
    }
}
