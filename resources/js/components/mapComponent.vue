
<template>
    <div id="container">
        <div :id="mapRef" class="wm-map"></div>
    </div>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import "leaflet/dist/leaflet.css";
import L from "leaflet";
const DEFAULT_TILES = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const DEFAULT_ATTRIBUTION = '<a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery (c) <a href="https://www.mapbox.com/">Mapbox</a>';
const DEFAULT_CENTER = [0, 0];
const polygonOption = {
    fillColor: '#f03',
    fillOpacity: 0.5,
};
let mapDiv = null;
let polygon = null;
export default {
    name: "MapPolygon",
    mixins: [FormField, HandlesValidationErrors],
    props: ['field', 'geojson'],
    data() {
        return {
            mapRef: `mapContainer-${Math.floor(Math.random() * 10000 + 10)}`,
            uploadFileContainer: 'uploadFileContainer',
        }
    },
    methods: {
        initMap() {
            
            setTimeout(() => {
                const center = this.field.center ?? this.center ?? DEFAULT_CENTER;
                const polygonGeojson = this.field.geojson;
                mapDiv = L.map(this.mapRef).setView(center, 13);

                L.tileLayer(
                    this.field.tiles ?? DEFAULT_TILES,
                    {
                        attribution: this.field.attribution ?? DEFAULT_ATTRIBUTION,
                        maxZoom: 15,
                        minZoom: 11,
                        id: "mapbox/streets-v11",
                    }
                ).addTo(mapDiv);

                if (polygonGeojson != null) {
                    polygon = L.geoJson(JSON.parse(polygonGeojson), {
                        style: polygonOption
                    }).addTo(mapDiv);
                    mapDiv.fitBounds(polygon.getBounds());
                }

            }, 300);
        }
    },
    watch: {
        geojson: (gjson) => {
            if (polygon != null) {
                mapDiv.removeLayer(polygon);
            }
            if (gjson != null) {
                polygon = L.geoJSON(gjson, polygonOption).addTo(mapDiv);
                mapDiv.fitBounds(polygon.getBounds());
            }
        }
    },
    mounted() {
        this.initMap();
    },
};
</script>
