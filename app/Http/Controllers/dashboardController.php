<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Jurnal;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AbsenDatang;
use App\Models\AbsenPulang;
use App\Models\RPP;


class dashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $jurnal = Jurnal::count();
        $user= User::count();
        $absendatang= AbsenDatang::count();
        $absenpulang= AbsenPulang::count();
        $rpp= RPP::count();

        $active = 'dashboard';
        return view('dashboard.index', compact('jurnal', 'user', 'absendatang', 'absenpulang', 'rpp', 'active'));
    }
}
