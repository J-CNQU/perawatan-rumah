<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $properties = $user->properties()
            ->with(['assets.category', 'assets.maintenanceLogs' => function ($query) {
                $query->latest('service_date');
            }])
            ->latest()
            ->get();

        $monthlySalary = (float) ($user->monthly_salary ?? 0);
        $maintenanceBudgetPercentage = (int) ($user->maintenance_budget_percentage ?? 10);

        $allocatedBudget = ($monthlySalary * $maintenanceBudgetPercentage) / 100;

        $currentMonthExpenses = $properties
            ->flatMap(fn (Property $property) => $property->assets)
            ->flatMap(fn ($asset) => $asset->maintenanceLogs)
            ->filter(fn ($log) => $log && $log->service_date)
            ->filter(function ($log) {
                $serviceDate = Carbon::parse($log->service_date);

                return $serviceDate->month === now()->month && $serviceDate->year === now()->year;
            })
            ->sum('cost');

        $budgetUsedPercentage = $allocatedBudget > 0
            ? ($currentMonthExpenses / $allocatedBudget) * 100
            : 0;

        if ($budgetUsedPercentage < 80) {
            $financialStatus = 'Aman';
        } elseif ($budgetUsedPercentage <= 100) {
            $financialStatus = 'Waspada';
        } else {
            $financialStatus = 'Over Budget';
        }

        foreach ($properties as $property) {
            $totalAssets = $property->assets->count();
            $normalAssets = $property->assets->where('condition', 'Normal')->count();
            $property->health_score = $totalAssets > 0 ? round(($normalAssets / $totalAssets) * 100) : 100;
        }

        return view('dashboard', compact(
            'properties',
            'allocatedBudget',
            'currentMonthExpenses',
            'budgetUsedPercentage',
            'financialStatus',
            'monthlySalary',
            'maintenanceBudgetPercentage',
        ));
    }

    public function updateFinancials(Request $request)
    {
        $validated = $request->validate([
            'monthly_salary' => ['required', 'numeric', 'min:0'],
            'maintenance_budget_percentage' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        auth()->user()->update([
            'monthly_salary' => $validated['monthly_salary'],
            'maintenance_budget_percentage' => $validated['maintenance_budget_percentage'],
        ]);

        return redirect()->route('dashboard')->with('success', 'Pengaturan keuangan berhasil diperbarui.');
    }
}
