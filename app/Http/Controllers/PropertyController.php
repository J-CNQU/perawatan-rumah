<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{

    public function index()
    {
        $properties = auth()->user()->properties()->with('assets')->latest()->get();

        return view('dashboard', compact('properties'));
    }

    public function show(Property $property)
    {
        $property = auth()->user()->properties()
            ->with(['assets.category', 'assets.maintenanceLogs' => function ($query) {
                $query->latest('service_date');
            }])
            ->findOrFail($property->id);

        $totalMaintenanceCost = $property->assets
            ->sum(fn ($asset) => $asset->maintenanceLogs->sum('cost'));

        $totalAssets = $property->assets->count();
        $healthyAssets = $property->assets->where('condition', 'Normal')->count();
        $healthScore = $totalAssets > 0 ? round(($healthyAssets / $totalAssets) * 100) : 100;

        $upcomingReminders = [];

        foreach ($property->assets as $asset) {
            $latestLog = $asset->maintenanceLogs->sortByDesc('service_date')->first();
            $baseDate = $latestLog?->service_date
                ? $latestLog->service_date
                : ($asset->purchase_date ?? now()->toDateString());

            $intervalMonths = max((int) ($asset->maintenance_interval_months ?? 3), 1);
            $nextServiceDate = \Carbon\Carbon::parse($baseDate)->addMonths($intervalMonths);
            $daysUntilService = now()->diffInDays($nextServiceDate, false);
            $isDue = $daysUntilService <= 30 && $daysUntilService >= -30;

            if ($isDue) {
                $upcomingReminders[] = [
                    'asset' => $asset,
                    'next_service_date' => $nextServiceDate,
                    'days_until_service' => $daysUntilService,
                    'status' => $daysUntilService < 0 ? 'Terlewat' : 'Mendekati',
                ];
            }
        }

        usort($upcomingReminders, fn ($a, $b) => $a['next_service_date'] <=> $b['next_service_date']);

        return view('properties.show', compact('property', 'totalMaintenanceCost', 'healthScore', 'upcomingReminders'));
    }

    public function exportPdf(Property $property)
    {
        $property = auth()->user()->properties()
            ->with(['assets.category', 'assets.maintenanceLogs' => function ($query) {
                $query->latest('service_date');
            }])
            ->findOrFail($property->id);

        $totalMaintenanceCost = $property->assets
            ->sum(fn ($asset) => $asset->maintenanceLogs->sum('cost'));

        $healthScore = $property->assets->count() > 0
            ? round(($property->assets->where('condition', 'Normal')->count() / $property->assets->count()) * 100)
            : 100;

        return view('properties.pdf', compact('property', 'totalMaintenanceCost', 'healthScore'));
    }

    public function storeAsset(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'condition' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);

        // Preserve/display user-facing condition labels (e.g. 'Normal', 'Perlu Servis', 'Rusak')
        $normalisedCondition = strtolower(trim($validated['condition']));
        $normalisedCondition = str_replace(['_', '-'], ' ', $normalisedCondition);
        $conditionMap = [
            'normal' => 'Normal',
            'good' => 'Normal',
            'perlu servis' => 'Perlu Servis',
            'perlu_servis' => 'Perlu Servis',
            'rusak' => 'Rusak',
            'broken' => 'Rusak',
        ];

        $validated['condition'] = $conditionMap[$normalisedCondition] ?? ucwords($normalisedCondition);

        $categoryName = trim($validated['category']);
        $categoryName = str_replace(['_', '-'], ' ', $categoryName);
        $categoryName = ucwords(strtolower($categoryName));

        $category = Category::firstOrCreate([
            'name' => $categoryName,
        ]);

        $property->assets()->create([
            'category_id' => $category->id,
            'name' => trim($validated['name']),
            'condition' => $validated['condition'],
            'purchase_date' => $validated['purchase_date'] ?? now()->toDateString(),
            'purchase_price' => $validated['purchase_price'] ?? 0,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('properties.show', $property)->with('success', 'Aset berhasil ditambahkan.');
    }

    public function storeMaintenanceLog(Request $request, Property $property)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'type' => 'required|string|max:30',
            'service_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $typeMap = [
            'routine checkup' => 'Routine Checkup',
            'routine_checkup' => 'Routine Checkup',
            'repair' => 'Repair',
            'replacement' => 'Replacement',
        ];

        $validated['type'] = $typeMap[strtolower(trim($validated['type']))] ?? trim($validated['type']);

        $asset = $property->assets()->findOrFail($validated['asset_id']);

        $asset->maintenanceLogs()->create([
            'type' => $validated['type'],
            'service_date' => $validated['service_date'],
            'cost' => $validated['cost'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('properties.show', $property)->with('success', 'Log perawatan berhasil ditambahkan.');
    }

    public function updateAsset(Request $request, Property $property, \App\Models\Asset $asset)
    {
        abort_unless($asset->property_id === $property->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'condition' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
        ]);

        // Preserve/display user-facing condition labels (e.g. 'Normal', 'Perlu Servis', 'Rusak')
        $normalisedCondition = strtolower(trim($validated['condition']));
        $normalisedCondition = str_replace(['_', '-'], ' ', $normalisedCondition);
        $conditionMap = [
            'normal' => 'Normal',
            'good' => 'Normal',
            'perlu servis' => 'Perlu Servis',
            'perlu_servis' => 'Perlu Servis',
            'rusak' => 'Rusak',
            'broken' => 'Rusak',
        ];

        $validated['condition'] = $conditionMap[$normalisedCondition] ?? ucwords($normalisedCondition);

        $categoryName = trim($validated['category']);
        $categoryName = str_replace(['_', '-'], ' ', $categoryName);
        $categoryName = ucwords(strtolower($categoryName));

        $category = Category::firstOrCreate(['name' => $categoryName]);

        $asset->update([
            'category_id' => $category->id,
            'name' => trim($validated['name']),
            'condition' => $validated['condition'],
            'purchase_date' => $validated['purchase_date'] ?? $asset->purchase_date,
            'purchase_price' => $validated['purchase_price'] ?? $asset->purchase_price,
            'description' => $validated['description'] ?? $asset->description,
        ]);

        return redirect()->route('properties.show', $property)->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroyAsset(Property $property, \App\Models\Asset $asset)
    {
        abort_unless($asset->property_id === $property->id, 404);

        $asset->delete();

        return redirect()->route('properties.show', $property)->with('success', 'Aset berhasil dihapus.');
    }

    public function updateMaintenanceLog(Request $request, Property $property, \App\Models\MaintenanceLog $maintenanceLog)
    {
        abort_unless($maintenanceLog->asset?->property_id === $property->id, 404);

        $validated = $request->validate([
            'type' => 'required|string|max:30',
            'service_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $typeMap = [
            'routine checkup' => 'Routine Checkup',
            'routine_checkup' => 'Routine Checkup',
            'repair' => 'Repair',
            'replacement' => 'Replacement',
        ];

        $validated['type'] = $typeMap[strtolower(trim($validated['type']))] ?? trim($validated['type']);

        $maintenanceLog->update([
            'type' => $validated['type'],
            'service_date' => $validated['service_date'],
            'cost' => $validated['cost'],
            'notes' => $validated['notes'] ?? $maintenanceLog->notes,
        ]);

        return redirect()->route('properties.show', $property)->with('success', 'Log perawatan berhasil diperbarui.');
    }

    public function destroyMaintenanceLog(Property $property, \App\Models\MaintenanceLog $maintenanceLog)
    {
        abort_unless($maintenanceLog->asset?->property_id === $property->id, 404);

        $maintenanceLog->delete();

        return redirect()->route('properties.show', $property)->with('success', 'Log perawatan berhasil dihapus.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Home,Office,Store,Other',
            'address' => 'nullable|string|max:500',
        ]);

        auth()->user()->properties()->create($validated);

        return redirect()->route('dashboard')->with('success', 'Lokasi berhasil ditambahkan!');
    }
}