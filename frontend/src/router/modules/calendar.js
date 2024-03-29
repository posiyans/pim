const Calendar = {
  path: '/calendar',
  component: () => import('layouts/MainLayout.vue'),
  children: [
    {
      path: 'show',
      component: () => import('src/Modules/Calendar/pages/ShowCalendar/index.vue'),
      meta: {
        title: 'Календарь'
      }
    }
  ]
}

export default Calendar
