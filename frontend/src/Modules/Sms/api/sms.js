import request from 'src/utils/request'

export function getBalance() {
  return request({
    url: '/api/sms/balance/get',
    method: 'get'
  })
}
