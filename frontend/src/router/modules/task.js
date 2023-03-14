const Tasks = {
  path: '/task',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    {
      path: 'list',
      component: () => import('src/Modules/Task/pages/TasksList/index.vue'),
      meta: {
        title: 'Задачи'
      }
    },
    {
      path: 'show/:id',
      component: () => import('src/Modules/Task/pages/ShowTask/index.vue')
    },
    {
      path: 'edit/:id',
      component: () => import('src/Modules/Task/pages/EditTask/index.vue'),
      meta: {
        title: 'Редактировать задачу',
        roles: ['moderator']
      }
    }
  ]
}
export default Tasks