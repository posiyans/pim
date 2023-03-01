import request from 'src/utils/request'


export function test(params) {
  return request({
    url: '/api/test',
    method: 'get',
    params
  })
}


export function uploadProtocol(formData) {
  return request.post(
    '/api/test',
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
}

export function fetchList(params) {
  return request({
    url: '/api/protocol/list',
    method: 'get',
    params
  })
}

export function fetchProtokol(params) {
  return request({
    url: '/api/protocol/get',
    method: 'get',
    params
  })
}

export function downloadProtocol(params) {
  return request({
    url: '/api/protocol/get-file',
    method: 'get',
    params,
    responseType: 'blob'
  })
}

export function publishProtokol(data) {
  return request.post(
    '/api/protocol/create',
    data,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
}

export function fetchArticle(id) {
  return request({
    url: '/api/article/detail',
    method: 'get',
    params: { id }
  })
}

export function protokolToArchiv(data) {
  return request({
    url: '/api/protocol/move-to-archive',
    method: 'post',
    data
  })
}

export function updateProtokol(data) {
  return request({
    url: '/api/protocol/update',
    method: 'post',
    data
  })
}

export function uploadPartitionFile(id, formData) {
  return request.post(
    '/api/partition',
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
}
