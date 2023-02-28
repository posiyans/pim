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
          commit('setInfo', data.user)
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
        data.role = 'user'
        commit('setInfo', data)
        resolve(data)
      })
      .catch(() => {
        commit('setInfo', {
          role: 'guest'
        })
        resolve({ role: 'guest' })
      })
  })
}



