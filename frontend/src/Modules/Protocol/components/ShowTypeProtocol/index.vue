<template>
  <div>
    {{ showTitle }}
  </div>
</template>

<script>
import { useProtocol } from 'src/Modules/Protocol/api/useProtocol.js'

export default {
  props: {
    type: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      protokolTypeOptions: [
        { key: 'psd', display_name: 'Протоколы СД' },
        { key: 'skype', display_name: 'Скайп Протоколы' },
        { key: 'year', display_name: 'Готодовые протоколы' }
      ]
    }
  },
  computed: {
    showTitle() {
      const pr = this.protokolTypeOptions.find(item => item.key === this.type)
      if (pr) {
        return pr.display_name
      }
      return ''
    }
  },
  mounted() {
    const { typeProtocol } = useProtocol()
    this.protokolTypeOptions = typeProtocol
  },
}
</script>

<style scoped>

</style>
