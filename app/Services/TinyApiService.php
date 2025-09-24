<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TinyApiService
{
    protected string $token;
    protected string $baseUrl;

    /**
     * Pega as credenciais da API do arquivo de configuração ao iniciar o serviço.
     */
    public function __construct()
    {
        $this->token = config('services.tiny.api_token');
        $this->baseUrl = 'https://api.tiny.com.br/api2';
    }

    /**
     * Busca uma página de produtos na API do Tiny.
     *
     * @param int $page
     * @return array
     */
    public function getProducts(int $page = 1): array
    {
        if (empty($this->token)) {
            Log::error('ERRO FATAL: Token da API do Tiny não está configurado no arquivo .env ou no cache.');
            return [];
        }

        try {
            // VERSÃO FINAL E CORRETA DA URL:
            $response = Http::asForm()->post($this->baseUrl . '/produtos.pesquisa.php', [
                'token' => $this->token,
                'formato' => 'JSON',
                'pagina' => $page
            ]);

            $data = $response->json();

            if (!is_array($data) || !isset($data['retorno'])) {
                Log::warning('Resposta da API do Tiny não é um JSON válido ou tem estrutura inesperada.', [
                    'raw_response_body' => $response->body()
                ]);
                return [];
            }

            if ($data['retorno']['status'] === 'ERRO') {
                Log::error('A API do Tiny retornou um erro.', [
                    'erros' => $data['retorno']['erros'] ?? 'Nenhum detalhe de erro fornecido.'
                ]);
                return [];
            }

            if ($data['retorno']['status'] === 'OK') {
                if (empty($data['retorno']['produtos'])) {
                    return [];
                }
                return array_map(fn($item) => $item['produto'], $data['retorno']['produtos']);
            }

        } catch (\Exception $e) {
            Log::critical('Exceção ao chamar a API do Tiny.', [
                'message' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * Busca uma página de pedidos na API do Tiny.
     *
     * @param int $page
     * @return array
     */
    public function getOrders(int $page = 1): array
    {
        if (empty($this->token)) {
            Log::error('ERRO FATAL: Token da API do Tiny não está configurado.');
            return [];
        }

        try {
            $response = Http::asForm()->post($this->baseUrl . '/pedidos.pesquisa.php', [
                'token'   => $this->token,
                'formato' => 'JSON',
                'pagina'  => $page,
            ]);

            $data = $response->json();

            if (!is_array($data) || !isset($data['retorno'])) {
                Log::warning('Resposta da API do Tiny (Pedidos) não é um JSON válido ou tem estrutura inesperada.', [
                    'raw_response_body' => $response->body()
                ]);
                return [];
            }

            if ($data['retorno']['status'] === 'ERRO') {
                Log::error('A API do Tiny (Pedidos) retornou um erro.', [
                    'erros' => $data['retorno']['erros'] ?? 'Nenhum detalhe de erro fornecido.'
                ]);
                return [];
            }

            if ($data['retorno']['status'] === 'OK') {
                if (empty($data['retorno']['pedidos'])) {
                    return [];
                }
                // A estrutura da API para pedidos é a mesma dos produtos: um array de 'pedido'
                return array_map(fn($item) => $item['pedido'], $data['retorno']['pedidos']);
            }

        } catch (\Exception $e) {
            Log::critical('Exceção ao chamar a API de Pedidos do Tiny.', [
                'message' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * Busca os detalhes completos de um único pedido.
     *
     * @param string $orderId
     * @return array|null
     */
    public function getSingleOrder(string $orderId): ?array
    {
        if (empty($this->token)) {
            Log::error('ERRO FATAL: Token da API do Tiny não está configurado.');
            return null;
        }

        try {
            $response = Http::asForm()->post($this->baseUrl . '/pedido.obter.php', [
                'token'   => $this->token,
                'formato' => 'JSON',
                'id'      => $orderId,
            ]);

            $data = $response->json();

            if (!is_array($data) || !isset($data['retorno'])) {
                Log::warning('Resposta da API do Tiny (Obter Pedido) não é um JSON válido.', ['raw_response_body' => $response->body()]);
                return null;
            }

            if ($data['retorno']['status'] === 'ERRO') {
                Log::error('A API do Tiny (Obter Pedido) retornou um erro.', ['erros' => $data['retorno']['erros'] ?? 'N/A']);
                return null;
            }

            if ($data['retorno']['status'] === 'OK') {
                return $data['retorno']['pedido'];
            }
        } catch (\Exception $e) {
            Log::critical('Exceção ao chamar a API de Obter Pedido do Tiny.', ['message' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Busca os detalhes completos de um único produto.
     *
     * @param string $productId
     * @return array|null
     */
    public function getSingleProduct(string $productId): ?array
    {
        // ... (toda a lógica de verificação de token e try-catch,
        // igual ao método getSingleOrder, mas para o endpoint de produto)

        try {
            $response = Http::asForm()->post($this->baseUrl . '/produto.obter.php', [
                'token'   => $this->token,
                'formato' => 'JSON',
                'id'      => $productId,
            ]);

            $data = $response->json();

            if (!is_array($data) || !isset($data['retorno'])) {
                Log::warning('Resposta da API do Tiny (Obter Produto) não é um JSON válido.', ['raw_response_body' => $response->body()]);
                return null;
            }

            if ($data['retorno']['status'] === 'ERRO') {
                Log::error('A API do Tiny (Obter Produto) retornou um erro.', ['erros' => $data['retorno']['erros'] ?? 'N/A']);
                return null;
            }

            if ($data['retorno']['status'] === 'OK') {
                return $data['retorno']['produto'];
            }
        } catch (\Exception $e) {
            Log::critical('Exceção ao chamar a API de Obter Produto do Tiny.', ['message' => $e->getMessage()]);
        }

        return null;
    }
}
