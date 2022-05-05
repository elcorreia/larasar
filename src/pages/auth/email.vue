<template>

    <q-page class="q-pa-md">

      <q-form class="q-gutter-md">
        <div class="row flex justify-center">

          <div class="col-md-6 q-pa-md">

            <q-card class="my-card text-white">
              <q-card-section class="bg-primary">
                <div class="text-h6">{{$t('reset_password')}}</div>
              </q-card-section>

              <div class="q-pa-md">
                <q-input
                  v-model="form.email"
                  filled
                  type="email"
                  :label="$t('email')"
                  :hint="form.email_data"
                  lazy-rules
                  :rules="[val => val && val.length > 0 || '*']"
                  :error="form.email_data ? true : false"
                  :error-message='form.email_data'
                />

                <q-btn
                  color="primary"
                  icon="fas fa-link"
                  :loading="form.loader"
                  :label="$t('send_password_reset_link')"
                  class="q-ma-sm" @click.prevent="send"
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

export default {
  name: 'resetPage',
  setup() {
    const $store = useStore()
    const $q = useQuasar();

    const form = reactive({
      loader: false,
      email: '',
      email_data: ''
    });

    return {
      form,
      send () {
        form.loader = true

        $store.dispatch('users/emailAction', {
          email: form.email
        })
          .then(rep => {
            form.loader = false
            $q.notify({
              color: 'positive',
              position: 'bottom-left',
              message: rep.data.status,
              icon: 'check'
            })
          })
          .catch(error => {

            $q.notify({
              color: 'negative',
              // positive, warning, info
              position: 'top-right',
              message: error.response.data.errors.email[0],
              icon: 'announcement'
            })

            form.loader = false
            console.log(error.response.data.errors.email[0])
            form.email_data = error.response.data.errors.email[0]
          })
      }
    }
  },
  data () {
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
