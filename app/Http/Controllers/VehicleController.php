<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $vehicles = Vehicle::when($q, fn ($query) => $query->where(fn ($inner) => $inner->where('plate_number', 'like', "%{$q}%")->orWhere('brand', 'like', "%{$q}%")->orWhere('model', 'like', "%{$q}%")->orWhere('owner_name', 'like', "%{$q}%")))->latest()->paginate(10)->withQueryString();
        return view('vehicles.index', compact('vehicles', 'q'));
    }
    public function show(Vehicle $vehicle) { return view('vehicles.show', ['vehicle' => $vehicle, 'services' => $vehicle->services()->with('parts')->latest('service_date')->get()]); }
    public function create() { return view('vehicles.form', ['vehicle' => new Vehicle()]); }
    public function store(Request $request) { Vehicle::create($this->validated($request)); return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil ditambahkan.'); }
    public function edit(Vehicle $vehicle) { return view('vehicles.form', compact('vehicle')); }
    public function update(Request $request, Vehicle $vehicle) { $vehicle->update($this->validated($request, $vehicle)); return redirect()->route('vehicles.index')->with('success', 'Data kendaraan berhasil diperbarui.'); }
    public function destroy(Vehicle $vehicle) { $vehicle->delete(); return back()->with('success', 'Data kendaraan berhasil dihapus.'); }
    private function validated(Request $request, ?Vehicle $vehicle = null): array
    {
        return $request->validate(['plate_number' => ['required', 'max:20', Rule::unique('vehicles', 'plate_number')->ignore($vehicle)], 'brand' => 'required|max:80', 'model' => 'required|max:80', 'year' => 'required|integer|min:1900|max:'.(now()->year + 1), 'owner_name' => 'required|max:120', 'owner_phone' => 'nullable|max:30', 'current_odometer' => 'required|integer|min:0', 'notes' => 'nullable|max:2000']);
    }
}
