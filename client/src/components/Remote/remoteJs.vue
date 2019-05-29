<template />

<script>
export default {
  name: 'RemoteJs',
  props: {
    src: { type: String, required: true }
  },

  render: function(createElement) {
    return createElement('script', { attrs: { type: 'text/javascript', src: this.src }})
  }
}
</script>

