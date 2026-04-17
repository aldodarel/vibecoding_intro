<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Unit;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class UnitService
{
    public function getAllByProperty(Property $property): Collection
    {
        $this->ensurePropertyOwner($property);

        return $property->units()
            ->with('renter')
            ->orderBy('name')
            ->get();
    }

    public function create(Property $property, array $data): Unit
    {
        $this->ensurePropertyOwner($property);

        $payload = Arr::except($data, ['property_id']);

        return $property->units()->create($payload);
    }

    public function update(Unit $unit, array $data): Unit
    {
        $unit->loadMissing('property');
        $this->ensurePropertyOwner($unit->property);

        $payload = Arr::except($data, ['property_id']);

        $unit->update($payload);

        return $unit->refresh();
    }

    public function delete(Unit $unit): void
    {
        $unit->loadMissing('property');
        $this->ensurePropertyOwner($unit->property);

        if ($unit->isOccupied()) {
            throw new \Exception('Unit tidak bisa dihapus karena sedang ditempati');
        }

        $unit->delete();
    }

    private function ensurePropertyOwner(Property $property): void
    {
        if ($property->owner_id !== Auth::id()) {
            abort(403);
        }
    }
}
