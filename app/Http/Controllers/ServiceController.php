<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['user.freelancerProfile', 'category'])
            ->active()
            ->latest();

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Price filter
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'rating':
                    // This would require a more complex query with joins
                    break;
                case 'newest':
                default:
                    $query->latest();
                    break;
            }
        }

        $services = $query->paginate(12);
        $categories = Category::active()->parent()->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function show(Service $service)
    {
        $service->load(['user.freelancerProfile', 'category', 'reviews.reviewer']);
        
        $relatedServices = Service::where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->active()
            ->limit(4)
            ->get();

        return view('services.show', compact('service', 'relatedServices'));
    }

    public function create()
    {
        $this->authorize('create', Service::class);
        
        $categories = Category::active()->parent()->with('children')->get();
        
        return view('services.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Service::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:5',
            'delivery_time' => 'required|integer|min:1|max:365',
            'revisions' => 'required|integer|min:0|max:10',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'images.*' => 'nullable|image|max:5120', // 5MB max per image
        ]);

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services', 'public');
                $images[] = $path;
            }
        }

        $service = Service::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'delivery_time' => $validated['delivery_time'],
            'revisions' => $validated['revisions'],
            'features' => $validated['features'] ?? [],
            'images' => $images,
            'status' => Service::STATUS_ACTIVE,
        ]);

        return redirect()->route('services.show', $service)
            ->with('success', 'Service created successfully!');
    }

    public function edit(Service $service)
    {
        $this->authorize('update', $service);
        
        $categories = Category::active()->parent()->with('children')->get();
        
        return view('services.edit', compact('service', 'categories'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:5',
            'delivery_time' => 'required|integer|min:1|max:365',
            'revisions' => 'required|integer|min:0|max:10',
            'features' => 'nullable|array',
            'features.*' => 'string|max:255',
            'images.*' => 'nullable|image|max:5120',
            'status' => 'required|in:active,paused,draft',
        ]);

        // Handle new image uploads
        $images = $service->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services', 'public');
                $images[] = $path;
            }
        }

        $service->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'delivery_time' => $validated['delivery_time'],
            'revisions' => $validated['revisions'],
            'features' => $validated['features'] ?? [],
            'images' => $images,
            'status' => $validated['status'],
        ]);

        return redirect()->route('services.show', $service)
            ->with('success', 'Service updated successfully!');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        // Delete associated images
        if ($service->images) {
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully!');
    }

    public function myServices()
    {
        $services = Auth::user()->services()
            ->with(['category', 'orders'])
            ->latest()
            ->paginate(10);
        
        return view('services.my-services', compact('services'));
    }
}
