<template>
  <div class="no-data" :style="cls">
    <img class="default-img" src="@/assets/nodatadefault.png">
    <p v-if="text">{{ text }}</p>
    <slot v-else />
  </div>
</template>
<script>
export default {
  name: 'NoData',
  props: {
    text: {
      type: String,
      default: ''
    }
  },
  computed: {
    cls() {
      return {
        fontSize: '14px'
      }
    }
  }
}
</script>
<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus" scoped>
@import '../../stylus/color.styl'
.no-data
  width: 100%
  height: 100%
  background-color: $color-white
  font-size: 16px
  display: flex
  flex-direction: column
  align-items: center
  justify-content: center
  min-height: 450px
  @media (max-width: 450px)
    min-height: 280px
  > p
    margin-top: 20px
    color: $color-light-grey
  .default-img
    width: 240px
    height: 190px
</style>

