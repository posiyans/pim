import request from 'src/utils/request'


export function downloadFile(params) {
  return request({
    url: '/api/file/download',
    method: 'get',
    responseType: 'blob',
    params
  })
}

export function deleteFile(params) {
  return request({
    url: '/api/file/delete',
    method: 'get',
    params
  })
}


export function uploadFile(formData) {
  return request.post(
    '/api/file/upload',
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
}


