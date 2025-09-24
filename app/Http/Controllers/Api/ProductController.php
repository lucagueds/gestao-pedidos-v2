<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Traits\ManagesProductImages; // 1. Importe o Trait

class ProductController extends Controller
{
    use ManagesProductImages;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::query()
            // Eager Loading: Essencial por causa do whenLoaded('prices') no Resource
            ->with('prices')
            ->orderBy('name') // Ordena por nome
            ->paginate(20); // Um pouco mais de itens por página

        return ProductResource::collection($products);
    }

    /**
     * Update the specified product's prices in storage.
     */
    public function updatePrices(Request $request, Product $product)
    {
        // 1. Validação dos Dados Recebidos
        $validatedData = $request->validate([
            // Garante que 'prices' seja um array e que seja obrigatório
            'prices' => 'required|array',
            // Garante que cada item dentro do array 'prices' seja um objeto
            'prices.*' => 'required|array',
            // Valida os campos de cada objeto dentro do array
            'prices.*.list_name' => 'required|string',
            'prices.*.value' => 'required|numeric|min:0',
        ]);

        // 2. Lógica para Salvar ou Atualizar
        foreach ($validatedData['prices'] as $priceData) {
            // updateOrCreate é perfeito aqui:
            // Ele procura um preço com este 'product_id' e 'list_name'.
            // Se encontrar, atualiza o 'value'.
            // Se não encontrar, cria um novo registro.
            $product->prices()->updateOrCreate(
                ['list_name' => $priceData['list_name']],
                ['value' => $priceData['value']]
            );
        }

        // 3. Retorna o Produto Atualizado
        // O load('prices') recarrega a relação de preços com os novos dados.
        return new ProductResource($product->load('prices'));
    }

    /**
     * Resincroniza um produto específico com a API do Tiny.
     */
    public function resync(Request $request, Product $product, TinyApiService $tinyApiService, ImageManager $imageManager)
    {
        // Busca os dados mais recentes do Tiny
        $productData = $tinyApiService->getSingleProduct($product->tiny_id);
        if (!$productData) {
            return response()->json(['message' => 'Produto não encontrado no Tiny ERP.'], 404);
        }

        // Atualiza os campos de texto
        $product->name = $productData['nome'];
        $product->sku = $productData['codigo'] ?? null;
        $product->items_per_box = !empty($productData['unidade_por_caixa']) ? $productData['unidade_por_caixa'] : null;

        // Se o request pedir para verificar a imagem, executa a lógica
        if ($request->input('with_images', false)) {
            $imagePath = $this->handleImage($productData, $imageManager);
            $product->image_path = $imagePath;
        }

        $product->save();

        return new ProductResource($product->load('prices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
