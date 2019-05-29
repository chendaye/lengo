<template>
  <body />
</template>

<script>
export default {
  name: 'RemoteCss',
  props: {
    href: { type: String, required: true }
  },

  render: function(createElement) {
    return createElement('link', { attrs: { type: 'text/css', rel: 'stylesheet', href: this.href }})
  }
}
</script>

