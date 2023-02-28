<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar>
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>
          Quasar App
        </q-toolbar-title>

        <MyInfo />
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      :width="200"
      bordered
    >
      <q-list>
        <MenuItems v-for="item in linksList" :key="item.title" :item="item" />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { defineComponent, ref } from 'vue'
import MyInfo from 'src/components/MyInfo/index.vue'
import MenuItems from './components/MenuItems/index.vue'

const linksList = [
  {
    title: 'Главная',
    icon: 'school',
    link: '/'
  },
  {
    title: 'Задачи',
    icon: 'code',
    link: '/tasks/list'
  },
  {
    title: 'Календарь',
    icon: 'chat',
    link: '/calendar'
  },
  {
    title: 'Протоколы',
    icon: 'record_voice_over',
    link: '/protokol'
  },
  {
    title: 'Пользователи',
    roles: ['admin', 'moderator'],
    icon: 'rss_feed',
    link: '/users'
  }
]

export default defineComponent({
  name: 'MainLayout',

  components: {
    MyInfo,
    MenuItems
  },

  setup() {
    const leftDrawerOpen = ref(false)

    return {
      linksList,
      leftDrawerOpen,
      toggleLeftDrawer() {
        leftDrawerOpen.value = !leftDrawerOpen.value
      }
    }
  }
})
</script>
