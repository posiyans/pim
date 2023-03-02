const moment = require('moment')
// const momentTz = require('moment-timezone')
require('moment/locale/ru')
require('moment/locale/en-au')
moment.locale('ru')
// momentTz.locale('ru')
// momentTz.suppressDeprecationWarnings = true
// momentTz.tz.setDefault('UTC')

export default async ({ app }) => {
  app.config.globalProperties.$moment = moment
}
