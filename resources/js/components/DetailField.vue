<template>
    <panel-item :field="field">
        <template #value>
      <wm-map-multi-polygon-nova3 :field="field"></wm-map-multi-polygon-nova3>
    </template>
    </panel-item>
</template>

<script>
export default {
    props: ['index','resource', 'resourceName', 'resourceId', 'field'],
}
</script>
