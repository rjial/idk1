<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Absen;
use Alert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Datatables;


class AbsenController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Absen::select('*')
            ->where('user_id', Auth::user()->id)
            ->where('tanggal', Date("Y-m-d"))
            ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('foto', function ($data) {
                    $url= asset('images/absen/'.$data->foto);
                    return '<img src="'.$url.'" width="70" alt="..." />'; 
                })
                ->addColumn('action', function ($data) {
                    $button = ' <a href="'. route("absen-Edit.guru", $data->id).'" class="edit btn btn-success " id="' . $data->id . '" ><i class="fa fa-edit"></i></a>';
                    $button .= ' <a href="'. route("absen.Destroy", $data->id).'" class="hapus btn btn-danger" id="' . $data->id . '" ><i class="fa fa-trash"></i></a>';
                    return $button;
                })
                ->rawColumns(['foto', 'action'])
                ->make(true);
        }
        return view('guru.absendatang');
    }

    public function riwayat(Request $request)
    {
        if ($request->ajax()) {
            $data = Absen::select('*')
            ->where('user_id', Auth::user()->id)
            ->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('foto', function ($data) {
                    $url= asset('images/absen/'.$data->foto);
                    return '<img src="'.$url.'" width="70" alt="..." />'; 
                })
                ->rawColumns(['foto'])
                ->make(true);
        }
        return view('guru.riwayatabsen');
    }

    public function create()
    {
       return view('guru.absendatangCreate');
    }
   

    public function save(Request $request)
    {
        $date = Carbon::now();
        $user = User::where('id', Auth::user()->id)->first();
        $datang = Absen::where('user_id', $user->id)->first();

        $datang = new Absen;
        $datang->user_id = $user->id;
        $datang->tanggal = $date;
        $datang->status = $request->status;
        $datang->kondisi = $request->kondisi;
        $file = $request->file('foto');
        //Mendapatkan nama file
        $nama_file = $file->getClientOriginalName();

        // Mendapatkan Extension File
        $extension = $file->getClientOriginalExtension();
    
        // Mendapatkan Ukuran File
        $ukuran_file = $file->getSize();
     
        // Proses Upload File
        $destinationPath = 'images\absen';
        $file->move($destinationPath,$file->getClientOriginalName());
        $datang->foto = $nama_file;
        $datang->save();
        alert()->success('Absen Berhasil Disimpan');
        return redirect('absen-guru');
    }

   public function edit($id)
   {

       $datang = Absen::find($id);
       return view('guru.absendatangEdit',compact('datang'));
   }

    public function update(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $file = $request->file('foto');
        if($request->file('foto') != null){
            // dd($file);
            $ext_foto = $file->extension();
            $filename = $file->move(public_path() . '/images/absen/', $file->getClientOriginalName());
            $date = Carbon::now();
            $datang = Absen::where('id', $id)->first();
            // dd($absen_datang);
            $datang->tanggal      = $date;
            $datang->status       = $request->status;
            $datang->kondisi      = $request->kondisi;
            $datang->foto         = $file->getClientOriginalName();
            $datang->updated_at   = date('Y-m-d H:i:s');
            $datang->save();
        }else{
            $date = Carbon::now();
            DB::table('absen')->where('id',$id)->update([
                'tanggal' => $date,
                'status' => $request->status,
                'kondisi' => $request->kondisi,
                'foto' => $request->foto_old,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
        alert()->success('Absen Telah Diupdate', 'Success');
        return redirect('absen-guru');

    }

    public function destroy($id)
    {
        $data = Absen::find($id);
        $data->delete();
        return redirect('absen-guru');
    }
}