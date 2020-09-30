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
        <b-form-select v-model="selected" :options="options" v-bind:disabled="isReadOnly"></b-form-select>
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
      <div class="form-group" v-show="tombol_register">
        <b-form-input v-bind:readonly="isReadOnly" v-model="token" placeholder="Paste disini token Web Service Dapodik"></b-form-input>
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
        <b-button block variant="warning" v-show="tombol_register" v-on:click="register()" v-bind:disabled="isRegister">
          <b-spinner v-show="isRegister" small type="grow"></b-spinner>
          <span v-show="!isRegister">Registrasi Pengguna eRapor SMK</span>
          <span v-show="isRegister">Memproses Registrasi eRapor SMK...</span>
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
import Swal from 'sweetalert2'
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
    selected: {sekolah_id: null, npsn: null},
    url: null,
    options: [
      { value: {sekolah_id: null, npsn: null}, text: 'Pilih Sekolah' }
    ],
    success: false,
    danger: false,
    tombol_register: false,
    success_text: null,
    danger_text: null,
    isReadOnly: false,
    semester_id: null,
    tahun_ajaran_id: null,
    npsn: null,
    sekolah_id: null,
    polling: null,
    isKirim: false,
    isRegister: false,
    isTesKoneksi: false,
    token: null,
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  }),
  methods: {
    validateUrl: function() {
      //const regex = RegExp('(https?:\\/\\/)?((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|((\\d{1,3}\\.){3}\\d{1,3}))(\\:\\d+)?(\\/[-a-z\\d%_.~+@]*)*(\\?[;&a-z\\d%_.~+=-@]*)?(\\#[-a-z\\d_@]*)?$', 'i');
      //return this.url.match(regex);
      var pattern = new RegExp('^(https?:\\/\\/)?'+ // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))'+ // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?'+ // query string
        '(\\#[-a-z\\d_]*)?$','i'); // fragment locator
      let url;
      try {
        url = new URL(this.url);
      } catch (_) {
        return false;  
      }
      return url.protocol === "http:" || url.protocol === "https:";
    },
    prosesUrl(validUrl){
      if(!this.selected.sekolah_id){
        return this.showError('Sekolah tidak boleh kosong!')
      }
      console.log(validUrl);
      if(validUrl){
        return this.prosesKoneksi()
      } else {
        return this.showError('URL eRapor SMK tidak valid. Silahkan periksa kembali!')
      }
    },
    showError(message){
      Swal.fire({
        type: 'error',
        title: 'Gagal',
        text: message,
        reverseButtons: true,
        confirmButtonText: 'Ok',
      })
      return false
    },
    tesKoneksi(){
      if(!this.url){
        return this.showError('URL eRapor SMK tidak boleh kosong!')
      }
      let validUrl = this.validateUrl()
      return this.prosesUrl(validUrl)
    },
    prosesKoneksi(){
      this.isTesKoneksi = true
      axios.post(`/api/dapodik/cek-koneksi`, {
        sekolah_id : this.selected.sekolah_id,
        npsn : this.selected.npsn,
        semester_id: this.semester_id,
        tahun_ajaran_id: this.tahun_ajaran_id,
        url : this.url,
      })
      .then((response) => {
        let getData = response.data.data
        console.log(getData);
        if(getData.status == 'success'){
          if(getData.sekolah){
            this.success = true
            this.success_text = 'Data Sekolah ditemukan di aplikasi eRapor SMK. Silahkan klik tombol di atas untuk mengirim data Dapodik ke aplikasi eRapor SMK'
            this.isReadOnly = true
            this.danger = false
          } else {
            this.tombol_register = true
            this.danger = true
            this.danger_text = 'Data Sekolah tidak ditemukan di aplikasi eRapor SMK. Silahkan klik tombol diatas untuk melakukan registrasi aplikasi eRapor SMK.'
          }
        } else {
          this.danger = true
          this.danger_text = 'Koneksi ke aplikasi eRapor SMK gagal. Pastikan aplikasi eRapor SMK sudah memakai versi 5.1.1'
        }
        this.isTesKoneksi = false
      })
    },
    register(){
      this.isRegister = true
      axios.post(`/api/dapodik/registrasi`, {
        token: this.token,
        sekolah_id : this.selected.sekolah_id,
        npsn : this.selected.npsn,
        semester_id: this.semester_id,
        tahun_ajaran_id: this.tahun_ajaran_id,
        url : this.url,
      })
      .then((response) => {
        this.isRegister = false
        let getData = response.data
        if(getData.response_erapor){
          this.isReadOnly = true
          this.success = true
          this.success_text = 'Registrasi Pengguna eRapor SMK berhasil. Silahkan klik tombol di atas untuk mengirim data Dapodik ke aplikasi eRapor SMK'
          this.tombol_register = false
          this.danger_text = null
          this.danger = false
        }
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
        sekolah_id : this.selected.sekolah_id,
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
          { value: {sekolah_id: null, npsn: null}, text: 'Pilih Sekolah' }
        ];
        let getData = response.data
        getData.data.forEach(myFunction)
        //var a = { value: {sekolah_id: null, npsn: null}, text: 'Pilih Sekolah' }
        function myFunction(item) {
          var a = {};
          a.value = {sekolah_id: item.sekolah_id, npsn:item.npsn}
          //a.value = {sekolah_id: item.sekolah_id}
          a.text = item.nama
          tempData.push(a)
        }
        this.options = tempData
        this.semester_id = getData.semester.semester_id
        this.tahun_ajaran_id = getData.semester.tahun_ajaran_id
        console.log(this.options);
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
