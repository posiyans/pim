import { date, LocalStorage } from 'quasar'


export default async ({ store, router }) => {
  let route = '/'
  const checkExpiredToken = async () => {
    if (LocalStorage.has('UserToken')) {
      const close = LocalStorage.getItem('tokenExpired')
      if (close) {
        const now = new Date()
        const diff = date.getDateDiff(now, close, 'seconds')
        // последнее получение даныых больше 20 минут выходим из систему
        if (diff > 20 * 60) {
          LocalStorage.remove('UserToken')
          LocalStorage.remove('tokenExpired')
          await store.dispatch('user/getInfo')
        }
      }
    } else {
      if (route !== '/auth/login') {
        router.push('/auth/login')
      }
    }
  }
  checkExpiredToken()

  try {
    await store.dispatch('user/getInfo')
  } catch (e) {
    console.log(e.status)
    if (e.status === 410) {
      store.commit('user/newUser', true)
    }
  }

  setInterval(() => {
    // проверка каждую минуту на время жизни токена
    checkExpiredToken()
  }, 60 * 1000)

  router.beforeEach(async (to, from, next) => {
    route = to.fullPath
    const hasRole = !!store.state.user.info.roles
    if (hasRole) {
      if (to.meta && to.meta.roles) {
        if (to.meta.roles.filter(x => store.state.user.info.roles.includes(x)).length > 0) {
          next(true)
        } else {
          next('/page/403')
        }
        return true
      }
      store.dispatch('header/fetchCountMyNoReadReport')
      store.commit('header/setTitle', to.meta.title)
      if (to.path === '/auth/login') {
        next('/')
      }
      next()
      return true
    }
    if (to.path === '/auth/login') {
      next(true)
      return true
    }
    next('/auth/login')
    return true

  })
}