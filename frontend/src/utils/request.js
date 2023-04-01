import axios from 'axios'
import { LocalStorage } from 'quasar'

let timer = null
const service = axios.create({
  baseURL: process.env.API, // url = base url + request url
  withCredentials: true, // send cookies when cross-domain requests
  timeout: 10000 // request timeout
})
// request interceptor
service.interceptors.request.use(
  config => {
    setTimeout(() => {
      LocalStorage.set('tokenExpired', new Date())
      // LocalStorage.set('tokenExpired', date.formatDate(new Date(), 'YYYY-MM-DD HH:mm:ss'))
    }, 500)

    // if (timer) {
    //   clearTimeout(timer)
    // }
    //
    // timer = setTimeout(() => {
    //   LocalStorage.remove('UserToken')
    //   window.location = '/auth/login'
    // }, 20 * 60 * 1000)
    const token = LocalStorage.getItem('UserToken') || ''
    if (token) {
      config.headers.Authorization = 'Bearer ' + token
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

service.interceptors.response.use(
  response => {
    return response
  },
  error => {
    if (error.response.status === 401) {
      LocalStorage.remove('UserToken')
      if (window.location.pathname !== '/auth/login') {
        window.location = '/auth/login'
      }
    }
    return Promise.reject(error)
    // return error
  }
)
export default service
