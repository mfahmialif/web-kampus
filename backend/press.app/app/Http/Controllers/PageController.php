<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    public function index(Request $request)
    {
        $query = Page::query()->with('creator:id,name,email');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%"));
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->boolean('all')) {
            return $query->orderBy('title')->get(['id', 'title', 'slug', 'status']);
        }

        return $query->latest()->paginate($request->input('per_page', 10));
    }

    public function publicShow(string $slug)
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('status', 'Published')
            ->firstOrFail();

        return response()->json($page);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()?->id;

        if (! empty($data['slug'])) {
            $data['slug'] = Page::uniqueSlug($data['slug']);
        }

        return response()->json(Page::create($data)->load('creator:id,name,email'), 201);
    }

    public function show(Page $page)
    {
        return response()->json($page->load('creator:id,name,email'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $this->validated($request, $page);
        if (! empty($data['slug']) && $data['slug'] !== $page->slug) {
            $data['slug'] = Page::uniqueSlug($data['slug'], $page->id);
        }

        $page->update($data);

        return response()->json($page->fresh()->load('creator:id,name,email'));
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return response()->json(['message' => 'Page berhasil dihapus.']);
    }

    private function validated(Request $request, ?Page $page = null): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($page?->id),
            ],
            'body' => 'nullable|string',
            'status' => 'required|in:Published,Draft',
        ]);
    }
}
