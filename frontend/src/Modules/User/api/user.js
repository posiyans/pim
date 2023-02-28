import request from 'src/utils/request'

export function fetchList(query) {
  return request({
    url: '/api/user',
    method: 'get',
    params: query
  })
}

export function updateUser(user) {
  return request.put('/api/user/' + user.id,
    { user: user })
}

export function createUser(user) {
  return request.post('/api/user',
    { user: user })
}

export function updatePassword(password) {
  return request.put('/api/user/' + password.id,
    { password: password })
}

export function fetchExecutors() {
  return request({
    url: '/api/user/list',
    method: 'get',
    params: { field: { 'key': 'id', 'display_name': 'name' } }
  })
}
