<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use function Psy\debug;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories for public API.
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Display a listing of all categories for admin.
     */
    public function adminIndex()
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'is_active' => 'boolean',
        ]);

        $slug = Str::slug($validated['name']);
        $validated['slug'] = Category::where('slug', $slug)->exists()
            ? $slug . '-' . time()
            : $slug;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    /**
     * Display the specified category.
     */
    public function show($id)
    {
        $category = Category::with(['products' => function($query) {
            $query->where('is_active', true);
        }])->findOrFail($id);

        return response()->json($category);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Update slug only if name changed
        if ($category->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $validated['slug'] = Category::where('slug', $slug)
                ->where('id', '!=', $category->id)
                ->exists() ? $slug . '-' . time() : $slug;
        }


        $category->update($validated);

        return response()->json($category);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        // Don't delete if category has products
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with associated products. Please reassign or delete the products first.'
            ], 422);
        }

        // Delete associated image
        if ($category->image_url) {
            $imagePath = str_replace('/storage/', '', $category->image_url);
            Storage::disk('public')->delete($imagePath);
        }

        $category->delete();

        return response()->json(null, 204);
    }
}
