<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Sekolah;
use App\Models\Ptk;
use App\Models\Rombongan_belajar;
use App\Models\Pembelajaran;
use App\Models\Peserta_didik;
use App\Models\Anggota_rombel;
use App\Models\Kelas_ekskul;
use App\Models\Dudi;
use App\Models\Jurusan;
use App\Models\Kurikulum;
use App\Models\Mata_pelajaran;
use App\Models\Mata_pelajaran_kurikulum;
use App\Models\Tahun_ajaran;
use App\Models\Semester;
use App\Models\Wilayah;
use App\HelperModel;
use App\Helpers\Encrypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
class DapodikController extends Controller
{
    public function __construct()
    {
        $this->semester = Semester::where(function($query){
            $query->whereHas('tahun_ajaran', function($query){
                $query->where('periode_aktif', 1);
            });
            $query->where('periode_aktif', 1);
            $query->whereNull('expired_date');
        })->first();
    }
    public function sekolah(Request $request){
        Storage::disk('public')->delete('kirim_data.json');
        $all_data = Sekolah::where(function($query){
            $query->whereHas('sekolah_longitudinal', function($query){
                $query->where('semester_id', $this->semester->semester_id);
            });
            $query->where('bentuk_pendidikan_id', 15);
            //$query->where('soft_delete', 0);
        })->get();
        return response()->json(['status' => 'success', 'data' => $all_data, 'semester' => $this->semester]);
    }
    public function cek_koneksi(Request $request){
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/cek-koneksi', [
            'sekolah_id' => $request->sekolah_id,
        ]);
        if($response->successful()){
            $all_data = $response->json();
        } else {
            $all_data = ['status' => 'failed', 'data' => NULL];
        }
        //http://localhost:5774/rest/Pengguna?_dc=1601393946024&sekolah_id=d7b059e8-7141-4860-8161-3e8e7adfae7c&peran_id=10&aktif=%23IN0%2C1&callback=ptkgrid&page=1&start=0&limit=50
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function registrasi(Request $request){
        $response = Http::withOptions([
            'verify' => false,
        ])->withToken($request->token)->get('http://localhost:5774/WebService/getPengguna?npsn='.$request->npsn);
        //$all_data = json_decode($response->body());
        if($response->successful()){
            $all_user = $response->json();
            $encrypt = new Encrypt();
            foreach($all_user['rows'] as $user){
                $user = (object) $user;
                if($user->peran_id_str == 'Operator Sekolah' && $user->password){
                    $encrypt->setString($user->password);
                    $pengguna[] = [
                        'sekolah_id' => $user->sekolah_id,
                        'name' => $user->nama,
                        'email' => $user->username,
                        'password' => $encrypt->doDecrypt(),
                        'password_dapo' => $user->password,
                    ];
                }
            }
            $all_data = [
                'sekolah' => $this->get_sekolah($request, 1),
                'pengguna' => $pengguna,
            ];
            $response = Http::withOptions([
                'verify' => false,
            ])->post($request->url.'/api/dapodik/kirim-data', [
                'sekolah_id' => $request->sekolah_id,
                'semester_id' => $request->semester_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'data' => HelperModel::prepare_send(json_encode($all_data)),
                'permintaan' => 'registrasi',
            ]);
            if($response->successful()){
                return response()->json(['status' => 'success', 'data' => $all_data, 'response_erapor' => $response->json()]);
            } else {
                return response()->json(['status' => 'success', 'data' => $all_data, 'response_erapor' => NULL]);
            }
        } else {
            $all_data = NULL;
        }
    }
    public function debug(Request $request){
        $all_data = $this->get_ptk($request, 1);
        return response()->json(['status' => 'success', 'count' => count($all_data), 'data' => $all_data]);
    }
    public function hitung(){
        if(Storage::disk('public')->exists('kirim_data.json')){
            $data = Storage::disk('public')->get('kirim_data.json');
        } else {
            $data = json_encode(['text' => 'Memulai proses pengiriman data Dapodik ke Aplikasi eRapor SMK']);
        }
        $text = json_decode($data);
        $text = $text->text;
        return response()->json(['status' => 'success', 'data' => $text]);
    }
    public function kirim_data(Request $request){
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Referensi Wilayah']));
        $all_data = $this->get_wilayah();
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'wilayah',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Referensi Jurusan']));
        $all_data = $this->get_jurusan($request, 1);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Referensi Kurikulum']));
        $all_data = $this->get_kurikulum($request, 1);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Referensi Mata Pelajaran']));
        $all_data = $this->get_mata_pelajaran($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'mata_pelajaran',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Referensi Mata Pelajaran Kurikulum']));
        $all_data = $this->get_mata_pelajaran_kurikulum($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'mapel_kur',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data sekolah']));
        $all_data = $this->get_sekolah($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'sekolah',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data PTK']));
        $all_data = $this->get_ptk($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'guru',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Rombongan Belajar']));
        $all_data = $this->get_rombongan_belajar($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'rombongan_belajar',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Peserta Didik Aktif']));
        $all_data = $this->get_peserta_didik($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'siswa_aktif',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim kelengkapan data Peserta Didik Aktif']));
        $all_data = $this->get_kelengkapan_data($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'kelengkapan_data',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Peserta Didik Keluar']));
        $all_data = $this->get_peserta_didik($request->all() + ['keluar' => 1], 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'siswa_keluar',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Pembelajaran']));
        $all_data = $this->get_pembelajaran($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'pembelajaran',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Ekstrakurikuler']));
        $all_data = $this->get_ekskul($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'ekskul',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data Anggota Ekskul']));
        $all_data = $this->get_anggota_ekskul($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'anggota_ekskul',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengirim data DUDI']));
        $all_data = $this->get_dudi($request, 1);
        $response = Http::withOptions([
            'verify' => false,
        ])->post($request->url.'/api/dapodik/kirim-data', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'dudi',
        ]);
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Mengambil data referensi KD dari server']));
        $all_data = $this->get_count_kd($request, 1);
        /*$response = Http::withOptions([
            'verify' => false,
        ])->post('http://app.erapor-smk.net/api/sinkronisasi/get-kd', [
            'sekolah_id' => $request->sekolah_id,
            'semester_id' => $request->semester_id,
            'tahun_ajaran_id' => $request->tahun_ajaran_id,
            'data' => HelperModel::prepare_send(json_encode($all_data)),
            'permintaan' => 'dudi',
        ]);*/
        Storage::disk('public')->put('kirim_data.json', json_encode(['text' => 'Kirim data Dapodik ke Aplikasi eRapor SMK selesai!']));
        return response()->json(['status' => 'success', 'data' => 'Kirim data Dapodik ke Aplikasi eRapor SMK selesai!', 'response' => json_decode($response->body()), 'all_data' => $all_data]);
    }
    public function get_count_kd($request, $internal = 0){
        $response = Http::withOptions([
            'verify' => false,
        ])->post('http://app.erapor-smk.net/api/sinkronisasi/get-kd');
        return response()->json(['status' => 'success', 'data' => 'Kirim data Dapodik ke Aplikasi eRapor SMK selesai!', 'response' => json_decode($response->body()), 'all_data' => NULL]);
    }
    public function get_wilayah(){
        $data = Wilayah::whereRaw('last_sync >= last_update')->whereDate('last_update', '>=', $this->semester->tanggal_mulai)->orderBy('id_level_wilayah')->get();
        return $data;
    }
    public function get_kelengkapan_data($request, $internal = 0){
        $callback_rombel = function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('sekolah_id', $request->sekolah_id);
            $query->where('semester_id', $request->semester_id);
            $query->whereIn('jenis_rombel', [1,8,9]);
            $query->whereHas('ptk');
        };
        $data = Anggota_rombel::where(function($query) use ($request, $callback_rombel){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('jenis_pendaftaran_id', 1);
            $query->whereHas('rombongan_belajar', $callback_rombel);
        })->orWhere(function($query) use ($request, $callback_rombel){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('jenis_pendaftaran_id', 2);
            $query->whereHas('rombongan_belajar', $callback_rombel);
        })->with(['rombongan_belajar' => $callback_rombel, 'peserta_didik'])->get();
        return $data;
    }
    public function get_sekolah($request, $internal = 0){
        $data = Sekolah::with(['wilayah.parrentRecursive', 'jurusan_sp', 'ptk_terdaftar' => function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            //$query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->whereHas('ptk', function($query){
                $query->whereRaw('last_sync >= last_update');
                //$query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
                $query->where('jenis_ptk_id',20);
            });
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
            $query->where('sekolah_id', $request->sekolah_id);
            $query->whereNull('jenis_keluar_id');
            $query->whereNotNull('ptk_terdaftar_id');
            $query->with(['ptk.wilayah']);
        }, 'tugas_tambahan' => function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            //$query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->whereHas('ptk');
            $query->where('sekolah_id', $request->sekolah_id);
            $query->where('tst_tambahan', NULL);
            $query->where('jabatan_ptk_id', 2);
            $query->orWhere('sekolah_id', $request->sekolah_id);
            $query->whereNull('tst_tambahan');
            $query->where('jabatan_ptk_id', 33);
            $query->with(['ptk.wilayah.parrentRecursive']);
        }])->find($request->sekolah_id);
        if($internal){
            return ($data) ? $data->toArray() : NULL;
        }
        return response()->json(['error' => FALSE, 'dapodik' => $data]);
    }
    public function get_ptk($request, $internal = 0){
        $callback = function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('sekolah_id', $request->sekolah_id);
            $query->whereNull('jenis_keluar_id');
            $query->where('tahun_ajaran_id', $this->semester->tahun_ajaran_id);
        };
        $data = Ptk::whereHas('ptk_terdaftar', $callback)->with(['ptk_terdaftar' => $callback, 'wilayah.parrentRecursive', 'rwy_pend_formal' => function($query){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->whereNotIn('gelar_akademik_id', [9999, 99999]);
		    $query->whereNotNull('gelar_akademik_id');
        }]);
        $data = $data->get();
        if($internal){
            return $data;
        }
        return response()->json(['error' => FALSE, 'dapodik' => $data]);
    }
    public function get_rombongan_belajar($request, $internal = 0){
        $data = Rombongan_belajar::with(['jurusan_sp'])->where(function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('sekolah_id', $request->sekolah_id);
            $query->where('semester_id', $request->semester_id);
            $query->whereIn('jenis_rombel', [1,8,9]);
            $query->whereHas('ptk');
        });
        $data = $data->get();
        if($internal){
            return $data;
        }
        return response()->json(['error' => FALSE, 'dapodik' => $data]);
    }
    public function get_peserta_didik($request, $internal = 0){
        if(is_array($request)){
            $request = (object) $request;
        }
        $callback = function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('sekolah_id', $request->sekolah_id);
            if($request->keluar){
                $query->whereNotNull('jenis_keluar_id');
            } else {
                $query->whereNull('jenis_keluar_id');
            }
        };
        $callback_anggota = function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->whereHas('rombongan_belajar', function($query) use ($request){
                $query->whereRaw('last_sync >= last_update');
                $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
                $query->where('sekolah_id', $request->sekolah_id);
                $query->where('semester_id', $request->semester_id);
                $query->whereIn('jenis_rombel', [1,8,9]);
                $query->whereHas('ptk');
            });
        };
        $data = Peserta_didik::where(function($query) use ($callback, $callback_anggota, $request){
            $query->whereHas('registrasi_peserta_didik', $callback);
            $query->whereHas('anggota_rombel', $callback_anggota);
        })->with(['anggota_rombel' => $callback_anggota, 'anggota_rombel.rombongan_belajar' => function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('sekolah_id', $request->sekolah_id);
            $query->where('semester_id', $request->semester_id);
            $query->whereIn('jenis_rombel', [1,8,9]);
            $query->whereHas('ptk');
        }, 'registrasi_peserta_didik' => $callback, 'wilayah.parrentRecursive']);
        $data = $data->get();
        if($internal){
            return $data;
        }
        return response()->json(['error' => FALSE, 'dapodik' => $data]);
    }
    public function get_pembelajaran($request, $internal = 0){
        $data = Pembelajaran::with(['ptk_terdaftar'])->where(function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->whereHas('rombongan_belajar', function($query) use ($request){
                $query->whereRaw('last_sync >= last_update');
                $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
                $query->where('sekolah_id', $request->sekolah_id);
                $query->where('semester_id', $request->semester_id);
                $query->whereHas('ptk');
            });
            $query->where('semester_id', $request->semester_id);
            $query->whereHas('ptk_terdaftar');
        });
        $data = $data->get();
        if($internal){
            return $data;
        }
        return response()->json(['error' => FALSE, 'dapodik' => $data]);
    }
    public function get_ekskul($request, $internal = 0){
        $data = Kelas_ekskul::where(function($query) use ($request){
            $query->whereHas('rombongan_belajar', function($query) use ($request){
                $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
                $query->where('sekolah_id', $request->sekolah_id);
                $query->where('semester_id', $request->semester_id);
                $query->whereHas('ptk');
            });
        })->with(['rombongan_belajar.ruang']);
        $data = $data->get();
        if($internal){
            return $data;
        }
        return response()->json(['error' => FALSE, 'dapodik' => $data]);
    }
    public function get_anggota_ekskul($request, $internal = 0){
        $callback = function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('sekolah_id', $request->sekolah_id);
            $query->where('semester_id', $request->semester_id);
            $query->where('jenis_rombel', 51);
            $query->whereHas('ptk');
            $query->whereHas('kelas_ekskul');
            if($request->rombongan_belajar_id){
                $query->where('rombongan_belajar_id', $request->rombongan_belajar_id);
            }
        };
        $data = Anggota_rombel::whereHas('rombongan_belajar', $callback)->whereRaw('last_sync >= last_update')->with(['rombongan_belajar' => $callback]);
        $data = $data->get();
        if($internal){
            return $data;
        }
        return response()->json(['error' => FALSE, 'dapodik' => $data]);
    }
    public function get_dudi($request, $internal = 0){
        $callback = function($query) use ($request){
            $query->where('sekolah_id', $request->sekolah_id);
            $query->with(['akt_pd' => function($query){
                $query->with(['anggota_akt_pd' => function($query){
                    $query->with(['registrasi_peserta_didik']);
                }, 'bimbing_pd']);
            }]);
        };
        $data = Dudi::where(function($query) use ($callback){
            $query->whereHas('mou', $callback);
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
        })->with(['mou' => $callback]);
        $data = $data->get();
        if($internal){
            return $data;
        }
        return response()->json(['error' => FALSE, 'dapodik' => $data]);
    }
    public function get_jurusan($request, $internal = 0){
        $data = Jurusan::where(function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->where('untuk_smk', 1);
        })->get();
        return $data;
    }
    public function get_kurikulum($request, $internal = 0){
        $data = Kurikulum::where(function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->whereHas('jenjang_smk');
        })->get();
        return $data;
    }
    public function get_mata_pelajaran($request, $internal = 0){
        $data = Mata_pelajaran::where(function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->whereHas('jurusan_smk');
            /*if($request->updated_at){
                $query->where('last_update >=', $request->updated_at);
            } elseif($request->last_sync){
                $query->where('last_sync >=', $request->last_sync);
            }*/
        })->get();
        return $data;
    }
    public function get_mata_pelajaran_kurikulum($request, $internal = 0){
        $data = Mata_pelajaran_kurikulum::where(function($query) use ($request){
            $query->whereRaw('last_sync >= last_update');
            $query->whereDate('last_update', '>=', $this->semester->tanggal_mulai);
            $query->whereHas('kurikulum_smk');
        })->get();
        return $data;
    }
}
