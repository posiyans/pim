const Auth = {
  path: '/auth',
  component: () => import('layouts/ClearLayout.vue'),
  children: [
    { path: 'login', component: () => import('src/pages/Auth/Login/index.vue') }
  ]
}

export default Auth
