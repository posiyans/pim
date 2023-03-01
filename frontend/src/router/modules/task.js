const Tasks = {
  path: '/task',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    {
      path: 'list',
      component: () => import('src/Modules/Task/pages/TasksList/index.vue')
    },
    {
      path: 'show/:id',
      component: () => import('src/Modules/Task/pages/ShowTask/index.vue')
    }
  ]
}
export default Tasks