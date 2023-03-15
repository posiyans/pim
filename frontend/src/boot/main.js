export default async ({ store, router }) => {

  // let timer = null
  await store.dispatch('user/getInfo')

  router.beforeEach(async (to, from, next) => {
    // if (timer) clearTimeout(timer)
    // timer = setTimeout(() => {
    //   store.dispatch('user/userLogout')
    //     .then(() => {
    //       router.push('/auth/login')
    //     })
    // }, 15 * 60 * 1000)


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