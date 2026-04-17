<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreUnitRequest;
use App\Http\Requests\Owner\UpdateUnitRequest;
use App\Models\Property;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UnitController extends Controller
{
    public function __construct(
        private readonly UnitService $unitService,
    ) {}

    public function index(Property $property): Response
    {
        $this->authorizeOwner($property);

        $units = $this->unitService->getAllByProperty($property);

        return Inertia::render('owner/units/index', [
            'property' => $property,
            'units' => $units,
        ]);
    }

    public function create(Property $property): Response
    {
        $this->authorizeOwner($property);

        return Inertia::render('owner/units/create', [
            'property' => $property,
        ]);
    }

    public function store(Property $property, StoreUnitRequest $request): RedirectResponse
    {
        $this->authorizeOwner($property);

        $this->unitService->create($property, $request->validated());

        return redirect()->route('owner.properties.units.index', $property)
            ->with('success', 'Unit berhasil ditambahkan.');
    }

    public function edit(Property $property, Unit $unit): Response
    {
        $this->authorizeOwner($property);
        $this->ensureNestedUnit($property, $unit);

        return Inertia::render('owner/units/edit', [
            'property' => $property,
            'unit' => $unit,
        ]);
    }

    public function update(Property $property, Unit $unit, UpdateUnitRequest $request): RedirectResponse
    {
        $this->authorizeOwner($property);
        $this->ensureNestedUnit($property, $unit);

        $this->unitService->update($unit, $request->validated());

        return redirect()->route('owner.properties.units.index', $property)
            ->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Property $property, Unit $unit): RedirectResponse
    {
        $this->authorizeOwner($property);
        $this->ensureNestedUnit($property, $unit);

        try {
            $this->unitService->delete($unit);

            return redirect()->route('owner.properties.units.index', $property)
                ->with('success', 'Unit berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal Menghapus Unit');
        }
    }

    private function authorizeOwner(Property $property): void
    {
        if ($property->owner_id !== Auth::id()) {
            abort(403);
        }
    }

    private function ensureNestedUnit(Property $property, Unit $unit): void
    {
        if ($unit->property_id !== $property->id) {
            abort(404);
        }
    }
}
