<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString()); $to = $request->input('to', now()->toDateString());
        $services = Service::with('vehicle')->whereBetween('service_date', [$from, $to])->latest('service_date')->get();
        return view('reports.index', compact('services', 'from', 'to'));
    }
}
