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
              />

              <q-btn
                :label="$t('send_password_reset_link')"
                :loading="form.loader"
                class="q-ma-sm"
                color="primary"
                icon="fas fa-link" @click.prevent="send"
              />
              <q-btn
                flat
                :label="$t('go_home')"
                class="q-ma-sm"
                color="primary"
                to="/login"
              />

            </div>

          </q-card>

        </div>

      </div>
    </q-form>

    <div v-show="!form.showForm" class="row flex justify-center">
      <div class="col-md-6 q-pa-md">
        <q-card class="my-card">
          <q-card-section class="bg-primary">
            <div class="text-h6 text-white">{{ $t('reset_password') }}</div>
          </q-card-section>

          <q-card-section>
            <div class="">
                {{ $t('reset_password_text_success') }}
            </div>
          </q-card-section>

          <q-separator/>
          <q-card-actions>
            <q-btn

              :label="$t('go_home')"
              class="q-ma-sm"
              color="primary"
              to="/login"
            />
          </q-card-actions>

        </q-card>
      </div>
    </div>


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
      showForm: true,
      email: 'elcorreia17@gmail.com',
      email_data: ''
    });

    return {
      form,
      send() {
        form.loader = true

        $store.dispatch('users/emailAction', {
          email: form.email
        })
          .then(rep => {

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
            console.log(error.response.data?.errors?.email[0])
            form.email_data = error.response.data?.errors?.email[0]
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
