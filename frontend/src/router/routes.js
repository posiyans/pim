import Auth from './modules/auth'
import Tasks from './modules/task.js'
import Protocol from './modules/protocol.js'
import User from './modules/user.js'
import Calendar from './modules/calendar.js'
import Setting from './modules/setting.js'

const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('src/pages/PrimaryPage/index.vue') }
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  { path: '/page/403', component: () => import('src/pages/Errors/403.vue') },
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  },
  Auth,
  Tasks,
  Protocol,
  User,
  Calendar,
  Setting
]

export default routes
