<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\TinyApiService;
use App\Traits\ManagesProductImages; // 1. Importe o Trait
use Illuminate\Console\Command;
use Intervention\Image\ImageManager;

class SyncProductsCommand extends Command
{
    use ManagesProductImages; // 2. Use o Trait

    protected $signature = 'sync:products {--force-images : Força a verificação e o download de imagens para todos os produtos}';
    protected $description = 'Sincroniza os produtos da API do Tiny para o banco de dados local.';

    public function handle(TinyApiService $tinyApiService, ImageManager $imageManager)
    {
        $this->info('Iniciando a sincronização de produtos...');
        $forceImages = $this->option('force-images');
        if ($forceImages) {
            $this->warn('Modo de sincronização COMPLETA (com imagens) ativado.');
        } else {
            $this->info('Modo de sincronização RÁPIDA (apenas texto) ativado.');
        }

        $page = 1;
        do {
            $this->line("Buscando página {$page} de produtos...");

            $productsFromApi = $tinyApiService->getProducts($page);

            if (empty($productsFromApi)) {
                $this->info('Nenhum produto encontrado na página, finalizando.');
                break;
            }

            foreach ($productsFromApi as $productSummary) {
                $productData = $tinyApiService->getSingleProduct($productSummary['id']);
                if (!$productData) continue;

                $updateData = [
                    'name' => $productData['nome'],
                    'sku' => $productData['codigo'] ?? null,
                    'items_per_box' => !empty($productData['unidade_por_caixa']) ? $productData['unidade_por_caixa'] : null,
                ];

                if ($forceImages) {
                    $updateData['image_path'] = $this->handleImage($productData, $imageManager);
                }

                Product::updateOrCreate(['tiny_id' => $productData['id']], $updateData);
                sleep(1);
            }
            $page++;
        } while (true);

        $this->info("Sincronização de produtos concluída!");
        return 0;
    }
}
