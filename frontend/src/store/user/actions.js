import { getMyInfo, loginByUsername, logout } from 'src/api/login'
import { SessionStorage } from 'quasar'

export function loginUser({ commit }, data) {
  return new Promise((resolve, reject) => {
    loginByUsername(data)
      .then(response => {
        const data = response.data
        SessionStorage.set('UserToken', data.token)
        const user = data.user
        user.roles = ['user']
        if (user.moderator) {
          user.roles.push('moderator')
          user.roles.push('admin')
        }
        commit('setInfo', user)
        resolve(data)
      }).catch(error => {
      reject(error)
    })
  })
}

export function getInfo({ commit }) {
  return new Promise((resolve, reject) => {
    getMyInfo()
      .then(response => {
        const { data } = response
        if (!data) {
          reject('Ошибка попробуйте позже')
        }
        data.roles = ['user']
        if (data.moderator) {
          data.roles.push('moderator')
          data.roles.push('admin')
        }
        commit('setInfo', data)
        resolve(data)
      })
      .catch(() => {
        resolve({ role: 'guest' })
      })
  })
}

export function userLogout({ commit }, data) {
  return new Promise((resolve, reject) => {
    logout(data)
      .then(() => {
        SessionStorage.remove('UserToken')
        commit('setInfo', { role: '' })
        resolve()
      }).catch(error => {
      reject(error)
    })
  })
}



