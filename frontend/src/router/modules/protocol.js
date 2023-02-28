const Protocol = {
  path: '/protocol',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    {
      path: 'show/:id',
      component: () => import('src/Modules/Protocol/pages/ShowProtocol/index.vue')
    },
    {
      path: 'edit/:id',
      component: () => import('src/Modules/Protocol/pages/EditProtocol/index.vue')
    },
    {
      path: 'list',
      component: () => import('src/Modules/Protocol/pages/ListProtocol/index.vue')
    },
    {
      path: 'add',
      component: () => import('src/Modules/Protocol/pages/AddProtocol/index.vue')
    }
  ]
}
export default Protocol