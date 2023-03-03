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
      v-show="imagecropperShow"
      :width="400"
      :params="{ 'id': id }"
      :height="400"
      imgFormat="jpg"
      field="avatar"
      :url="url"
      lang-type="ru"
      @close="close"
      style="z-index: 20000;"
      with-credentials
      @crop-upload-success="cropSuccess"
    />
  </div>
</template>

<script>
import ImageCropper from 'vue-image-crop-upload'
import AvatarById from 'src/Modules/User/components/AvatarById/index.vue'

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
    url() {
      return '/avatar/upload'
    },
    key() {
      return this.$store.state.avatar.key
    }
  },
  methods: {
    cropSuccess() {
      this.imagecropperShow = false
      this.$store.commit('avatar/incrementKey')
    },
    close() {
      this.imagecropperShow = false
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
