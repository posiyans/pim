import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/protokol',
    method: 'get',
    params: query
  })
}

export function fetchProtokol(query) {
  return request({
    url: '/protokol/' + query,
    method: 'get'
  })
}
export function downloadProtokol(query) {
  return request({
    url: '/protokol/export/' + query,
    method: 'get',
    responseType: 'blob'
  })
}

export function publishProtokol(data) {
  return request.post(
    '/protokol',
    data,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
}

export function fetchArticle(id) {
  return request({
    url: '/article/detail',
    method: 'get',
    params: { id }
  })
}

export function protokolToArchiv(protokol) {
  return request.put('/protokol/' + protokol,
    { type: 'protokolToArchiv' })
}

export function updateProtokol(protokol) {
  return request.put('/protokol/' + protokol.id,
    { type: 'protokolUpdate',
      protokol: protokol
    })
}

export function uploadPartitionFile(id, formData) {
  return request.post(
    '/partition',
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
}
