import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/task/list',
    method: 'get',
    params: query
  })
}

export function fetchListForToday(query) {
  return request({
    url: '/task/list',
    method: 'get',
    params: query
  })
}

export function fetchTask(id, query = false) {
  return request({
    url: '/task/info/' + id,
    method: 'get',
    params: query
  })
}

export function fetchTaskStatistic() {
  return request({
    url: '/task/statistic',
    method: 'get'
    // params: query
  })
}

export function addReport(formData) {
  return request.post(
    '/report',
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }
  )
}

export function taskDone(executor) {
  return request.put('/report/' + executor.id,
    { item: executor })
}

export function delReport(id) {
  return request.delete('/report/' + id)
}

export function taskToArchiv(task) {
  return request.put('/task/' + task,
    { type: 'taskToArchiv' })
}

export function updateTask(task) {
  return request.put('/task/' + task.id,
    { type: 'updateTask',
      task: task
    })
}

export function fetchProtokol(query) {
  return request({
    url: '/protokol/source',
    method: 'get',
    params: query
  })
}

export function downloadReport(query) {
  return request({
    url: '/file/' + query,
    method: 'get',
    responseType: 'blob'
  })
}

export function transferDate(task) {
  return request.put('/task/' + task.id,
    { type: 'transferDate',
      task: task
    })
}
