import axios from 'axios'
import { LocalStorage } from 'quasar'
// create an axios instance
const service = axios.create({
  baseURL: process.env.API, // url = base url + request url
  withCredentials: true, // send cookies when cross-domain requests
  timeout: 20000 // request timeout
})
// request interceptor
service.interceptors.request.use(
  config => {
    const token = LocalStorage.getItem('UserToken') || ''
    config.headers.Authorization = 'Bearer ' + token
    config.headers['X-Requested-With'] = 'XMLHttpRequest'
    config.headers['X-CSRF-TOKEN'] = LocalStorage.has('UserToken') ? LocalStorage.getItem('UserToken') : ''
    return config
  },
  error => {
    // do something with request error
    // console.log(error) // for debug
    return Promise.reject(error)
  }
)

// // // response interceptor
// service.interceptors.response.use(
//   response => {
//     return response
//   },
//   error => {
//     return error
//   }
// )

export default service
