import { store } from 'quasar/wrappers'
import { createStore } from 'vuex'
import user from './user/index.js'
import avatar from './avatar/index.js'
import header from './header/index.js'
import users from 'src/Modules/User/store/index.js'
/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation;
 *
 * The function below can be async too; either use
 * async/await or return a Promise which resolves
 * with the Store instance.
 */

export default store(function (/* { ssrContext } */) {
  const Store = createStore({
    modules: {
      user,
      avatar,
      header,
      users
    },

    // enable strict mode (adds overhead!)
    // for dev mode and --debug builds only
    strict: process.env.DEBUGGING
  })

  return Store
})
