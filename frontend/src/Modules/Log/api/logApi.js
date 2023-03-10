import request from 'src/utils/request'


export function getTaskHistory(params) {
  return request({
    url: '/api/log/task/get',
    method: 'get',
    params
  })
}

export function getProtocolHistory(params) {
  return request({
    url: '/api/log/protocol/get',
    method: 'get',
    params
  })
}
