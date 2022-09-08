Nova.booting((Vue, router, store) => {
  Vue.component(
    'index-map-multi-polygon-nova3',
    require('./components/IndexField').default
  )
  Vue.component(
    'detail-map-multi-polygon-nova3',
    require('./components/DetailField').default
  )
  Vue.component(
    'form-map-multi-polygon-nova3',
    require('./components/FormField').default
  )
  Vue.component(
    'wm-map-multi-polygon-nova3',
    require('./components/mapComponent').default
  )
})