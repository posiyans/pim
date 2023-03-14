<template>
  <q-img v-if="url" :src="url" spinner-color="white">
    <template v-slot:loading>
      <q-spinner-gears color="secondary" size="0.5em" />
    </template>
  </q-img>
  <q-spinner-gears v-else color="secondary" size="1em" />
</template>

<script>

import { getUserAvatar } from 'src/Modules/User/api/user'

export default {
  props: {
    id: {
      type: [String, Number],
      required: true
    },
  },
  data() {
    return {
      url: null,
      keyCache: '/user_avatar_'
    }
  },
  computed: {
    key() {
      return this.$store.state.avatar.key
    },
    kk() {
      return this.keyCache + this.id + '_' + this.key
    }
  },
  mounted() {
    this.getData()
  },
  watch: {
    async key() {
      await caches.delete('avatar-cache');
      this.getData()
    }
  },
  methods: {
    async getData() {
      const avatarCache = await caches.open('avatar-cache');
      const options = {
        ignoreSearch: true,
        ignoreMethod: true,
        ignoreVary: true
      };
      const res = avatarCache.match(this.kk, options);
      res
        .then((response) => {
          console.log(response)
          if (response) {
            const reader = response.body.getReader();
            return new ReadableStream({
              start(controller) {
                return pump()

                function pump() {
                  return reader.read().then(({ done, value }) => {
                    if (done) {
                      controller.close()
                      return
                    }
                    controller.enqueue(value);
                    return pump()
                  });
                }
              }
            })
          } else {
            const data = {
              id: this.id,
              sail: this.sail
            }
            getUserAvatar(data)
              .then(res => {
                this.url = URL.createObjectURL(res.data)
                avatarCache.put(this.kk, new Response(res.data))
              })
          }
        })
        .then((stream) => new Response(stream))
        .then((response) => response.blob())
        .then((blob) => {
          return URL.createObjectURL(blob)
        })
        .then((url) => {
          this.url = url
        })
        .catch((err) => console.error(err));
    }
  }
}
</script>

<style scoped>

</style>
