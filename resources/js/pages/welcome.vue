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
        {{ title }} v.1.0.0
      </div>
      <div class="form-group">
        <b-form-select v-model="sekolah_id" :options="options" v-bind:disabled="isReadOnly"></b-form-select>
      </div>
      <div class="form-group">
        <b-input-group>
          <b-form-input v-bind:readonly="isReadOnly" v-model="url" placeholder="Masukkan URL eRapor SMK. Contoh: http://localhost:8154 atau http://erapor.smkcontoh.sch.id"></b-form-input>
          <b-input-group-append>
            <!--b-button variant="info" v-on:click="tesKoneksi()" v-bind:disabled="isReadOnly">Tes Koneksi</b-button-->
            <b-button block variant="info" v-bind:disabled="isReadOnly" v-on:click="tesKoneksi()">
              <b-spinner v-show="isTesKoneksi" small type="grow"></b-spinner>
              <span v-show="!isTesKoneksi">Tes Koneksi</span>
              <span v-show="isTesKoneksi">Mengecek alamat eRapor SMK...</span>
            </b-button>
          </b-input-group-append>
        </b-input-group>
      </div>
      <div class="form-group">
        <input v-model="semester_id" type="hidden">
        <input v-model="tahun_ajaran_id" type="hidden">
        <!--b-button block variant="primary" v-show="success" v-on:click="kirimData()">Kirim Data Dapodik ke eRapor SMK</b-button-->
        <b-button block variant="primary" v-show="success" v-on:click="kirimData()" v-bind:disabled="isKirim">
          <b-spinner v-show="isKirim" small type="grow"></b-spinner>
          <span v-show="!isKirim">Kirim Data Dapodik ke eRapor SMK</span>
          <span v-show="isKirim">Memproses Kirim Data Dapodik ke eRapor SMK...</span>
        </b-button>
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
    return { title: null }
  },

  data: () => ({
    title: window.config.appName,
    sekolah_id: null,
    url: null,
    options: [
      { value: null, text: 'Pilih NPSN' },
      { value: 'a', text: 'This is option a' },
    ],
    success: false,
    danger: false,
    success_text: null,
    danger_text: null,
    isReadOnly: false,
    semester_id: null,
    tahun_ajaran_id: null,
    polling: null,
    isKirim: false,
    isTesKoneksi: false,
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  }),
  methods: {
    tesKoneksi(){
      this.isTesKoneksi = true
      axios.post(`/api/dapodik/cek-koneksi`, {
        sekolah_id : this.sekolah_id,
        semester_id: this.semester_id,
        tahun_ajaran_id: this.tahun_ajaran_id,
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
        this.isTesKoneksi = false
      })
    },
    kirimData(){
      this.isKirim = true
      this.polling = setInterval(() => {
        axios.get('api/dapodik/hitung')
        .then((response) => {
          let getData = response.data
          this.success_text = getData.data
        })
      }, 1000)
      axios.post(`/api/dapodik/kirim-data`, {
        sekolah_id : this.sekolah_id,
        semester_id: this.semester_id,
        tahun_ajaran_id: this.tahun_ajaran_id,
        url : this.url,
      })
      .then((response) => {
        clearInterval(this.polling)
        this.isKirim = false
        let getData = response.data
        this.success_text = null
        console.log(getData);
        this.success_text = getData.data
      })
    },
    loadPostsData() {
      axios.post(`/api/dapodik/sekolah`)
      .then((response) => {
        var tempData = [
          { value: null, text: 'Pilih Sekolah' }
        ];
        let getData = response.data
        getData.data.forEach(myFunction)
        function myFunction(item) {
          var a = {};
          a.value = item.sekolah_id
          a.text = item.nama
          tempData.push(a)
        }
        this.options = tempData
        this.semester_id = getData.semester.semester_id
        this.tahun_ajaran_id = getData.semester.tahun_ajaran_id
        console.log(getData.semester);
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
  font-size: 75px;
}
</style>
