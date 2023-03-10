<template>
  <q-layout view="hHh Lpr lFf">
    <PrimaryHeader />

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      :width="200"
      bordered
    >
      <q-list>
        <MenuItems v-for="item in showMenu" :key="item.title" :item="item" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <div class="q-pa-md">
        <router-view />
      </div>
    </q-page-container>
  </q-layout>
</template>

<script>
import { computed, defineComponent } from 'vue'
import MenuItems from './components/MenuItems/index.vue'
import PrimaryHeader from 'src/layouts/components/Header/index.vue'
import { useStore } from 'vuex'

const linksList = [
  {
    title: 'Главная',
    icon: 'school',
    link: '/',
    roles: ['user']

  },
  {
    title: 'Задачи',
    icon: 'code',
    link: '/task/list',
    roles: ['user']
  },
  {
    title: 'Протоколы',
    icon: 'record_voice_over',
    link: '/protocol/list',
    roles: ['admin']
  },
  {
    title: 'Календарь',
    icon: 'chat',
    link: '/calendar/show',
    roles: ['user']
  },
  {
    title: 'Пользователи',
    roles: ['admin', 'moderator'],
    icon: 'rss_feed',
    link: '/user/list',
  }
]

export default defineComponent({
  name: 'MainLayout',
  components: {
    MenuItems,
    PrimaryHeader

  },

  setup() {
    const store = useStore()
    const leftDrawerOpen = computed(() => {
      return store.state.header.leftDrawerOpen
    })

    const showMenu = computed(() => {
      return linksList.filter(item => {
        return item.roles.filter(x => store.state.user.info.roles.includes(x)).length > 0
      })
    })
    return {
      linksList,
      showMenu,
      leftDrawerOpen,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value
      }
    }
  }
})
</script>
