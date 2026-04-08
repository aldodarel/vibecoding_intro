<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use App\Models\Unit;

class PropertyService 
{
    public function __construct(
        private FilesystemFactory $storage,
    ) {
    }

    public function getAllByOwner(int $ownerId, ?string $search = null): LengthAwarePaginator
    {
        $query = Property::query()
            ->where('owner_id', $ownerId)
            ->with('propertyType');
            // ->withCount('units');

        $keyword = trim((string) $search);

        if (mb_strlen($keyword) >= 2) {
            $normalized = mb_strtolower($keyword);

            $query->where(function ($builder) use ($normalized): void {
                $builder
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$normalized}%"])
                    ->orWhereRaw('LOWER(city) LIKE ?', ["%{$normalized}%"]);
            });
        }

        return $query
            ->latest()
            ->paginate(10);
    }

    public function findByOwner(int $propertyId, int $ownerId): Property
    {
        return Property::query()
            ->where('id', $propertyId)
            ->where('owner_id', $ownerId)
            ->with('propertyType')
            ->firstOrFail();
    }

    public function create(array $data, int $ownerId): Property
    {
        $payload = Arr::except($data, ['owner_id', 'cover_image']);
        $payload['owner_id'] = $ownerId;

        if (($data['cover_image'] ?? null) instanceof UploadedFile) {
            $payload['cover_image'] = $this->storeCoverImage($data['cover_image']);
        }

        return Property::create($payload);
    }

    public function update(Property $property, array $data): Property
    {
        $payload = Arr::except($data, ['owner_id', 'cover_image']);

        if (($data['cover_image'] ?? null) instanceof UploadedFile) {
            $this->deleteCoverImage($property->cover_image);
            $payload['cover_image'] = $this->storeCoverImage($data['cover_image']);
        }

        $property->update($payload);

        return $property->refresh();
    }

    public function delete(Property $property): void
    {
        $this->deleteCoverImage($property->cover_image);
        $property->delete();
    }

    private function storeCoverImage(UploadedFile $file): string
    {
        return $file->store('properties', 'public');
    }

    private function deleteCoverImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        $this->storage->disk('public')->delete($path);
    }
}
