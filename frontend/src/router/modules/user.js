const User = {
  path: '/user',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    {
      path: 'list',
      component: () => import('src/Modules/User/pages/ListUser/index.vue'),
      meta: {
        title: 'Пользователи',
        roles: ['moderator']
      }
    },
    {
      path: 'profile',
      component: () => import('src/Modules/User/pages/MyProfile/index.vue'),
      meta: {
        title: 'Профиль',
        roles: ['user']
      }
    }
  ]
}

export default User
