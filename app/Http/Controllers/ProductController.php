<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Lista todos os produtos.
     */
    public function index()
    {
        $products = Product::with('category','specifications')->get();
        return response()->json($products);
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
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        // Upload da imagem, se existir (comentado no código original)
        // if ($request->hasFile('image')) {
        //     $image = $request->file('image');
        //     $path = $image->store('products', 'public');
        // }

        // Criando o produto
        $product = Product::create($data);

        // Salvando especificações se existirem
        if (!empty($data['specifications'])) {
            $this->saveSpecifications($product, $data['specifications']);
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
