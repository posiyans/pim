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
          Задачник
        </q-toolbar-title>
        <ShowBalance />
        <UserMenu />
      </q-toolbar>
    </q-header>

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
import { computed, defineComponent, ref } from 'vue'
import MenuItems from './components/MenuItems/index.vue'
import ShowBalance from 'src/Modules/Sms/Components/ShowBalance/index.vue'
import UserMenu from 'src/layouts/components/UserMenu/index.vue'
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
    title: 'Календарь',
    icon: 'chat',
    link: '/calendar/show',
    roles: ['user']
  },
  {
    title: 'Протоколы',
    icon: 'record_voice_over',
    link: '/protocol/list',
    roles: ['admin']
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
    ShowBalance,
    UserMenu
  },

  setup() {
    const leftDrawerOpen = ref(false)
    const store = useStore()
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
