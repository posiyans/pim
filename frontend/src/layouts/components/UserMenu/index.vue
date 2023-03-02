<template>
  <div>
    <q-btn-dropdown
      color="white"
      no-caps
      flat
      :label="name.name"
    >
      <q-list>
        <q-item clickable v-close-popup @click="logout">
          <q-item-section>
            <q-item-label>Выйти</q-item-label>
          </q-item-section>
        </q-item>
      </q-list>
    </q-btn-dropdown>
  </div>
</template>

<script>
export default {
  computed: {
    name() {
      return this.$store.state.user.info
    }
  },
  methods: {
    logout() {
      this.$store.dispatch('user/userLogout')
        .then(() => {
          this.$router.push('/auth/login')
        })
    }
  }
}
</script>

<style scoped>

</style>
