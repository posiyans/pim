import request from '@/utils/request'

export function fetchList(query) {
  return request({
    url: '/user',
    method: 'get',
    params: query
  })
}

export function updateUser(user) {
  return request.put('/user/' + user.id,
    { user: user })
}
export function createUser(user) {
  return request.post('/user',
    { user: user })
}

export function updatePassword(password) {
  return request.put('/user/' + password.id,
    { password: password })
}

export function fetchExecutors() {
  return request({
    url: '/user/list',
    method: 'get',
    params: { field: { 'key': 'id', 'display_name': 'name' }}
  })
}
