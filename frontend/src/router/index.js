import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/views/layout/Layout'

/** note: sub-menu only appear when children.length>=1
 *  detail see  https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
 **/

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirect in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    roles: ['admin','editor']    will control the page roles (you can set multiple roles)
    title: 'title'               the name show in sub-menu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar
    noCache: true                if true, the page will no be cached(default is false)
    breadcrumb: false            if false, the item will hidden in breadcrumb(default is true)
    affix: true                  if true, the tag will affix in the tags-view
  }
**/
export const constantRouterMap = [
  {
    path: '/redirect',
    component: Layout,
    hidden: true,
    children: [
      {
        path: '/redirect/:path*',
        component: () => import('@/views/redirect/index')
      }
    ]
  },
  {
    path: '/login',
    component: () => import('@/views/login/index'),
    hidden: true
  },
  {
    path: '/auth-redirect',
    component: () => import('@/views/login/authredirect'),
    hidden: true
  },
  {
    path: '/404',
    component: () => import('@/views/errorPage/404'),
    hidden: true
  },
  {
    path: '/401',
    component: () => import('@/views/errorPage/401'),
    hidden: true
  },
  {
    path: '/task',
    component: Layout,
    hidden: true,
    children: [
      {
        path: 'info/:taskId',
        component: () => import('@/views/tasks/tasksInfo'),
        name: 'TaskInfo',
        meta: { title: 'Задача', icon: 'dashboard', noCache: true, affix: true }
      }
    ]
  },
  {
    path: '/protokol',
    component: Layout,
    hidden: true,
    children: [
      {
        path: 'show/:protokolId',
        component: () => import('@/views/protokols/protokolInfo'),
        name: 'ProtokolInfo',
        meta: { title: 'Протокол', icon: 'dashboard', noCache: true, affix: true, roles: ['admin'] }
      },
      {
        path: 'edit/:protokolId',
        component: () => import('@/views/protokols/protokolEdit'),
        name: 'ProtokolEdit',
        meta: { title: 'Протокол', icon: 'dashboard', noCache: true, affix: true, roles: ['admin'] }
      }
    ]
  },
  {
    path: '',
    component: Layout,
    redirect: 'dashboard',
    children: [
      {
        path: 'dashboard',
        component: () => import('@/views/dashboard/index'),
        name: 'Dashboard',
        meta: { title: 'start', icon: 'dashboard', noCache: true, affix: true }
      }
    ]
  },
  {
    path: '/tasks',
    component: Layout,
    redirect: '/tasks/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/tasks/tasksList'),
        name: 'Tasks',
        meta: { title: 'tasks', icon: 'dashboard', noCache: true, affix: true }
      }
    ]
  },
  {
    path: '/calendar',
    component: Layout,
    redirect: '/calendar/index',
    children: [
      {
        path: 'index',
        component: () => import('@/views/calendar/index'),
        name: 'Calendar',
        meta: { title: 'Календарь', icon: 'dashboard', noCache: true, affix: true }
      }
    ]
  }
]

export default new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})

export const asyncRouterMap = [
  {
    path: '/protokols',
    component: Layout,
    redirect: '/protokols/index',
    meta: { title: 'prtotokols', icon: 'dashboard', noCache: true, affix: true, roles: ['admin'] },
    children: [
      {
        path: 'index',
        component: () => import('@/views/protokols/protokolList'),
        name: 'Protokols',
        meta: { title: 'prtotokols', icon: 'dashboard', noCache: true, affix: true, roles: ['admin'] }
      },
      {
        path: 'add',
        component: () => import('@/views/protokols/protokolAdd'),
        name: 'ProtokolAdd',
        hidden: true,
        meta: { title: 'protokolAdd', icon: 'dashboard', noCache: true, affix: true, roles: ['admin'] }
      }
    ]
  },
  {
    path: '/users',
    component: Layout,
    redirect: '/users/index',
    meta: { title: 'users', icon: 'peoples', noCache: true, affix: true, roles: ['admin'] },
    children: [
      {
        path: 'index',
        component: () => import('@/views/users/userList'),
        name: 'Users',
        meta: { title: 'users', icon: 'peoples', noCache: true, affix: true, roles: ['admin'] }
      }
    ]
  },
  {
    path: '/taskedit',
    component: Layout,
    redirect: '/taskedit/index',
    meta: { title: 'Edit', noCache: true, affix: true, roles: ['admin'] },
    hidden: true,
    children: [
      {
        path: 'index/:taskId',
        component: () => import('@/views/tasks/tasksEdit'),
        name: 'TaskEdit',
        meta: { title: 'Задача', icon: 'dashboard', noCache: true, affix: true }
      }
    ]
  },
  { path: '*', redirect: '/404', hidden: true }
]
