import request from 'src/utils/request'

export function fetchList(query) {
  return request({
    url: '/api/task/list',
    method: 'get',
    params: query
  })
}

export function fetchListForToday(query) {
  return request({
    url: '/api/task/list',
    method: 'get',
    params: query
  })
}

export function fetchTask(id, query = false) {
  return request({
    url: '/api/task/info/' + id,
    method: 'get',
    params: query
  })
}

export function fetchTaskStatistic() {
  return request({
    url: '/api/task/statistic',
    method: 'get'
    // params: query
  })
}

export function addReport(formData) {
  return request.post(
    '/api/report',
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }
  )
}

export function taskDone(executor) {
  return request.put('/api/report/' + executor.id,
    { item: executor })
}

export function delReport(id) {
  return request.delete('/api/report/' + id)
}

export function taskToArchiv(task) {
  return request.put('/api/task/' + task,
    { type: 'taskToArchiv' })
}

export function updateTask(task) {
  return request.put('/api/task/' + task.id,
    {
      type: 'updateTask',
      task: task
    })
}

export function fetchProtokol(query) {
  return request({
    url: '/api/protokol/source',
    method: 'get',
    params: query
  })
}

export function downloadReport(query) {
  return request({
    url: '/api/file/' + query,
    method: 'get',
    responseType: 'blob'
  })
}

export function transferDate(task) {
  return request.put('/api/task/' + task.id,
    {
      type: 'transferDate',
      task: task
    })
}
