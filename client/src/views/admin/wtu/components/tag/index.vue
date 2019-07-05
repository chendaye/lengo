<template>
  <div>
    <tag :size-val="size" :id-val="id" />
  </div>
</template>

<script>
import tag from '@/components/Tag/index'

export default {
  components: {
    tag
  },
  data() {
    return {
      size: 'mini',
      id: 1
    };
  },
  methods: {

  }
};
</script>
