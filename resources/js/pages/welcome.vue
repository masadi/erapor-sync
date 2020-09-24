<template>
  <div>
    <div class="top-right links" v-show="authenticated">
      <template v-if="authenticated">
        <router-link :to="{ name: 'home' }">
          {{ $t('home') }}
        </router-link>
      </template>
      <template v-else>
        <router-link :to="{ name: 'login' }">
          {{ $t('login') }}
        </router-link>
        <router-link :to="{ name: 'register' }">
          {{ $t('register') }}
        </router-link>
      </template>
    </div>

    <div class="text-center">
      <div class="title mb-4">
        {{ title }}
      </div>
      <div class="form-group">
        <b-form-select v-model="selected" :options="options" v-bind:disabled="isReadOnly"></b-form-select>
      </div>
      <div class="form-group">
        <b-input-group>
          <b-form-input v-bind:readonly="isReadOnly" v-model="url" placeholder="Masukkan URL eRapor SMK. Contoh: http://localhost:8154 atau http://erapor.smkcontoh.sch.id"></b-form-input>
          <b-input-group-append>
            <b-button variant="info" v-on:click="tesKoneksi()" v-bind:disabled="isReadOnly">Tes Koneksi</b-button>
          </b-input-group-append>
        </b-input-group>
      </div>
      <div class="form-group">
        <b-button block variant="primary" v-show="success" v-on:click="kirimData()">Kirim Data Dapodik ke eRapor SMK</b-button>
      </div>
      <b-alert show variant="success" v-show="success">{{success_text}}</b-alert>
      <b-alert show variant="danger" v-show="danger">{{danger_text}}</b-alert>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'
export default {
  layout: 'basic',
  created() {
    this.loadPostsData()
  },
  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: window.config.appName,
    selected: null,
    url: null,
    options: [
      { value: null, text: 'Pilih NPSN' },
      { value: 'a', text: 'This is option a' },
    ],
    success: false,
    danger: false,
    success_text: '',
    danger_text: '',
    isReadOnly: false,
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  }),
  methods: {
    tesKoneksi(){
      axios.post(`/api/dapodik/cek-koneksi`, {
        sekolah_id : this.selected,
        url : this.url,
      })
      .then((response) => {
        let getData = response.data.data
        if(getData){
          if(getData.sekolah){
            this.success = true
            this.success_text = 'Data Sekolah ditemukan di aplikasi eRapor SMK. Silahkan klik tombol di atas untuk mengirim data Dapodik ke aplikasi eRapor SMK'
            this.isReadOnly = true
          } else {
            this.danger = true
            this.danger_text = 'Data Sekolah tidak ditemukan di aplikasi eRapor SMK'
          }
        } else {
          this.danger = true
          this.danger_text = 'Koneksi ke aplikasi eRapor SMK gagal. Silahkan periksa kembali URL eRapor SMK'
        }
      })
    },
    kirimData(){
      axios.post(`/api/dapodik/kirim-data`, {
        sekolah_id : this.selected,
        url : this.url,
      })
      .then((response) => {
        let getData = response.data.data
        console.log(getData);
      })
    },
    loadPostsData() {
      axios.post(`/api/dapodik/sekolah`)
      .then((response) => {
        var tempData = [
          { value: null, text: 'Pilih Sekolah' }
        ];
        let getData = response.data.data
        getData.forEach(myFunction)
        function myFunction(item) {
          var a = {};
          a.value = item.sekolah_id
          a.text = item.nama
          tempData.push(a)
        }
        this.options = tempData
      })
    }
  }
}
</script>

<style scoped>
.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

.title {
  font-size: 85px;
}
</style>
