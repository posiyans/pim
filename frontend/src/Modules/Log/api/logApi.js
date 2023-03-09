import request from 'src/utils/request'


export function getTaskHistory(params) {
  return request({
    url: '/api/log/task/get',
    method: 'get',
    params
  })
}
