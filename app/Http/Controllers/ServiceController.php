<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $services = Service::with('vehicle')->when($q, fn ($query) => $query->whereHas('vehicle', fn ($vehicle) => $vehicle->where('plate_number', 'like', "%{$q}%")->orWhere('owner_name', 'like', "%{$q}%"))->orWhere('service_type', 'like', "%{$q}%"))->latest('service_date')->paginate(12)->withQueryString();
        return view('services.index', compact('services', 'q'));
    }
    public function create(Request $request) { return view('services.form', ['service' => new Service(['vehicle_id' => $request->integer('vehicle_id')]), 'vehicles' => Vehicle::orderBy('plate_number')->get()]); }
    public function store(Request $request) { $service = $this->save($request); return redirect()->route('services.show', $service)->with('success', 'Data servis berhasil dicatat.'); }
    public function show(Service $service) { $service->load('vehicle', 'parts'); return view('services.show', compact('service')); }
    public function edit(Service $service) { return view('services.form', ['service' => $service->load('parts'), 'vehicles' => Vehicle::orderBy('plate_number')->get()]); }
    public function update(Request $request, Service $service) { $service = $this->save($request, $service); return redirect()->route('services.show', $service)->with('success', 'Data servis berhasil diperbarui.'); }
    public function destroy(Service $service) { $service->delete(); return redirect()->route('services.index')->with('success', 'Data servis berhasil dihapus.'); }
    private function save(Request $request, ?Service $service = null): Service
    {
        $data = $request->validate(['vehicle_id' => 'required|exists:vehicles,id', 'service_date' => 'required|date', 'service_type' => 'required|max:100', 'odometer' => 'required|integer|min:0', 'description' => 'nullable|max:2000', 'labor_cost' => 'nullable|numeric|min:0', 'next_service_date' => 'nullable|date|after_or_equal:service_date', 'next_service_odometer' => 'nullable|integer|min:0', 'status' => 'required|in:Selesai,Diproses', 'parts' => 'nullable|array', 'parts.*.name' => 'nullable|max:100', 'parts.*.quantity' => 'nullable|integer|min:1', 'parts.*.unit_price' => 'nullable|numeric|min:0']);
        return DB::transaction(function () use ($data, $service) {
            $parts = collect($data['parts'] ?? [])->filter(fn ($part) => filled($part['name'] ?? null))->values();
            $partsCost = $parts->sum(fn ($part) => (int) ($part['quantity'] ?? 1) * (float) ($part['unit_price'] ?? 0));
            unset($data['parts']); $data['labor_cost'] = $data['labor_cost'] ?? 0; $data['parts_cost'] = $partsCost; $data['total_cost'] = $data['labor_cost'] + $partsCost;
            $service = $service ? tap($service)->update($data) : Service::create($data);
            $service->parts()->delete();
            foreach ($parts as $part) $service->parts()->create(['name' => $part['name'], 'quantity' => $part['quantity'] ?? 1, 'unit_price' => $part['unit_price'] ?? 0, 'subtotal' => ($part['quantity'] ?? 1) * ($part['unit_price'] ?? 0)]);
            $service->vehicle->update(['current_odometer' => max($service->vehicle->current_odometer, $service->odometer)]);
            return $service;
        });
    }
}
