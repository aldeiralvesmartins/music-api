<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Normalizer;

class ProductController extends Controller
{
    /**
     * Lista todos os produtos.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string'],
            'category_id' => ['nullable', 'string'],
            'allowed_categories' => ['nullable', 'array'],
            'allowed_categories.*' => ['string'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'min_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'brand' => ['nullable', 'string'],
            'sort' => [
                'nullable',
                Rule::in(['name_asc','name_desc','price_asc','price_desc','rating_desc','newest']),
            ],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int)($validated['per_page'] ?? 20);

        $query = Product::query()->with(['images', 'category','specifications']);

        // Filtro explícito por companhia para garantir o escopo de tenant
        $companyId = app()->bound('company_id') ? app('company_id') : null;
        if ($companyId) {
            $query->where($query->getModel()->getTable() . '.company_id', $companyId);
        }



        if (!empty($validated['q'])) {

            // 1 — Normaliza e remove acentos
            $q = $validated['q'];
            $q = Normalizer::normalize($q, Normalizer::FORM_D);
            $q = preg_replace('/\pM/u', '', $q); // remove marcas de acento
            $q = strtolower($q); // lowercase

            // 2 — Adiciona curingas do LIKE
            $q = "%{$q}%";

            $query->where(function ($qbuilder) use ($q) {
                $qbuilder
                    ->whereRaw(
                        "immutable_unaccent(lower(name::text)) LIKE ?",
                        [$q]
                    )
                    ->orWhereRaw(
                        "immutable_unaccent(lower(description::text)) LIKE ?",
                        [$q]
                    );
            });
        }





        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }

        if (!empty($validated['allowed_categories'])) {
            $query->whereIn('category_id', $validated['allowed_categories']);
        }

        if (isset($validated['min_price'])) {
            $query->where('price', '>=', $validated['min_price']);
        }
        if (isset($validated['max_price'])) {
            $query->where('price', '<=', $validated['max_price']);
        }

        if (isset($validated['min_rating'])) {
            $query->where('rating', '>=', $validated['min_rating']);
        }

        if (!empty($validated['brand'])) {
            $query->where('brand', $validated['brand']);
        }

        switch ($validated['sort'] ?? 'name_asc') {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating_desc':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
        }

        $paginator = $query->paginate($perPage)->appends($request->query());

        return response()->json([
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * Mostra um produto específico.
     */
    public function show(string $id)
    {
        $product = Product::with('category','specifications')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        return response()->json($product);
    }

    /**
     * Cria um novo produto.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
            'specifications' => 'nullable|array',
            'specifications.*.name' => 'required_with:specifications|string|max:255',
            'specifications.*.value' => 'required_with:specifications',
            'specifications.*.type' => 'required_with:specifications|in:text,number,select,boolean,color',
            'specifications.*.unit' => 'nullable|string|max:20',
            'specifications.*.options' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Dados validados
        $data = $validator->validated();
        DB::beginTransaction();

        // Criando o produto
        $product = Product::create($data);

        // Salvando especificações se existirem
        if (!empty($data['specifications'])) {
            $this->saveSpecifications($product, $data['specifications']);
        }

        foreach ($data['images'] ?? [] as $url) {
            $product->images()->create(['url' => $url]);
        }

        DB::commit();

        return response()->json($product, 201);
    }

    /**
     * Atualiza um produto existente.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'stock' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
            'specifications' => 'nullable|array',
            'specifications.*.name' => 'required_with:specifications|string|max:255',
            'specifications.*.value' => 'required_with:specifications',
            'specifications.*.type' => 'required_with:specifications|in:text,number,select,boolean,color',
            'specifications.*.unit' => 'nullable|string|max:20',
            'specifications.*.options' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        DB::beginTransaction();

        try {
            // Atualiza os campos do produto
            $product->update($data);

            // Atualiza as especificações (se vierem)
            if (!empty($data['specifications'])) {
                $this->saveSpecifications($product, $data['specifications']);
            } else {
                // Se não vieram especificações, remove as existentes
                $product->specifications()->delete();
            }

            DB::commit();

            // Retorna o produto atualizado com as especificações
            return response()->json($product->load('specifications'));

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao atualizar o produto',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Exclui um produto.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Produto excluído com sucesso']);
    }

    protected function saveSpecifications(Product $product, array $specifications): void
    {
        $specsToSave = [];

        foreach ($specifications as $index => $spec) {
            if (empty($spec['name']) || $spec['value'] === null) {
                continue;
            }

            $specsToSave[] = [
                'product_id' => $product->id,
                'name' => $spec['name'],
                'value' => $spec['value'],
                'display_value' => $spec['display_value'] ?? null,
                'type' => $spec['type'] ?? 'text',
                'unit' => $spec['unit'] ?? null,
                'options' => !empty($spec['options']) ? json_encode($spec['options']) : null,
                'sort_order' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($specsToSave)) {
            // Remove especificações antigas
            $product->specifications()->delete();

// Cria as novas via Eloquent para disparar o evento `creating`
            foreach ($specsToSave as $specData) {
                $product->specifications()->create($specData);
            }
        }
    }
}
