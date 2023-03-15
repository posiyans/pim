<template>
  <div>
    {{ showTitle }}
  </div>
</template>

<script>
import { getTypeProtocol } from 'src/Modules/Protocol/api/protocol'

export default {
  props: {
    type: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      protokolTypeOptions: []
    }
  },
  computed: {
    showTitle() {
      const pr = this.protokolTypeOptions.find(item => item.id === this.type)
      if (pr) {
        return pr.name
      }
      return ''
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      getTypeProtocol()
        .then(res => {
          this.protokolTypeOptions = res.data
        })
    },
  }
}
</script>

<style scoped>

</style>
