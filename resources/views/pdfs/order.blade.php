<!DOCTYPE html>
<html lang="pt-br">
    <head>
        {{-- Seu <head> e <style> continuam os mesmos --}}
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Pedido de Venda Nº {{ $pedido['numero'] }}</title>
        <style>
            body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
            .header-table, .info-table, .items-table { width: 100%; border-collapse: collapse; }
            .header-table td { vertical-align: top; }
            .header-logo { width: 150px; }
            .header-logo img { max-width: 120px; }
            .header-company-info { text-align: right; }
            .header-company-info h3, .header-company-info p { margin: 0; }
            .section-title { background-color: #f2f2f2; padding: 4px; font-weight: bold; text-align: center; margin-top: 10px; margin-bottom: 4px; border: 1px solid #ddd; }
            .info-table td { padding: 3px 5px; }
            .info-label { font-weight: bold; width: 120px; }
            .items-table { margin-top: 10px; border: 1px solid #ddd; }
            .items-table th, .items-table td { border: 1px solid #ddd; padding: 4px 5px; text-align: left; vertical-align: middle; }
            .items-table th { background-color: #f2f2f2; font-weight: bold; }
            .items-table .text-center { text-align: center; }
            .items-table .text-right { text-align: right; white-space: nowrap }
            .item-description { font-size: 9px; color: #555; }
            .totals-table { width: 100%; }
            .totals-table td { padding: 5px; font-weight: bold; }
            .totals-label { text-align: left; }
            hr { border: 0; border-top: 1px solid #ddd; margin: 10px 0; }
        </style>
    </head>
    <body>

        {{-- A parte do Header e Informações do Cliente continua a mesma --}}
        <table class="header-table">
            <tr>
                <td class="header-logo"><img src="{{ public_path('images/ducatoys.png') }}" alt="Duca Toys Logo"></td>
                <td class="header-company-info">
                    <h3>PRIME IMPORT COMERCIO DE BRINQUEDOS E UTILIDADES DOMESTICAS LTDA</h3>
                    <p>CNPJ: 57.887.584/0001-86</p>
                    <p>Telefone: (47) 3241-1400</p>
                </td>
            </tr>
        </table>
        <div class="section-title" style="font-size: 14px;">Pedido de Venda N° {{ $pedido['numero'] }}</div>
        <hr>
        <table class="items-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr><td class="info-label">Cliente:</td><td><strong>{{ $cliente['nome'] }}</strong><br>CNPJ: {{ $cliente['cpf_cnpj'] ?? 'N/A' }}, IE: {{ $cliente['ie'] ?? 'N/A' }}</td></tr>
                        <tr><td class="info-label">Endereço:</td><td>{{ $cliente['endereco'] ?? '' }}<br>{{ $cliente['cep'] ?? '' }} - {{ $cliente['cidade'] ?? '' }}, {{ $cliente['uf'] ?? '' }}</td></tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr><td class="info-label">Data:</td><td>{{ \Carbon\Carbon::createFromFormat('d/m/Y', $pedido['data_pedido'])->format('d/m/Y') }}</td></tr>
                        <tr><td class="info-label">Contato:</td><td>{{ $cliente['fone'] ?? 'N/A' }}</td></tr>
                        <tr><td class="info-label">Lista de Preço:</td><td>{{ $priceListName }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="section-title">ITENS DO PEDIDO</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Imagem</th>
                    <th>Descrição</th>
                    <th class="text-center">SKU</th>
                    <th class="text-center">Qtd. Cx</th>
                    <th class="text-center">Qtd. Un</th>
                    <th class="text-center">UN</th>
                    <th class="text-center">Preço Un.</th>
                    <th class="text-center">Preço Cx</th>
                    <th class="text-center">Preço Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($itens as $item)
                    <tr>
                        <td class="text-center">
                            @if(!empty($item['imagem_url_path']) && file_exists($item['imagem_url_path']))
                                <img src="{{ $item['imagem_url_path'] }}" style="width:40px; height:auto;">
                            @else
                                ---
                            @endif
                        </td>
                        <td>
                            {{ $item['descricao'] }}
                            <div class="item-description">Unidades por Caixa: {{ $item['unidades_por_caixa'] }}</div>
                        </td>
                        <td class="text-center">{{ $item['codigo'] }}</td>
                        <td class="text-center">{{ number_format($item['qtd_caixas'], 2, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($item['total_unidades'], 0, ',', '.') }}</td>
                        <td class="text-center">{{ $item['unidade'] }}</td>
                        <td class="text-right">R$ {{ number_format($item['preco_unitario'], 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($item['preco_caixa'], 2, ',', '.') }}</td>
                        <td class="text-right">R$ {{ number_format($item['preco_total_item'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width: 100%; margin-top: 15px;" class="items-table">
            <tr>
                <td style="vertical-align: top; width: 50%;">
                    <table class="totals-table">
                        <tr>
                            <td class="totals-label">Total de Caixas:</td>
                            <td style="text-align: right" class="totals-value">{{ number_format($soma_total_caixas, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                <td style="vertical-align: top; width: 50%;">
                    <table class="totals-table">
                        <tr>
                            <td class="totals-label">Total do pedido:</td>
                            <td style="text-align: right" class="totals-value">R$ {{ number_format($soma_total_pedido, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </body>
</html>
