export default async ({ store, router }) => {
  await store.dispatch('user/getInfo')

  router.beforeEach(async (to, from, next) => {
    const hasRole = !!store.state.user.info.role
    if (hasRole) {
      if (to.meta && to.meta.roles) {
        if (to.meta.roles.includes(store.state.user.info.role)) {
          next(true)
          return true
        } else {
          if (store.state.user.info.role === 'guest') {
            next('/auth/login')
          } else {
            next('/page/403')
          }
          return true
        }
      }
      next(true)
      return true
    } else {
      store.commit('user/setInfo', {
        role: 'guest'
      })
      next({ ...to, replace: true })
      return true
    }
  })
}