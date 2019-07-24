<template>
  <div id="m-footer">
    <div class="footer-wrap">
      <p>
        本博客已运行<span>{{ runningTime }}</span><span class="time-jump"></span>
      </p>
      <p>
        {{ blogInfo.sign }}/{{ blogInfo.blogName || '博客' }}
        Life is only inevitable, there is no chance.
      </p>
    </div>
  </div>
</template>

<script>
import {
  mapGetters
} from 'vuex'

export default {
  name: 'MFooter',
  components: {
  },
  data() {
    return {
      runningTime: 0
    }
  },
  computed: {
    ...mapGetters([
      'blogInfo'
    ])
  },
  created() {
    this.running()
  },
  methods: {
    running() {
      const startTime = new Date('2019/08/01 00:00:00')
      const timer = setInterval(() => {
        const time = new Date() - startTime
        const day = parseInt(time / 1000 / 60 / 60 / 24, 10)
        const hour = parseInt(time / 1000 / 60 / 60 % 24, 10)
        const minute = parseInt(time / 1000 / 60 % 60, 10)
        const second = parseInt(time / 1000 % 60, 10)
        this.runningTime = `${day}天${hour}小时${minute}分${second}秒`
      }, 1000)
    }
  }
}
</script>

<style lang="stylus" src="@/stylus/main.styl" scoped></style>
<style lang="stylus" scoped>
@import '../../../stylus/color.styl'
#m-footer
  position: relative
  height: 120px
  width: 100%
  background-color: $color-main
  .footer-wrap
    position: relative
    max-width: 1000px
    padding: 0 20px
    margin: 0 auto
    height: 120px
    font-size: 13px
    @media (max-width: 768px)
      font-size: 12px
    display: flex
    flex-direction: column
    justify-content: center
    color: #999999
    > p
      margin: 4px 0
      a
        color: rgba(59, 116, 241, .8)
        text-decoration: underline
</style>
