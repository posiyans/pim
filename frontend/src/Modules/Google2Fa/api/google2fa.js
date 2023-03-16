import request from 'src/utils/request'

export function changeGoogle2FaSecretKey(data) {
  return request({
    url: '/api/user/change-google-secret-key',
    method: 'post',
    data
  })
}
