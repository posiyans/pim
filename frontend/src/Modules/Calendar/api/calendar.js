import request from 'src/utils/request'


export function getTasksForCalendar(params) {
  return request({
    url: '/api/calendar/get-tasks',
    method: 'get',
    params
  })
}
