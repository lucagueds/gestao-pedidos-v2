<?php

// Em app/Traits/ManagesProductImages.php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

trait ManagesProductImages
{
    /**
     * Lida com o download, otimização e salvamento da imagem de um produto.
     * Retorna o caminho da imagem ou null.
     */
    private function handleImage(array $productData, ImageManager $imageManager): ?string
    {
        if (empty($productData['anexos'])) {
            return null;
        }

        $imageUrl = $productData['anexos'][0]['anexo'];
        $fileName = $productData['id'] . '.jpg';
        $filePath = 'products/' . $fileName;

        // Se o arquivo já existe, não precisa baixar de novo.
        if (Storage::disk('public')->exists($filePath)) {
            return $filePath;
        }

        try {
            $imageContent = Http::get($imageUrl)->body();

            $optimizedImage = $imageManager->read($imageContent)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->toJpeg(75);

            Storage::disk('public')->put($filePath, (string)$optimizedImage);

            return $filePath;
        } catch (\Exception $e) {
            // Se der erro, loga mas não quebra a aplicação
            logger()->error("Falha ao baixar/salvar imagem para o produto {$productData['id']}: " . $e->getMessage());
            return null;
        }
    }
}
