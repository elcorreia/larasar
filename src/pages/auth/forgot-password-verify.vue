<template>

  <q-page class="q-pa-md">

    <q-form v-show="form.showForm" class="q-gutter-md">
      <div class="row flex justify-center">

        <div class="col-md-6 q-pa-md">

          <q-card class="my-card text-white">
            <q-card-section class="bg-primary">
              <div class="text-h6">{{ $t('reset_password') }}</div>
            </q-card-section>

            <div class="q-pa-md">
              <q-input
                v-model="form.email"
                :error="!!form.email_data"
                :error-message='form.email_data'
                :hint="form.email_data"
                :label="$t('email')"
                :rules="[val => val && val.length > 0 || '*']"
                filled
                lazy-rules
                type="email"
                :disable="true"
              />
              <q-input
                    v-model="form.password"
                    :error="!!form.password_data"
                    :error-message='form.password_data'
                    :hint="form.password_data"
                    :label="$t('password')"
                    :rules="[val => val && val.length > 0 || '*']"
                    filled
                    lazy-rules
                    type="password"
              />

                <q-input
                    v-model="form.password_confirmation"
                    :error="!!form.password_confirmation_data"
                    :error-message='form.password_confirmation_data'
                    :hint="form.password_confirmation_data"
                    :label="$t('confirm_password')"
                    filled
                    lazy-rules
                    type="password"
                    :rules="[val => val==form.password || 'Password is not matched']"
                />

              <q-btn
                :label="$t('reset_password')"
                :loading="form.loader"
                class="q-ma-sm"
                color="primary"
                icon="fas fa-link" @click.prevent="send"
              />

            </div>

          </q-card>

        </div>

      </div>
    </q-form>

  </q-page>

</template>

<script>
import {useStore} from 'vuex';
import {reactive} from 'vue';
import {useQuasar} from 'quasar';
import {useRoute} from "vue-router";

export default {
  name: 'resetPage',
  setup() {
    const $store = useStore()
    const $q = useQuasar();
    const $route = useRoute();

    const form = reactive({
      loader: false,
      showForm: true,
      email: $route.query.email,
      email_data: null,
      password : null,
      password_data : null,
     password_confirmation : null,
     password_confirmation_data : null,
     token :  $route.query.token,

    });

    return {
      form,
      send() {
        form.loader = true


        $store.dispatch('users/passwordConfirm',
            form
        )
          .then(rep => {
              form.loader = false

            $q.notify({
              color: 'positive',
              position: 'top',
              message: rep.data.message,
              icon: 'check'
            })

            form.showForm = false
            form.loader = false


          })
          .catch(error => {
            const mainMessage = error.response.data?.message ?? error.response.data?.errors?.email[0];

            $q.notify({
              color: 'negative',
              // positive, warning, info
              position: 'top-right',
              message: mainMessage,
              icon: 'announcement'
            })


              form.loader = false
              this.form.error = error.response.data.errors.password[0];
              this.$refs.password.focus()
          })
      }
    }
  },
  data() {
    return {
      loader: false,
      email: '',
      email_data: ''
    }
  },
  methods: {


    // send () {
    //   this.loader = true
    //   this.$axios.post('/api/password/email', {
    //     email: this.email,
    //     locale: locale
    //   })
    //     .then(rep => {
    //       this.loader = false
    //       this.$q.notify({
    //         color: 'positive',
    //         position: 'top',
    //         message: rep.data.status,
    //         icon: 'check'
    //       })
    //     })
    //     .catch(error => {
    //       this.loader = false
    //       this.email_data = error.response.data.email || error.response.data.errors.email[0]
    //     })
    // }
    // send () {
    //   this.loader = true
    //
    //   $store.dispatch('/api/password/email', form.value)
    //     .then(() => {
    //       loader.value = false
    //       Object.assign(form, formInitialState);
    //       $router.push({ path: '/' })
    //     })
    //     .catch(error => catchErr(error));
    //
    //
    //   this.$axios.post('/api/password/email', {
    //     email: this.email,
    //     locale: locale
    //   })
    //     .then(rep => {
    //       this.loader = false
    //       this.$q.notify({
    //         color: 'positive',
    //         position: 'top',
    //         message: rep.data.status,
    //         icon: 'check'
    //       })
    //     })
    //     .catch(error => {
    //       this.loader = false
    //       this.email_data = error.response.data.email || error.response.data.errors.email[0]
    //     })
    // }
  }
}
</script>
