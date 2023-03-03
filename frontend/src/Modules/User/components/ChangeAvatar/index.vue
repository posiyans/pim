<template>
  <div>
    <div :key="key" @click="imagecropperShow=true">
      <slot>
        <div class="relative-position" :style="'width:' + size">
          <AvatarById :id="id" :size="size" />
          <div class="absolute-top-right bg-grey q-pa-xs o-80 avatar-edit-btn">
            <q-btn dense icon="edit" size="sm" color="white" flat />
          </div>
        </div>
      </slot>
    </div>
    <image-cropper
      v-model="imagecropperShow"
      :key="key"
      :width="400"
      :params="{ 'id': id }"
      :height="400"
      :headers="headers"
      imgFormat="jpg"
      field="avatar"
      :url="url"
      lang-type="ru"
      @update:model-value="close"
    />
  </div>
</template>

<script>
import ImageCropper from 'vue-image-crop-upload'
import AvatarById from 'src/Modules/User/components/AvatarById/index.vue'
import { SessionStorage } from 'quasar'

export default {
  components: {
    ImageCropper,
    AvatarById
  },
  props: {
    id: {
      type: [String, Number],
      required: true
    },
    size: {
      type: String,
      default: '150px'
    }
  },
  data() {
    return {
      imagecropperShow: false
    }
  },
  computed: {
    headers() {
      const token = SessionStorage.getItem('UserToken') || ''
      return {
        Authorization: 'Bearer ' + token
      }
    },
    url() {
      return process.env.API + '/api/user/avatar-upload'
    },
    key() {
      return this.$store.state.avatar.key
    }
  },
  methods: {
    cropSuccess() {
      this.imagecropperShow = false
      this.$store.commit('avatar/increment')
    },
    close() {
      console.log('close')
      this.imagecropperShow = false
      this.$store.commit('avatar/increment')
    }
  }
}
</script>

<style scoped lang="scss">
.avatar-edit-btn {
  opacity: 0.2;

  &:hover {
    opacity: 1;

  }
}
</style>
