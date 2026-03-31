<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim($request->string('q')->toString());

        $categories = Category::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('notes', 'like', "%{$q}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('categories.index', ['categories' => $categories, 'filters' => ['q' => $q]]);
    }

    public function create(): View
    {
        Gate::authorize('manage-inventory');
        return view('categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'notes' => ['nullable', 'string'],
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        Gate::authorize('manage-inventory');
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        Gate::authorize('manage-inventory');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'notes' => ['nullable', 'string'],
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('admin-only');

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted.');
    }
}
