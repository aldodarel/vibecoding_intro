<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use App\Services\PropertyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function __construct(
        private readonly PropertyService $propertyService,
    ) {
    }

    public function index(Request $request): Response
{
    $properties = $this->propertyService->getAllByOwner(
        ownerId: auth()->id(),
        search: $request->get('search'),
    );

    return Inertia::render('owner/properties/index', [
        'properties' => $properties,
        'filters' => $request->only(['search']),
    ]);
}


    public function create(): Response
    {
        return Inertia::render('owner/properties/create', [
            'propertyTypes' => PropertyType::all(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $ownerId = (int) auth()->id();

        $this->propertyService->create($request->all(), $ownerId);

        return redirect()->route('owner.properties.index')
            ->with('success', 'Properti berhasil ditambahkan.');
    }

    public function show(int $id): Response
    {
        $ownerId = (int) auth()->id();
        $property = $this->propertyService->findByOwner($id, $ownerId);

        return Inertia::render('owner/properties/show', [
            'property' => $property,
        ]);
    }

    public function edit(int $id): Response
    {
        $ownerId = (int) auth()->id();
        $property = $this->propertyService->findByOwner($id, $ownerId);

        return Inertia::render('owner/properties/edit', [
            'property' => $property,
            'propertyTypes' => PropertyType::query()->latest()->get(),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $ownerId = (int) auth()->id();
        $property = $this->propertyService->findByOwner($id, $ownerId);

        $this->propertyService->update($property, $request->all());

        return redirect()->route('owner.properties.index')
            ->with('success', 'Properti berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $ownerId = (int) auth()->id();
        $property = $this->propertyService->findByOwner($id, $ownerId);

        $this->propertyService->delete($property);

        return redirect()->route('owner.properties.index')
            ->with('success', 'Properti berhasil dihapus.');
    }
}
