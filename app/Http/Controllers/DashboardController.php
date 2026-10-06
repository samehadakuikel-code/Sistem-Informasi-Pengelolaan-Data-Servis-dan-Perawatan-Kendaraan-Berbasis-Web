<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->startOfDay();
        $allUpcoming = Service::with('vehicle')->whereNotNull('next_service_date')->orderBy('next_service_date')->get();
        $upcoming = $allUpcoming->take(6);
        $overdue = $allUpcoming->filter(fn (Service $service) => $service->next_service_date->lt($today));
        $dueSoon = $allUpcoming->filter(fn (Service $service) => $service->next_service_date->between($today, $today->copy()->addDays(7)));
        $months = collect(range(5, 0))->map(function (int $offset) {
            $date = now()->subMonths($offset);
            return ['label' => $date->translatedFormat('M'), 'total' => (float) Service::whereYear('service_date', $date->year)->whereMonth('service_date', $date->month)->sum('total_cost')];
        });
        $maxMonthlyCost = max(1, $months->max('total'));
        return view('dashboard', [
            'vehicleCount' => Vehicle::count(),
            'serviceCount' => Service::count(),
            'monthlyCost' => Service::whereBetween('service_date', [now()->startOfMonth(), now()->endOfMonth()])->sum('total_cost'),
            'recentServices' => Service::with('vehicle')->latest('service_date')->take(5)->get(),
            'upcoming' => $upcoming,
            'overdue' => $overdue,
            'dueSoon' => $dueSoon,
            'months' => $months,
            'maxMonthlyCost' => $maxMonthlyCost,
            'totalCost' => Service::sum('total_cost'),
        ]);
    }
}
