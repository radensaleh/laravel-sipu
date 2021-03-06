<?php

namespace App\Http\Controllers;

use DB;
use PDF;
use App\AdminUKM;
use App\UKM;
use App\Admin;
use App\Mahasiswa;
use App\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class AdminUKMController extends Controller
{
      public function logout(Request $request){
        $request->session()->forget('id_admin');
        return redirect()->route('home');
      }

      public function adminUkmPage(Request $request){
        if($request->session()->exists('id_admin')){
          $id_admin = $request->session()->get('id_admin');
          if( $id_admin == 'ADM01' ){
            return redirect()->route('dashboardKompa');
          }else if( $id_admin == 'ADM02' ){
            return redirect()->route('dashboardKopen');
          }else if( $id_admin == 'ADM03' ){
            return redirect()->route('dashboardRpi');
          }else if( $id_admin == 'ADM04' ){
            return redirect()->route('dashboardPopi');
          }else if( $id_admin == 'ADM05' ){
            return redirect()->route('dashboardFolafo');
          }
        }else{
          return view('home');
        }
      }

      //LOGIN ADMIN UKM
      public function doLogin(Request $request){
        $auth = auth()->guard('adminUkm');

          $credentials = [
            'id_admin' => $request->id_admin,
            'id_ukm'   => $request->id_ukm,
            'email'    => $request->email,
            'password' => $request->password,
          ];

        if( $auth->attempt($credentials) ){
            $request->session()->put('id_admin', $request->id_admin);
            return response()->json([
              'error'   => 0,
              'message' => 'Login Success',
              'ukm'     => $request->id_ukm
            ], 200);
        }else{
            return response()->json([
              'error' => 1,
              'message' => 'Wrong ID, ID UKM, Email or Password'
            ], 200);
        }
      }

      //ADMIN UKM KOMPA [UKM01]
      public function dashboardKompa(Request $request){
          $id = $request->session()->get('id_admin');
          if(!$request->session()->exists('id_admin')){
            return redirect()->route('home');
          }else{
            $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
            $getIdUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

            $countMHS    = Mahasiswa::count();
            $countDaftar = Pendaftaran::count();

            $daftarKompa = DB::table('tb_pendaftaran')->where('id_ukm', $getIdUkm)->count();

            return view('adminUkm.kompa.dashboard', compact('getid','daftarKompa','countMHS','countDaftar'));
          }

      }
      public function data_kompa(Request $request){
         $id = $request->session()->get('id_admin');
         if(!$request->session()->exists('id_admin')){
           return redirect()->route('home');
         }else{
           $getid       = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
           $getidUkm    = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');
           $countDaftar = Pendaftaran::count();

           $dataKompa = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();

           return view('adminUkm.kompa.datakompa',
              compact('getid','countDaftar','dataKompa')
           );
         }
      }
      public function PdfKompa(Request $request){
          $id = $request->session()->get('id_admin');
          $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
          $getidUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

          $dataKompa = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();
          $pdfKompa  = PDF::loadView('adminUkm.kompa.pdfkompa',   compact('getid','dataKompa'));
          return $pdfKompa->download('Pdf-Kompa.pdf');
      }
      public function destroyKompa(Request $request){
          $id = Pendaftaran::findOrFail($request->id_pendaftaran);
          $id->delete();

          if( $id ){
            return response()->json([
              'error' => 0,
              'message' => 'Success Delete Data'
            ], 200);
          }
      }

      //ADMIN UKM KOPEN [UKM02]
      public function dashboardKopen(Request $request){
          $id = $request->session()->get('id_admin');
          if(!$request->session()->exists('id_admin')){
            return redirect()->route('home');
          }else{
            $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
            $getIdUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

            $countMHS    = Mahasiswa::count();
            $countDaftar = Pendaftaran::count();

            $daftarKopen = DB::table('tb_pendaftaran')->where('id_ukm', $getIdUkm)->count();
            return view('adminUkm.kopen.dashboard', compact('getid','daftarKopen','countMHS','countDaftar'));
          }
      }
      public function data_kopen(Request $request){
         $id = $request->session()->get('id_admin');
         if(!$request->session()->exists('id_admin')){
           return redirect()->route('home');
         }else{
           $getid       = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
           $getidUkm    = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');
           $countDaftar = Pendaftaran::count();

           $dataKopen = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();

           return view('adminUkm.kopen.datakopen',
              compact('getid','countDaftar','dataKopen')
           );
         }
      }
      public function PdfKopen(Request $request){
          $id = $request->session()->get('id_admin');
          $getid     = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
          $getidUkm  = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

          $dataKopen = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();
          $pdfKopen  = PDF::loadView('adminUkm.kopen.pdfkopen',   compact('getid','dataKopen'));
          return $pdfKopen->download('Pdf-Kopen.pdf');
      }
      public function destroyKopen(Request $request){
          $id = Pendaftaran::findOrFail($request->id_pendaftaran);
          $id->delete();

          if( $id ){
            return response()->json([
              'error' => 0,
              'message' => 'Success Delete Data'
            ], 200);
          }
      }

      //ADMIN UKM RPI [UKM03]
      public function dashboardRpi(Request $request){
          $id = $request->session()->get('id_admin');
          if(!$request->session()->exists('id_admin')){
            return redirect()->route('home');
          }else{
            $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
            $getIdUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

            $countMHS    = Mahasiswa::count();
            $countDaftar = Pendaftaran::count();

            $daftarRpi = DB::table('tb_pendaftaran')->where('id_ukm', $getIdUkm)->count();

            return view('adminUkm.rpi.dashboard', compact('getid','daftarRpi','countMHS','countDaftar'));
          }
      }
      public function data_rpi(Request $request){
         $id = $request->session()->get('id_admin');
         if(!$request->session()->exists('id_admin')){
           return redirect()->route('home');
         }else{
           $getid       = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
           $getidUkm    = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');
           $countDaftar = Pendaftaran::count();

           $dataRpi = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();

           return view('adminUkm.rpi.datarpi',
              compact('getid','countDaftar','dataRpi')
           );
         }
      }
      public function PdfRpi(Request $request){
          $id = $request->session()->get('id_admin');
          $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
          $getidUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

          $dataRpi  = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();
          $pdfRpi   = PDF::loadView('adminUkm.rpi.pdfrpi',   compact('getid','dataRpi'));
          return $pdfRpi->download('Pdf-Rpi.pdf');
      }
      public function destroyRpi(Request $request){
          $id = Pendaftaran::findOrFail($request->id_pendaftaran);
          $id->delete();

          if( $id ){
            return response()->json([
              'error' => 0,
              'message' => 'Success Delete Data'
            ], 200);
          }
      }

      //ADMIN UKM POPI [UKM04]
      public function dashboardPopi(Request $request){
          $id = $request->session()->get('id_admin');
          if(!$request->session()->exists('id_admin')){
            return redirect()->route('home');
          }else{
            $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
            $getIdUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

            $countMHS    = Mahasiswa::count();
            $countDaftar = Pendaftaran::count();

            $daftarPopi = DB::table('tb_pendaftaran')->where('id_ukm', $getIdUkm)->count();
            return view('adminUkm.popi.dashboard', compact('getid','daftarPopi','countMHS','countDaftar'));
          }
      }
      public function data_popi(Request $request){
         $id = $request->session()->get('id_admin');
         if(!$request->session()->exists('id_admin')){
           return redirect()->route('home');
         }else{
           $getid       = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
           $getidUkm    = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');
           $countDaftar = Pendaftaran::count();

           $dataPopi = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();

           return view('adminUkm.popi.datapopi',
              compact('getid','countDaftar','dataPopi')
           );
         }
      }
      public function PdfPopi(Request $request){
          $id = $request->session()->get('id_admin');
          $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
          $getidUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

          $dataPopi = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();
          $pdfPopi  = PDF::loadView('adminUkm.popi.pdfpopi',   compact('getid','dataPopi'));
          return $pdfPopi->download('Pdf-Popi.pdf');
      }
      public function destroyPopi(Request $request){
          $id = Pendaftaran::findOrFail($request->id_pendaftaran);
          $id->delete();

          if( $id ){
            return response()->json([
              'error' => 0,
              'message' => 'Success Delete Data'
            ], 200);
          }
      }

      //ADMIN UKM FOLAFO [UKM05]
      public function dashboardFolafo(Request $request){
          $id = $request->session()->get('id_admin');
          if(!$request->session()->exists('id_admin')){
            return redirect()->route('home');
          }else{
            $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
            $getIdUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

            $countMHS    = Mahasiswa::count();
            $countDaftar = Pendaftaran::count();

            $daftarFolafo = DB::table('tb_pendaftaran')->where('id_ukm', $getIdUkm)->count();
            return view('adminUkm.folafo.dashboard', compact('getid','daftarFolafo','countMHS','countDaftar'));

          }
      }
      public function data_folafo(Request $request){
         $id = $request->session()->get('id_admin');
         if(!$request->session()->exists('id_admin')){
           return redirect()->route('home');
         }else{
           $getid       = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
           $getidUkm    = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');
           $countDaftar = Pendaftaran::count();

           $dataFolafo = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();

           return view('adminUkm.folafo.datafolafo',
              compact('getid','countDaftar','dataFolafo')
           );
         }
      }
      public function PdfFolafo(Request $request){
          $id = $request->session()->get('id_admin');
          $getid    = DB::table('tb_admin_ukm')->where('id_admin', $id)->first();
          $getidUkm = DB::table('tb_admin_ukm')->where('id_admin', $id)->value('id_ukm');

          $dataFolafo = Pendaftaran::with('mahasiswa')->where('id_ukm', $getidUkm)->get();
          $pdfFolafo  = PDF::loadView('adminUkm.folafo.pdffolafo',   compact('getid','dataFolafo'));
          return $pdfFolafo->download('Pdf-Folafo.pdf');
      }
      public function destroyFolafo(Request $request){
          $id = Pendaftaran::findOrFail($request->id_pendaftaran);
          $id->delete();

          if( $id ){
            return response()->json([
              'error' => 0,
              'message' => 'Success Delete Data'
            ], 200);
          }
      }


      //PAGE ADMIN WEB MENU ADMIN UKM
      public function show_dataadmin(Request $request){
          if(!$request->session()->get('id_admin')){
            return redirect()->route('adminpage');
          }else{
            $adminUKM    = AdminUKM::all();
            $dataUKM     = UKM::all();
            $admin         = Admin::all();
            $countMHS    = Mahasiswa::count();
            $countDaftar = Pendaftaran::count();

            return view('adminWeb.dataadmin',
                compact(
                  'countMHS',
                  'adminUKM',
                  'dataUKM',
                  'countDaftar',
                  'admin'
                )
            );
          }

      }

      public function store(Request $request){
          $checkIfExist = AdminUKM::where('id_admin', $request->id_admin)->get();

          if( count($checkIfExist) != 0 ){
            return response()->json([
               'error' => 1,
               'message' => 'Id Admin Already Exist'
             ], 200);
          }

          $create = AdminUKM::create($request->all());
          if( $create ) {
             return response()->json([
               'error' => 0,
               'message' => 'Success Add Data'
             ], 200);
        }
      }

      public function update(Request $request){
            $adminUKM = AdminUKM::findOrFail($request->id_admin);
            $adminUKM->update($request->all());

            if( $adminUKM ){
              return response()->json([
                'error' => 0,
                'message' => 'Success Edit Data'
              ], 200);
            }
      }

      public function destroy(Request $request){
         $adminUKM = AdminUKM::findOrFail($request->id_admin);
         $adminUKM->delete();

         if( $adminUKM ){
           return response()->json([
             'error' => 0,
             'message' => 'Success Delete Data'
           ], 200);
         }
      }
}
