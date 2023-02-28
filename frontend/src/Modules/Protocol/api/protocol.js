import request from 'src/utils/request'

export function fetchList(params) {
  return request({
    url: '/api/protocol/list',
    method: 'get',
    params
  })
}

export function fetchProtokol(query) {
  return request({
    url: '/api/protokol/' + query,
    method: 'get'
  })
}

export function downloadProtokol(query) {
  return request({
    url: '/api/protokol/export/' + query,
    method: 'get',
    responseType: 'blob'
  })
}

export function publishProtokol(data) {
  return request.post(
    '/api/protokol',
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

export function protokolToArchiv(protokol) {
  return request.put('/api/protokol/' + protokol,
    { type: 'protokolToArchiv' })
}

export function updateProtokol(protokol) {
  return request.put('/api/protokol/' + protokol.id,
    {
      type: 'protokolUpdate',
      protokol: protokol
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
