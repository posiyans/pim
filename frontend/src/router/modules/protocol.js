const Protocol = {
  path: '/protocol',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    {
      path: 'show/:id',
      component: () => import('src/Modules/Protocol/pages/ShowProtocol/index.vue'),
      meta: {
        title: 'Протокол'
      }
    },
    {
      path: 'edit/:id',
      component: () => import('src/Modules/Protocol/pages/EditProtocol/index.vue'),
      meta: {
        title: 'Протокол'
      }
    },
    {
      path: 'list',
      component: () => import('src/Modules/Protocol/pages/ListProtocol/index.vue'),
      meta: {
        title: 'Протоколы'
      }
    },
    {
      path: 'add',
      component: () => import('src/Modules/Protocol/pages/AddProtocol/index.vue'),
      meta: {
        title: 'Добавить протокол'
      }
    }
  ]
}
export default Protocol