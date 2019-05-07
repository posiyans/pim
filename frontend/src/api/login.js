import request from '@/utils/request'

export function loginByUsername(username, password, sms) {
  const data = {
    username,
    password,
    sms
  }
  return request({
    url: '/login/login',
    method: 'post',
    data
  })
}

export function logout() {
  return request({
    url: '/login/logout',
    method: 'post'
  })
}

export function getUserInfo(id) {
  return request({
    url: '/user/' + id,
    method: 'get'
  })
}

