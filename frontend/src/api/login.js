import request from 'src/utils/request'

export function loginByUsername(data) {
  return request({
    url: '/api/auth/login',
    method: 'post',
    data
  })
}

export function logout() {
  return request({
    url: '/api/auth/logout',
    method: 'get'
  })
}

export function getUserInfo(id) {
  return request({
    url: '/api/user/' + id,
    method: 'get'
  })
}


export function getMyInfo() {
  return request({
    url: '/api/my-user',
    method: 'get'
  })
}



