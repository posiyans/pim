const Tasks = {
  path: '/tasks',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    { path: 'list', component: () => import('src/Modules/Task/pages/TasksList/index.vue') }
  ]
}
export default Tasks