import Cookies from 'js-cookie'

const TokenKey = 'S-Token'
const TokenUser = 'U-Token'
const UserId = 'User-Id'

export function getToken() {
  return Cookies.get(TokenKey)
}

export function getUserToken() {
  return Cookies.get(TokenUser)
}

export function getUserId() {
  return Cookies.get(UserId)
}

export function setToken(data) {
  return Cookies.set(TokenUser, data.token.user, { expires: 7 }) && Cookies.set(TokenKey, data.token.sesions) && Cookies.set(UserId, data.id)
}

export function removeToken() {
  return Cookies.remove(TokenKey) && Cookies.remove(UserId)
}
