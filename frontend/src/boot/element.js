import { boot } from 'quasar/wrappers'
import ElementPlus, { dayjs } from 'element-plus'
import 'element-plus/dist/index.css'
import ru from 'element-plus/dist/locale/ru.mjs'

export default boot(({ app }) => {
  dayjs.Ls.en.weekStart = 1
  app.use(ElementPlus, {
    locale: ru
  })
})
