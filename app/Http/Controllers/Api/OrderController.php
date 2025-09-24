<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\TinyApiService;
use Illuminate\Http\Request;
use App\Traits\ManagesOrderItems;
use App\Jobs\GenerateOrderPdfJob;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    use ManagesOrderItems;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Order::query();
        $totalValue = $query->sum('total_amount_cache');
        $orders = $query->with('items')->latest('order_date')->paginate(15);

        // Em vez de retornar JSON, agora retorna a view Blade
        return view('orders.index', [
            'orders' => $orders,
            'totalValue' => $totalValue,
        ]);
    }

    public function resync(Request $request, Order $order, TinyApiService $tinyApiService)
    {
        $orderData = $tinyApiService->getSingleOrder($order->tiny_id);
        if (!$orderData) {
            return response()->json(['message' => 'Pedido não encontrado no Tiny ERP.'], 404);
        }

        // Atualiza os campos de texto do pedido
        $order->customer_name = $orderData['cliente']['nome'];
        $order->total_amount_cache = $orderData['total_pedido'];
        // Adicione outros campos de texto que desejar aqui...

        // Se o request pedir para resincronizar os itens...
        if ($request->input('with_items', false)) {
            $this->syncOrderItems($order, $orderData);
        }

        $order->save();

        return new OrderResource($order->load('items'));
    }

    public function generatePdf(Request $request, Order $order)
    {
        // Define o status como 'em processamento'
        $order->update([
            'pdf_status' => 'processing',
            'pdf_path' => null, // Limpa o caminho antigo, se houver
        ]);

        $priceListName = $request->query('price_list', 'Preço a Vista');

        // Envia o Job para a fila
        GenerateOrderPdfJob::dispatch($order, $priceListName);

        // Retorna uma resposta imediata
        return response()->json([
            'message' => 'Seu pedido para gerar o PDF foi recebido e está sendo processado.'
        ]);
    }

    public function downloadPdf(Order $order)
    {
        // Verifica se o PDF está pronto
        if ($order->pdf_status !== 'completed' || !$order->pdf_path || !Storage::disk('public')->exists($order->pdf_path)) {
            return response()->json([
                'message' => 'O PDF ainda não está pronto ou ocorreu um erro.',
                'status' => $order->pdf_status,
            ], 404);
        }

        // Força o download do arquivo
        return Storage::disk('public')->download($order->pdf_path);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        // 204 No Content é a resposta padrão para um delete bem-sucedido
        return response()->noContent();
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
}
