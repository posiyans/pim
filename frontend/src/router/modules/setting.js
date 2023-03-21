const Setting = {
  path: '/setting',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    {
      path: 'info',
      component: () => import('src/Modules/Setting/pages/PrimarySetting/index.vue'),
      meta: {
        title: 'Настройки',
        roles: ['moderator', 'admin']
      }
    }
  ]
}

export default Setting
