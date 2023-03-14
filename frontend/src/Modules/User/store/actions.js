import { fetchExecutors } from 'src/Modules/User/api/user'
import { setExecutor } from 'src/Modules/User/store/mutations'

export function getExecutors({ commit, state }) {
  return new Promise((resolve, reject) => {
    if (state.executors === null) {
      commit('setExecutor', [])
      fetchExecutors()
        .then(response => {
          commit('setExecutor', response.data)
          resolve(response.data)
        })
        .catch(() => {
          resolve([])
        })
    }
  })
}



