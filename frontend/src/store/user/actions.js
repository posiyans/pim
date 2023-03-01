import { getMyInfo, loginByUsername } from 'src/api/login'
import { LocalStorage } from 'quasar'

export function loginUser({ commit }, data) {
  return new Promise((resolve, reject) => {
    loginByUsername(data)
      .then(response => {
        const data = response.data
        if (data.sms) {
          resolve(data.sms)
        } else {
          LocalStorage.set('UserToken', data.token)
          const user = data.user
          user.roles = ['user']
          if (user.moderator) {
            user.roles.push('admin')
          }
          commit('setInfo', user)
          resolve(data)
        }
      }).catch(error => {
      reject(error)
    })
  })
}

export function getInfo({ commit }) {
  return new Promise((resolve, reject) => {
    getMyInfo()
      .then(response => {
        console.log(response)
        const { data } = response
        if (!data) {
          reject('Ошибка попробуйте позже')
        }
        console.log(data)
        data.roles = ['user']
        if (data.moderator) {
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



