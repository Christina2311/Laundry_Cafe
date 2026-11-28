<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMeasurementUnitRequest;
use App\Http\Requests\UpdateMeasurementUnitRequest;
use App\Models\MeasurementUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MeasurementUnitController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = MeasurementUnit::query();

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('unit_name', 'like', "%{$search}%")
                    ->orWhere('abbreviation', 'like', "%{$search}%");
            });
        }

        $units = $query
            ->orderBy('unit_name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'inventory.units.index',
            compact('units')
        );
    }

    public function create()
    {
        return view('inventory.units.create');
    }

    public function store(StoreMeasurementUnitRequest $request)
    {
        MeasurementUnit::create($request->validated());

        return redirect()
            ->route('inventory.units.index')
            ->with('success', 'Measurement unit created successfully.');
    }

    public function edit(MeasurementUnit $measurement_unit)
    {
        $unit = $measurement_unit;

        return view(
            'inventory.units.edit',
            compact('unit')
        );
    }

    public function update(
        UpdateMeasurementUnitRequest $request,
        MeasurementUnit $measurement_unit
    ) {
        $validated = $request->validated();

        $measurement_unit->fill($validated);

        if ($measurement_unit->isDirty()) {

            $unitId = $measurement_unit->unit_id;

            $references = [
                ['inventory_items', 'base_unit_id'],
                ['unit_conversions', 'from_unit_id'],
                ['unit_conversions', 'to_unit_id'],
                ['recipe_ingredients', 'unit_id'],
                ['modifier_option_ingredients', 'unit_id'],
                ['service_consumables', 'unit_id'],
                ['purchase_details', 'purchase_unit_id'],
            ];

            foreach ($references as [$table, $column]) {

                if (DB::table($table)
                    ->where($column, $unitId)
                    ->exists()
                ) {

                    throw ValidationException::withMessages([
                        'unit_name' => 'This measurement unit is already in use and cannot be modified.',
                    ]);
                }
            }
        }

        $measurement_unit->save();

        return redirect()
            ->route('inventory.units.index')
            ->with('success', 'Measurement unit updated successfully.');
    }
}
