import request from 'src/utils/request'

export function getUsersList(query) {
  return request({
    url: '/api/user/get-list',
    method: 'get',
    params: query
  })
}

export function getUserInfo(query) {
  return request({
    url: '/api/user/get',
    method: 'get',
    params: query
  })
}

export function getUserAvatar(query) {
  return request({
    url: '/api/user/get-avatar',
    method: 'get',
    responseType: 'blob',
    params: query
  })
}

export function updateUser(data) {
  return request({
    url: '/api/user/update',
    method: 'post',
    data
  })
}

export function createUser(user) {
  return request.post('/api/user',
    { user: user })
}

export function changeUserPassword(data) {
  return request({
    url: '/api/user/password-change',
    method: 'post',
    data
  })
}

export function fetchExecutors() {
  return request({
    url: '/api/user/list',
    method: 'get',
    params: { field: { 'key': 'id', 'display_name': 'name' } }
  })
}
