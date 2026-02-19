<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CenterController extends Controller
{
    /**
     * Display a listing of mental clinics and health centers.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:255',
            'type' => 'nullable|in:mental_clinic,health_center',
            'status' => 'nullable|in:active,inactive',
            'sort' => 'nullable|in:latest,oldest,name_asc,name_desc',
        ]);

        $query = Center::query();

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (!empty($validated['status'])) {
            $query->where('is_active', $validated['status'] === 'active');
        }

        $sort = $validated['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'name_desc':
                $query->orderByDesc('name');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $centers = $query->paginate(12)->withQueryString()->through(function (Center $center) {
            return [
                'id' => $center->id,
                'name' => $center->name,
                'type' => $center->type,
                'city' => $center->city,
                'phone' => $center->phone,
                'email' => $center->email,
                'address' => $center->address,
                'latitude' => $center->latitude !== null ? (float) $center->latitude : null,
                'longitude' => $center->longitude !== null ? (float) $center->longitude : null,
                'is_active' => (bool) $center->is_active,
                'created_at' => $center->created_at?->toDateTimeString(),
            ];
        });

        return Inertia::render('Admin/Centers/Index', [
            'centers' => $centers,
            'filters' => [
                'search' => $validated['search'] ?? '',
                'type' => $validated['type'] ?? '',
                'status' => $validated['status'] ?? '',
                'sort' => $sort,
            ],
            'mapCenters' => Center::query()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'type',
                    'city',
                    'address',
                    'latitude',
                    'longitude',
                ])
                ->map(function (Center $center) {
                    return [
                        'id' => $center->id,
                        'name' => $center->name,
                        'type' => $center->type,
                        'city' => $center->city,
                        'address' => $center->address,
                        'latitude' => (float) $center->latitude,
                        'longitude' => (float) $center->longitude,
                    ];
                }),
            'stats' => [
                'total' => Center::count(),
                'mental_clinics' => Center::where('type', 'mental_clinic')->count(),
                'health_centers' => Center::where('type', 'health_center')->count(),
                'active' => Center::where('is_active', true)->count(),
            ],
        ]);
    }

    /**
     * Store a newly created center.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:mental_clinic,health_center',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'is_active' => 'nullable|boolean',
        ]);

        Center::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'city' => $validated['city'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'created_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Center created successfully.');
    }

    /**
     * Update the specified center.
     */
    public function update(Request $request, Center $center)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:mental_clinic,health_center',
            'city' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90|required_with:longitude',
            'longitude' => 'nullable|numeric|between:-180,180|required_with:latitude',
            'is_active' => 'required|boolean',
        ]);

        $center->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'city' => $validated['city'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return back()->with('success', 'Center updated successfully.');
    }

    /**
     * Toggle center active/inactive status.
     */
    public function updateStatus(Request $request, Center $center)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $center->update([
            'is_active' => $validated['is_active'],
        ]);

        return back()->with('success', 'Center status updated successfully.');
    }

    /**
     * Remove the specified center.
     */
    public function destroy(Center $center)
    {
        $center->delete();

        return back()->with('success', 'Center deleted successfully.');
    }
}



