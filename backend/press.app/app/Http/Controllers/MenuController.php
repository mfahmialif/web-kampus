<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function primary()
    {
        $menu = $this->primaryMenu();
        $items = $menu->items()->where('is_active', true)->get();

        return response()->json($this->tree($items, publicOnly: true));
    }

    public function items()
    {
        $menu = $this->primaryMenu();

        return response()->json($this->tree($menu->items()->get(), publicOnly: false));
    }

    public function options()
    {
        return response()->json([
            'pages' => Page::query()->orderBy('title')->get(['id', 'title', 'slug', 'status']),
            'posts' => News::query()->orderByDesc('created_at')->get(['id', 'title', 'slug', 'status']),
        ]);
    }

    public function storeItem(Request $request)
    {
        $menu = $this->primaryMenu();
        $data = $this->validated($request, $menu);
        $data['menu_id'] = $menu->id;
        $data['sort_order'] = $data['sort_order'] ?? ((int) $menu->items()->where('parent_id', $data['parent_id'] ?? null)->max('sort_order')) + 1;
        $data = $this->normalizeReference($data);

        return response()->json(MenuItem::create($data), 201);
    }

    public function updateItem(Request $request, MenuItem $menuItem)
    {
        abort_unless((int) $menuItem->menu_id === (int) $this->primaryMenu()->id, 404);

        $data = $this->validated($request, $menuItem->menu, $menuItem);
        $data = $this->normalizeReference($data);
        $menuItem->update($data);

        return response()->json($menuItem->fresh());
    }

    public function destroyItem(MenuItem $menuItem)
    {
        abort_unless((int) $menuItem->menu_id === (int) $this->primaryMenu()->id, 404);
        $menuItem->delete();

        return response()->json(['message' => 'Menu item berhasil dihapus.']);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.parent_id' => 'nullable|exists:menu_items,id',
            'items.*.sort_order' => 'required|integer|min:0',
        ]);

        $menuId = $this->primaryMenu()->id;
        foreach ($data['items'] as $item) {
            MenuItem::query()
                ->where('menu_id', $menuId)
                ->whereKey($item['id'])
                ->update([
                    'parent_id' => $item['parent_id'] ?? null,
                    'sort_order' => $item['sort_order'],
                ]);
        }

        return $this->items();
    }

    private function primaryMenu(): Menu
    {
        return Menu::query()->firstOrCreate(
            ['slug' => 'primary'],
            ['name' => 'Primary Navigation']
        );
    }

    private function validated(Request $request, Menu $menu, ?MenuItem $menuItem = null): array
    {
        return $request->validate([
            'label' => 'required|string|max:120',
            'type' => ['required', Rule::in(['page', 'post', 'custom'])],
            'reference_id' => [
                Rule::requiredIf(fn () => in_array($request->input('type'), ['page', 'post'], true)),
                'nullable',
                'integer',
            ],
            'url' => [
                Rule::requiredIf(fn () => $request->input('type') === 'custom'),
                'nullable',
                'string',
                'max:500',
            ],
            'target' => ['required', Rule::in(['_self', '_blank'])],
            'parent_id' => [
                'nullable',
                Rule::exists('menu_items', 'id')->where(fn ($query) => $query->where('menu_id', $menu->id)),
                function ($attribute, $value, $fail) use ($menuItem) {
                    if ($menuItem && (int) $value === (int) $menuItem->id) {
                        $fail('Parent menu tidak boleh item yang sama.');
                    }
                },
            ],
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);
    }

    private function normalizeReference(array $data): array
    {
        if ($data['type'] === 'page') {
            abort_unless(Page::whereKey($data['reference_id'])->exists(), 422, 'Page tidak valid.');
            $data['url'] = null;
            return $data;
        }

        if ($data['type'] === 'post') {
            abort_unless(News::whereKey($data['reference_id'])->exists(), 422, 'Post tidak valid.');
            $data['url'] = null;
            return $data;
        }

        $data['reference_id'] = null;
        return $data;
    }

    private function tree($items, bool $publicOnly): array
    {
        $resolved = $items
            ->map(fn (MenuItem $item) => $this->payload($item, $publicOnly))
            ->filter()
            ->values();
        $grouped = $resolved->groupBy('parent_id');

        $build = function ($parentId) use (&$build, $grouped) {
            return ($grouped[$parentId] ?? collect())
                ->sortBy([['sort_order', 'asc'], ['id', 'asc']])
                ->map(function ($item) use (&$build) {
                    $item['children'] = $build($item['id'])->values()->all();
                    return $item;
                })
                ->values();
        };

        return $build('')->all();
    }

    private function payload(MenuItem $item, bool $publicOnly): ?array
    {
        $url = $item->url;

        if ($item->type === 'page') {
            $page = Page::find($item->reference_id);
            if (! $page || ($publicOnly && $page->status !== 'Published')) {
                return null;
            }
            $url = "/pages/{$page->slug}";
        }

        if ($item->type === 'post') {
            $post = News::find($item->reference_id);
            if (! $post || ($publicOnly && $post->status !== 'Published')) {
                return null;
            }
            $url = '/news/' . ($post->slug ?: $post->id);
        }

        return [
            'id' => $item->id,
            'parent_id' => $item->parent_id,
            'label' => $item->label,
            'type' => $item->type,
            'reference_id' => $item->reference_id,
            'url' => $url ?: '/',
            'target' => $item->target,
            'sort_order' => $item->sort_order,
            'is_active' => $item->is_active,
            'children' => [],
        ];
    }
}
