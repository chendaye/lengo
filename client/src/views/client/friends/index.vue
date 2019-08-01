<template>
  <div id="friends" v-loading="loading">
    <div v-for="(item, index) in friends" :key="index" class="type-wrap">
      <p>{{ item.name }}</p>
      <div class="friends-wrap">
        <a v-for="(friend, index) in item.list" :key="index" :href="friend.link" target="_blank">
          {{ friend.name }}
        </a>
      </div>
    </div>
  </div>
</template>

<script>
import crud from "@/api/crud";
const wtuCrud = crud.factory("blog", "client");
import {
  mapActions
} from 'vuex'

export default {
  name: 'Friends',
  components: {
  },
  data() {
    return {
      friends: [],
      loading: false
    }
  },
  created() {
    this.loading = true
    wtuCrud.get('linkList', {})
      .then((data) => {
        if (data.status === 200) {
          this.friends = data.data;
          this.loading = false;
        }
      })
      .catch(() => {
        this.friends = []
        this.loading = false
      })
  },
  methods: {
    ...mapActions([
      'getBlogFriendsList'
    ])
  }
}
</script>

<style lang="stylus" scoped>
@import '../../../stylus/color.styl';
#friends
  position: relative
  padding: 30px 10px
  max-width: 940px
  margin: 0 auto
  animation: show .8s
  .type-wrap
    > p
      border-left: 4px solid #999999
      padding: 5px 10px
      font-weight: bold
      font-size: 16px
    .friends-wrap
      padding: 10px
      display: flex
      flex-direction: row
      flex-wrap: wrap
      font-size: 14px
      a
        color: $color-white
        padding: 5px 10px
        background-color: $color-main
        border-radius: 5px
        margin: 5px
        transition: background-color .3s
        &:hover
          background-color: lighten($color-main, 20%)

@keyframes show {
  from {
    margin-top: -10px;
    opacity: 0;
  }
  to {
    margin-top: 0px;
    opacity: 1;
  }
}
</style>
