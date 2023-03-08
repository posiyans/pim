export default async ({ store, router }) => {

  let timer = null
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
    console.log('hasRole')
    console.log(hasRole)
    if (hasRole) {
      if (to.meta && to.meta.roles) {
        if (to.meta.roles.includes(store.state.user.info.roles)) {
          next(true)
          return true
        } else {
          next('/page/403')
        }
        return true
      }
      console.log(to)
      store.dispatch('header/fetchCountMyNoReadReport')
      store.commit('header/setTitle', to.meta.title)
      next()
      return true
    }
    console.log('to')
    console.log(to)
    if (to.path === '/auth/login') {
      next(true)
      return true
    }
    next('/auth/login')
    return true

  })
}