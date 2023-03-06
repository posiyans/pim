import { getCountNoReadReportForMy } from 'src/Modules/Task/api/task'

export function fetchCountMyNoReadReport({ commit }) {
  getCountNoReadReportForMy()
    .then(res => {
      commit('setCountReadNoReport', res.data)
    })
}
