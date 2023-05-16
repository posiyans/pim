<template>
  <div>
    <ShowTaskInfo :task-id="route.params.id" :key="key" />
  </div>
</template>

<script>
import { setReportAsRead } from 'src/Modules/Task/api/task.js'
import ShowTaskInfo from 'src/Modules/Task/components/ShowTaskInfo/index.vue'
import { defineComponent, onMounted, onUnmounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

export default defineComponent({
  components: {
    ShowTaskInfo
  },
  props: {},
  setup() {
    let timer = null
    const route = useRoute()
    const key = ref(1)
    onMounted(() => {
      timer = setTimeout(() => {
        setReportAsRead({ id: route.params.id })
      }, 2000)
    })
    onUnmounted(() => {
      if (timer) {
        clearTimeout(timer)
      }
    })
    watch(
      () => route.params.id,
      () => {
        key.value++
      }
    )
    return {
      route,
      key
    }
  }
})
</script>

<style scoped>

</style>
