<style lang="scss">

input:-webkit-any(:-webkit-autofill) ~ input {
  position: absolute;
  left: 45px;
  top: 13px;
  width: 203px;
  height: 27px;
  border: 0;
  background: #17181F !important;
  color: #cb2626 !important;
  font-size: 16px;
}


/*input:-webkit-autofill*/
/*input:-webkit-autofill:hover,*/
/*input:-webkit-autofill:focus,*/
/*textarea:-webkit-autofill,*/
/*textarea:-webkit-autofill:hover,*/
/*textarea:-webkit-autofill:focus,*/
/*select:-webkit-autofill,*/
/*select:-webkit-autofill:hover,*/
/*!*select:-webkit-autofill:focus *!*/
/*{*/
/*  -webkit-text-fill-color: #131111;*/
/*  -webkit-box-shadow: 0 0 0 1000px rgba(255, 255, 255, 0.07) !important;*/
/*  transition: background-color 100ms ease-in-out 0s;*/
/*  !*background-color: rgba(255, 255, 255, 0.07) !important;*!*/
/*}*/
/*.q-field--outlined .q-field__control:before {*/
/*  z-index: 1;*/
/*}*/

/*.q-field__native{*/
/*  background: rgba(255, 255, 255, 0.07)*/
/*}*/

</style>

<template>
  <q-page class="q-pa-md">
    <q-form class="q-gutter-md" @submit.prevent="authenticateUser">

      <div class="row flex justify-center">

        <div class="col-md-6 q-pa-md">

          <q-card class="my-card text-white">

            <q-card-section class="bg-primary">
              <div class="text-h6">{{ $t('login') }}</div>
            </q-card-section>

            <div class="q-pa-md">

              <q-input
                filled
                type="email"
                v-model="form.email"
                lazy-rules
                bottom-slots
                :label="$t('email')"
                :rules="[val => val && val.length > 0 || '*']"
                :error="email_data ? true : false"
                :error-message='email_data'

                autocorrect="off"
                autocapitalize="off"
                autocomplete="false"
                spellcheck="false"
              /><!-- https://quasar.dev/vue-components/field#Validation -->

              <q-input
                filled
                v-model="form.password"
                :label="$t('password')"
                :type="isPwd ? 'password' : 'text'"
                lazy-rules
                :rules="[val => !!val || '*']"
                :error="password_data"
                :error-message='password_data'

                autocorrect="off"
                autocapitalize="off"
                autocomplete="off"
                spellcheck="false"
              >
                <template v-slot:append>
                  <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                  />
                </template>
              </q-input>
              <!--            </div>-->
              <!--          </div>-->

              <div class="*row *q-pt-md">

                <q-btn color="primary"
                       icon="fas fa-sign-in-alt"
                       :loading="loader" :label="$t('login')"
                       type="submit"

                       class="q-ma-sm *col-lg-3"
                />
<!--                @click.prevent="authenticateUser"-->
                <!--            <q-btn color="primary"-->
                <!--              icon="fas fa-sync" v-if="true"-->
                <!--              :loading="loader" :label="$t('reset')"-->
                <!--              @click.prevent="deleteAllCookies" class="q-ma-sm *col-lg-3"-->
                <!--            />-->
                <q-btn color="primary"
                       flat
                       :label="$t('forgot_password')"
                       to="/reset"
                       class="*q-ma-sm *col-lg-12"/>

                <q-checkbox v-model="form.remember"
                            :label="$t('remember_me')"
                            class="*q-ma-sm *col-lg-3 label"/>
                <!--            <q-btn color="primary" flat :label="$t('register')" to='/register' >-->
                <!--             <q-checkbox  v-model="remember" :label="$t('remember_me')" class="*text-black *q-ma-sm *col-lg-3"/>-->
                <!--            </q-btn>-->

              </div>
            </div>


          </q-card>
        </div>

      </div>


    </q-form>

  </q-page>

</template>

<script>
import {ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useStore} from 'vuex'
import {useQuasar} from 'quasar';

export default {
  components: {},
  name: 'registerPage',
  props: ['auth'],
  setup() {
    const $route = useRoute()
    const $store = useStore()
    const $router = useRouter()
    const loader = ref(false)
    const token = ref($route.query.token || $route.params.token)

    const $q = useQuasar();
    $q.dark.set(false)

    const formInitialState = {
      email: $route.query.email || null,
      password: '',
      remember: true
    };

    const form = ref(formInitialState);

    const email_data = ref(null)
    const password_data = ref(null)

    function catchErr(error) {
      const data = error?.response?.data;
      loader.value = false
      const errors = data?.errors;
      const message = data?.message

      email_data.value = data?.['email'] || errors?.email?.[0]
      password_data.value = data?.['password'] || errors?.email?.[0]

      if(message) alert(message)
    }

    return {
      form,
      token,
      loader,
      email_data,
      isPwd: ref(true),
      authenticateUser() {

        $store.dispatch('users/loginAction', form.value)
          .then(() => {
            loader.value = false
            Object.assign(form, formInitialState);
             $router.push({ path: '/' })
          })
          .catch(error => catchErr(error));

        loader.value = true
      },

      deleteAllCookies() {
        let cookies = document.cookie.split(';')
        for (let i = 0; i < cookies.length; i++) {
          let cookie = cookies[i]
          let eqPos = cookie.indexOf('=')
          let name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie
          document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT'
        }
        location.reload()
      }
    }
  }
}
</script>
