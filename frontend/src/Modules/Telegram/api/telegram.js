import request from 'src/utils/request'

export function getLastUserFromTelegram() {
  return request({
    url: '/api/user/get-last-user-from-telegram',
    method: 'get'
  })
}
