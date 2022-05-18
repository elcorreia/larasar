<template>
  <q-layout view="lHr LpR lFr">

    <q-header elevated class="bg-primary text-white" height-hint="98">
      <q-toolbar>

        <q-toolbar-title>
          <q-btn to="/" class="q-ma-md">
              <q-icon name="home" /> {{ appName }}
          </q-btn>
        </q-toolbar-title>

        <locale-dropdown />

      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>

    <q-ajax-bar size="5px"/>
  </q-layout>
</template>

<script>
import { openURL, QAjaxBar, useQuasar, Cookies } from 'quasar'
import { ref, computed, watch, onMounted, version } from 'vue'
import { useStore } from 'vuex'
import { useRoute } from 'vue-router'
import { url, userData, notifyAction, crudAction, api } from 'boot/axios'
import LocaleDropdown from '../components/LocaleDropdown'

/**
 * Tags: SearchModule - AdModule - IpDebugModule - TagPostLacation
 *
 * @from
 */
export default {
  openURL,
  components: {
    QAjaxBar,
    LocaleDropdown
  },
  setup () {
    const $q = useQuasar()
    const $route = useRoute()
    const $store = useStore()
    const authDrawer = ref(true)
    const category = ref(null)
    const pages = ref([])
    const region = ref(null)
    const country = ref(null)
    const city = ref(null)


    const auth = computed(() => $store.getters['users/authGetter'])
    const roles = computed(() => $store.getters['users/rolesGetter'])
    const token = computed(() => $store.getters['users/tokenGetter'])
    const loc = computed(() => $store.getters['users/locationGetter'])
    const locale = computed(() => $store.getters['config/localeGetter'])
    const appName = computed(() => $store.getters['config/appNameGetter'])
    const ipDebug = computed(() => $store.getters['config/ipDebugGetter'])

    watch(locale, val => {
      crudAction({
        locale: val,
        url: 'api/pages/1',
        method: 'get',
        pages: 1
      }).then(getPages => {pages.value = getPages})
        .catch(e => notifyAction({error: 'localeSidePages', e}))
    }) // TagWatch: localePageModule

    watch(authDrawer, () => {
      crudAction({
        locale: locale.value,
        url: 'api/pages/1',
        method: 'get',
        pages: 1
      }).then(getPages => {pages.value = getPages})
        .catch(e => notifyAction({error: 'authDrawerPages', e}))
    }) // TagWatch: authDrawerPageModule

    watch(token, val => authDrawer.value = val ? 0 : 1) // TagWatch: AuthModule

    onMounted(() => {
      showInstallPromotion() // TagApp: AppModule
      if (token.value) authDrawer.value = false // TagWatch: AuthModule
      else crudAction({
        locale: locale.value,
        url: 'api/pages/1',
        method: 'get',
        pages: 1 // TagPage: PageModule
      }).then(getPages => {pages.value = getPages})
        .catch(e => notifyAction({error: 'mountedDrawerPages', e}))
      try {
        city.value = userData.city
        region.value = userData.region
        country.value = userData.country
      } catch (error) {}
    })

    function showInstallPromotion () {
      window.addEventListener('beforeinstallprompt', e => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault(); color.value = 'yellow'; setTimeout(() => {
          $q.notify({
            color: 'positive',
            position: 'top', // bottom - top
            message: $t('Get our free app for better experience. It wont take up space'),
            icon: 'fas fa-store',
            timeout: 0,
            actions: [
              { label: $t('Install App'), color: 'white', handler: () => installApp(e) },
              { icon: 'close', color: 'white', handler: () => { color.value = 'white' } }
            ] // <https://web.dev/customize-install> - <https://web.dev/codelab-make-installable>
          }) // https://developers.google.com/web/fundamentals/app-install-banners/native#mini-info-bar
        }, 500) // https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps/Add_to_home_screen
        function installApp (e) {
          e.prompt(); e.userChoice.then(choiceResult => {
            if (choiceResult.outcome === 'accepted') Cookies.set('appinstalled', userData, { expires: 365 })
            color.value = 'white'// either "accepted" or "dismissed"
          })
        }
      })
    } // TagApp: AppModule

    return {
      appName,
      ipDebug,
      version,
      auth,
      pages,
      path: computed(() => $route.path),
      laravel: computed(() => $store.getters['config/laravelGetter']),
      postPage: computed(() => $store.getters['crud/Getter']?.postPage),
      tab: ref('mails'),
      color: ref('white'),
      banner: ref(true),
      desktop: $q.platform.is.desktop,
      authDrawer,
      leftDrawerOpen: ref(false), // $q.platform.is.desktop,
      rightDrawer: ref(false),
      loader: ref(false),
      url,
      userData,
      region,
      country,
      city,
      // ====Search=== \\
      search_posts: ref(null),
      location: ref(false),
      // ============== \\
      category,
      // ================== \\
      admin: computed(() => Cookies.get('authID')),
      role: computed(() => roles.value),
      place: computed(() => loc.value),
      authRole: computed(() => {
        try {
          return auth.value.role
        } catch (e) { return false }
      }), // Auth Role
      superAdmin: computed(() => {
        try {
          return auth.value.id === 1
        } catch (e) { return false }
      }),
      avatar: computed(() => {
        if (auth.value?.avatar) {
          if (auth.value.avatar?.includes('images/profile')) return url + '/' + auth.value.avatar
          else return auth.value.avatar
        } else return auth.value?.new?.avatar
      }),

      logout () {
        $store.dispatch('users/logoutAction', auth.value)
        const authID = Cookies.get('authID')
                       Cookies.remove('authID')

        if (authID) setTimeout(() => {
            api({
              url: 'api/users',
              method: 'post',
              data: { log: true, userId: authID }
            }).then(() => {$store.dispatch('users/loginAction')})
              .catch(e => notifyAction({error: 'logAuth', e}))
        }, 500) // Log Back To Auth Account
      }//, authID
    }
  }
}
</script>

<style scoped>
/* .my-card {
  width: 100%;
  max-width: 300px;
} */
.q-btn {
    *text-transform: none;
}
.search {
    border-radius: 4px;
    background: white;
}
[v-cloak] {
  display: none !important;
}
</style>
