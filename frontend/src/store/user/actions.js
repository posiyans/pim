import { creteNewUser, getMyInfo, loginByUsername, logout } from 'src/api/login'
import { LocalStorage } from 'quasar'

export function loginUser({ commit }, data) {
  return new Promise((resolve, reject) => {
    loginByUsername(data)
      .then(response => {
        const data = response.data
        if (data.status === 'done') {
          LocalStorage.set('UserToken', data.token)
          const user = data.user
          commit('setInfo', user)
          resolve(data)
        } else {
          resolve(data)
        }
      })
      .catch(error => {
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
        commit('setInfo', data)
        resolve(data)
      })
      .catch(er => {
        // if (er.response.status === 410) {
        reject(er.response)
        // }
        // console.log(er.response.status)
        resolve({ role: 'guest' })
      })
  })
}

export function userLogout({ commit }, data) {
  return new Promise((resolve, reject) => {
    logout(data)
      .then(() => {
        LocalStorage.remove('UserToken')
        commit('setInfo', { role: '' })
        resolve()
      }).catch(error => {
      reject(error)
    })
  })
}


export function createUser({ commit }, data) {
  return new Promise((resolve, reject) => {
    creteNewUser(data)
      .then(response => {
        const data = response.data
        resolve(data)
      })
      .catch(error => {
        reject(error)
      })
  })
}




