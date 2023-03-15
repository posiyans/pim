<template>
  <div>
    <div v-for="task in list" :key="task.id" :class="task.arxiv  ? 'text-grey-5' : ''">
      <div class="row q-col-gutter-sm no-wrap">
        <div :class="done(task)">
          {{ partitionNumber }}.{{ task.number }}.
        </div>
        <div class="text-no-wrap">
          {{ task.executor }}
        </div>

        <div>
          -
        </div>
        <div>
          <div v-if="task.data_ispoln">
            <ShowTime :time="task.data_ispoln" format="DD.MM.YYYY" class="text-weight-bold" />
          </div>
          <div v-else class="text-teal">
            Тезис
          </div>
        </div>
        <div>
          -
        </div>
        <div v-html="task.text" />
        <div class="cursor-pointer" @click="$router.push('/task/show/' + task.id)">
          <q-icon name="east" color="primary" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ShowTime from 'components/ShowTime/index.vue'

export default {
  components: {
    ShowTime
  },
  props: {
    list: {
      type: Array,
      default: () => []
    },
    partitionNumber: {
      type: [String, Number],
      default: ''
    }
  },
  methods: {
    done(task) {
      const report = task.view_report
      let done = 0
      let not_performed = 0
      for (const item in report) {
        if (report[item].executor !== 0) {
          if (report[item].done == null) {
            not_performed++
          } else {
            done++
          }
        }
      }
      const total = done / (done + not_performed)
      return total === 1 ? 'text-teal-10' : 'text-red-10'
    }
  }
}
</script>

<style scoped>

</style>
