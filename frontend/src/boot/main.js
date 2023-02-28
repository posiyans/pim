export default async ({ store, router }) => {
  await store.dispatch('user/getInfo')

  router.beforeEach(async (to, from, next) => {
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