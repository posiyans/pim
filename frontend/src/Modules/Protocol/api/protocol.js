import request from 'src/utils/request'

export function parseProtocol(formData) {
  return request.post(
    '/api/protocol/parse-file',
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

export function getTypeProtocol() {
  return request({
    url: '/api/protocol/get-type',
    method: 'get',
  })
}

export function createTypeProtocol(data) {
  return request({
    url: '/api/protocol/create-type',
    method: 'post',
    data
  })
}