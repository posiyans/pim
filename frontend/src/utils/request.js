import axios from 'axios'
import { SessionStorage } from 'quasar'

// create an axios instance
const service = axios.create({
  baseURL: process.env.API, // url = base url + request url
  withCredentials: true, // send cookies when cross-domain requests
  timeout: 10000 // request timeout
})
// request interceptor
service.interceptors.request.use(
  config => {
    const token = SessionStorage.getItem('UserToken') || ''
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
      SessionStorage.remove('UserToken')
      if (window.location.pathname !== '/auth/login') {
        window.location = '/auth/login'
      }
    }
    return Promise.reject(error)
    // return error
  }
)
export default service
