<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Sekolah;
class DapodikController extends Controller
{
    public function sekolah(Request $request){
        $all_data = Sekolah::where('bentuk_pendidikan_id', 15)->where('soft_delete', 0)->get();
        return response()->json(['status' => 'success', 'data' => $all_data]);
    }
    public function cek_koneksi(Request $request){
        $response = Http::post($request->url.'/api/dapodik/cek-koneksi', [
            'sekolah_id' => $request->sekolah_id,
        ]);
        $all_data = $request->all();
        return response()->json(['status' => 'success', 'data' => json_decode($response->body())]);
    }
    public function kirim_data($data = NULL){
        if(!$data){
            $response = ['status' => 'success', 'data' => 'Sinkronisasi Referensi Jurusan'];
            echo json_encode($response);
            return $this->kirim_data('kurikulum');
        } elseif($data == 'kurikulum'){
            $response = ['status' => 'success', 'data' => 'Sinkronisasi Referensi Kurikulum'];
            echo json_encode($response);
            //return $this->kirim_data('kurikulum');
        }
    }
}
