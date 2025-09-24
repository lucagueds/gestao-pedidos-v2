<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\TinyApiService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class GenerateOrderPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Propriedades para guardar os dados necessários
    protected $order;
    protected $priceListName;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, string $priceListName)
    {
        $this->order = $order;
        $this->priceListName = $priceListName;
    }

    /**
     * Execute the job.
     */
    public function handle(TinyApiService $tinyApiService): void
    {
        // Pega o pedido e o nome da lista de preços
        $order = $this->order;
        $priceListName = $this->priceListName;

        // Lógica de geração de PDF que estava no controller
        $tinyOrderData = $tinyApiService->getSingleOrder($order->tiny_id);
        if (!$tinyOrderData) {
            throw new \Exception('Pedido não encontrado no Tiny ERP para gerar o PDF.');
        }

        $itensParaView = [];
        $somaTotalCaixas = 0;
        $somaTotalPedido = 0;
        $order->load('items.product.prices');

        foreach ($order->items as $localItem) {
            $product = $localItem->product;
            $localPrice = $product->prices->firstWhere('list_name', $this->priceListName);
            $precoUnitarioIndividual = $localPrice ? $localPrice->value : 0;
            $qtdCaixas = $localItem->quantity;
            $unidadesPorCaixa = $product->items_per_box > 0 ? $product->items_per_box : 1;
            $totalUnidadesItem = $qtdCaixas * $unidadesPorCaixa;
            $precoCaixa = $precoUnitarioIndividual * $unidadesPorCaixa;
            $precoTotalItem = $precoUnitarioIndividual * $totalUnidadesItem;
            $somaTotalCaixas += $qtdCaixas;
            $somaTotalPedido += $precoTotalItem;

            $itensParaView[] = [
                'descricao' => $product->name,
                'codigo' => $product->sku,
                'unidade' => $tinyOrderData['itens'][array_search($product->tiny_id, array_column(array_column($tinyOrderData['itens'], 'item'), 'id_produto'), true)]['item']['unidade'] ?? 'CX',
                'qtd_caixas' => $qtdCaixas,
                'total_unidades' => $totalUnidadesItem,
                'unidades_por_caixa' => $unidadesPorCaixa,
                'preco_caixa' => $precoCaixa,
                'preco_unitario' => $precoUnitarioIndividual,
                'preco_total_item' => $precoTotalItem,
                'imagem_url_path' => $product->image_path ? Storage::disk('public')->path($product->image_path) : null
            ];
        }

        $data = [
            'pedido' => $tinyOrderData,
            'cliente' => $tinyOrderData['cliente'],
            'itens' => $itensParaView,
            'soma_total_caixas' => $somaTotalCaixas,
            'soma_total_pedido' => $somaTotalPedido,
            'priceListName' => $this->priceListName,
        ];

        // Renderiza o PDF
        $pdf = Pdf::loadView('pdfs.order', $data);

        // SALVA o PDF em vez de fazer stream
        $filePath = 'pdfs/pedido-' . $order->tiny_id . '.pdf';
        Storage::disk('public')->put($filePath, $pdf->output());

        // Atualiza o pedido com o status de concluído e o caminho do arquivo
        $order->update([
            'pdf_status' => 'completed',
            'pdf_path' => $filePath,
        ]);
    }

    /**
     * Lida com uma falha no job.
     */
    public function failed(Throwable $exception): void
    {
        // Se algo der errado, podemos registrar a falha
        $this->order->update(['pdf_status' => 'failed']);
        logger()->error("Falha ao gerar PDF para o pedido {$this->order->tiny_id}: {$exception->getMessage()}");
    }
}
