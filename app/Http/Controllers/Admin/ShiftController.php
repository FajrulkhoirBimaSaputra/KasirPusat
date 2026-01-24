<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Shift;
use App\Models\User;
class ShiftController extends Controller
{

public function index(Request $request)
{
    $month = $request->get('month', now()->format('Y-m'));
    $date = Carbon::createFromFormat('Y-m', $month)->startOfMonth();

    $start = $date->copy()->startOfWeek(Carbon::SUNDAY);
    $end   = $date->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

    $period = CarbonPeriod::create($start, $end);

    $shifts = Shift::with('user')
        ->whereBetween('tanggal', [$start, $end])
        ->get()
        ->keyBy('tanggal');

    return view('shift.index', [
        'date' => $date,
        'period' => $period,
        'kasirs' => User::whereRole('kasir')->get(),
        'shifts' => $shifts
    ]);
}

public function store(Request $request)
{
    foreach ($request->shifts ?? [] as $tanggal => $user_id) {
        if (!$user_id) continue;

        Shift::updateOrCreate(
            ['tanggal' => $tanggal],
            ['user_id' => $user_id]
        );
    }

    return back()->with('success', 'Jadwal shift berhasil disimpan');
}
}