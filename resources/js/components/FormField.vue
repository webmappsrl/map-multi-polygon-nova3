
<template>
  <default-field :field="field">
    <template #field>
      <div class="mt-1 md:mt-0 pb-5 md:w-3/5 w-full md:py-5">
        <input :id="field.name" type="file" :class="errorClasses" :placeholder="field.name" @change="previewFiles"
          accept=".geojson,.gpx,.kml" />
        <p v-if="hasError" class="my-2 text-danger">
          {{  firstError  }}
        </p>
      </div>
      <wm-map-multi-polygon-nova3 :field="field" :attribution="attribution" :geojson="geojson">
      </wm-map-multi-polygon-nova3>
    </template>
  </default-field>
</template>
<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import * as t from '@mapbox/togeojson'

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ['field'],
  methods: {
    fill(formData) {
      let geometry = null;
      try {
        geometry = this.geojson.features[0].geometry;
      } catch (error) {
        console.error(error);
      }
      if (geometry != null) {
        if (geometry.type === 'Polygon') {
          geometry.type = 'MultiPolygon'
          geometry.coordinates = [geometry.coordinates]
        }
        if (geometry.type === 'LineString') {
          geometry.type = 'MultiPolygon'
          geometry.coordinates = [geometry.coordinates]
        }
        formData.append(this.field.attribute, JSON.stringify(geometry))
      }
    },
    previewFiles(event) {
      const reader = new FileReader();
      let fileName = event.target.files[0].name || '';
      reader.onload = (event) => {
        let res = event.target.result;
        if (fileName.indexOf('gpx') > -1) {
          const parser = new DOMParser().parseFromString(res, 'text/xml')
          res = t.gpx(parser);
        } else
          if (fileName.indexOf('kml') > -1) {
            const parser = new DOMParser().parseFromString(res, 'text/xml')
            res = t.kml(parser);
          } else {
            res = JSON.parse(res);
          }
        this.geojson = res;
      };
      reader.readAsText(event.target.files[0]);
    }
  },
  data() {
    return { geojson: null }
  }
};
</script>
