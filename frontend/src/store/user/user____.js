import { loginByUsername, logout, getUserInfo } from 'src/api/login'

const user____ = {
  state: {
    id: '',
    user: '',
    status: '',
    code: '',
    token: '',
    name: '',
    avatar: '',
    introduction: '',
    roles: [],
    sms: '',
    setting: {
      articlePlatform: []
    }
  },

  mutations: {
    SET_CODE: (state, code) => {
      state.code = code
    },
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    SET_INTRODUCTION: (state, introduction) => {
      state.introduction = introduction
    },
    SET_SETTING: (state, setting) => {
      state.setting = setting
    },
    SET_STATUS: (state, status) => {
      state.status = status
    },
    SET_NAME: (state, name) => {
      state.name = name
    },
    SET_ID: (state, id) => {
      state.id = id
    },
    SET_SMS: (state, sms) => {
      state.sms = sms
    },
    SET_AVATAR: (state, id) => {
      state.avatar = '/user/avatar/' + id + '/'
    },
    SET_ROLES: (state, roles) => {
      state.roles = roles
    }
  },

  actions: {
    LoginByUsername({ commit }, userInfo) {
      const username = userInfo.username.trim()
      const sms = userInfo.sms.trim()
      return new Promise((resolve, reject) => {
        loginByUsername(username, userInfo.password, sms).then(response => {
          const data = response.data
          if (data.sms) {
            resolve(data.sms)
          } else {
            commit('SET_TOKEN', data.token)
            resolve(data)
          }
        }).catch(error => {
          reject(error)
        })
      })
    },

    GetUserInfo({ commit, state }) {
      return new Promise((resolve, reject) => {
        getUserInfo(state.id).then(response => {
          if (!response.data) {
            reject('Проверка не удалась, пожалуйста, войдите снова.')
          }
          const data = response.data

          if (data.roles_json && data.roles_json.length > 0) {
            commit('SET_ROLES', data.roles_json)
          } else {
            commit('SET_ROLES', ['user'])
          }
          commit('SET_ID', data.id)
          commit('SET_NAME', data.name)
          commit('SET_AVATAR', data.id)
          commit('SET_SMS', data.sms)
          commit('SET_INTRODUCTION', data.introduction)
          resolve(response)
        }).catch(error => {
          reject(error)
        })
      })
    },


    LogOut({ commit, state }) {
      return new Promise((resolve, reject) => {
        logout(state.token).then(() => {
          commit('SET_TOKEN', '')
          commit('SET_ROLES', [])
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    }
  }
}

export default user____
