import request from 'src/utils/request'


export function downloadFile(params) {
  return request({
    url: '/api/file/download',
    method: 'get',
    responseType: 'blob',
    params
  })
}
