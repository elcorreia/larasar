import { Notify, LocalStorage, Cookies } from 'quasar'
import { axiosInstance, cookie } from 'boot/axios'
import { i18n } from 'boot/i18n'

export function configAction ({ commit }, locale) {
  cookie ? Cookies.set('locale', locale, { expires: 365 }) : LocalStorage.set('locale', locale, { expires: 365 })
  i18n.locale = locale
  axiosInstance.get('api/config')
    .then((response) => {
      const config = response.data
      // console.log(locale, 'configAction')
      commit('configMutation', { config, locale })
    })
    .catch(error => {
      Notify.create({
        color: 'negative',
        position: 'top',
        message: 'configAction' + error,
        icon: 'report_problem'
      })
    })
}
