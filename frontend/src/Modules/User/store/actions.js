import { fetchExecutors } from 'src/Modules/User/api/user'
import { setExecutor } from 'src/Modules/User/store/mutations'

export function getExecutors({ commit, state }) {
  console.log('log1')
  return new Promise((resolve, reject) => {
    if (state.executors === null) {
      console.log('log2')
      commit('setExecutor', [])
      fetchExecutors()
        .then(response => {
          console.log('log3')
          commit('setExecutor', response.data)
          resolve(response.data)
        })
        .catch(() => {
          resolve([])
        })
    }
  })
}



