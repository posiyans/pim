const User = {
  path: '/user',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    { path: 'list', component: () => import('src/Modules/User/pages/ListUser/index.vue') }
  ]
}

export default User
