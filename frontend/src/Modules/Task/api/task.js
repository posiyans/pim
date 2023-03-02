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

export function fetchTask(params) {
  return request({
    url: '/api/task/get',
    method: 'get',
    params
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
    '/api/report/create',
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }
  )
}

export function setTaskDone(data) {
  return request({
    url: '/api/task/report/set-done',
    method: 'post',
    data
  })
}

export function delReport(id) {
  return request.delete('/api/report/' + id)
}

export function taskToArchiv(params) {
  return request({
    url: '/api/task/move-to-archive',
    method: 'get',
    params
  })
}

export function updateTask(data) {
  return request({
    url: '/api/task/update',
    method: 'post',
    data
  })
}

export function fetchProtokol(query) {
  return request({
    url: '/api/protokol/source',
    method: 'get',
    params: query
  })
}

export function downloadReport(params) {
  return request({
    url: '/api/file/download',
    method: 'get',
    responseType: 'blob',
    params
  })
}

/**
 * перенести дату выполнения
 *
 * @param data
 * @returns {Promise<AxiosResponse<any>>|*}
 */
export function transferDate(data) {
  return request({
    url: '/api/task/move-date-execution',
    method: 'post',
    data
  })
}
