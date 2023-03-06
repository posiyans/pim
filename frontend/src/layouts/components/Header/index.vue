<template>
  <q-header elevated>
    <q-toolbar>
      <q-btn
        flat
        dense
        round
        icon="menu"
        aria-label="Menu"
        @click="$store.commit('header/toggleLeftDrawer')"
      />

      <q-toolbar-title>
        {{ headTitle }}
      </q-toolbar-title>
      <CountNoReadReport />
      <UserMenu />
    </q-toolbar>
  </q-header>
</template>

<script>
import UserMenu from 'src/layouts/components/UserMenu/index.vue'
import CountNoReadReport from 'src/Modules/Task/components/CountNoReadReport/index.vue'

export default {
  components: {
    UserMenu,
    CountNoReadReport
  },
  computed: {
    headTitle() {
      return this.$store.state.header.title || 'Задачник'
    }
  }
}
</script>

<style scoped>

</style>
