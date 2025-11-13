<?php

require_once '../config/conexao.php'; 

set_time_limit(0);
ini_set('memory_limit','512M');

function fetch_json($url, $timeout = 30) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_FAILONERROR => false,
    ]);
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false || $code >= 400) {
        return null;
    }
    $data = json_decode($body, true);
    return $data;
}


$base = 'https://fortnite-api.com/v2';
$url_all = $base . '/cosmetics/br';
$url_new = $base . '/cosmetics/new'; 
$url_shop = $base . '/shop';        


$data_all = fetch_json($url_all);
if (!$data_all || !isset($data_all['data'])) {
    die("Erro ao buscar /cosmetics. Tente novamente.");
}


$data_new = fetch_json($url_new);
$ids_new = [];
if ($data_new && isset($data_new['data'])) {
   
    foreach ($data_new['data'] as $it) {
        if (isset($it['id'])) $ids_new[] = $it['id'];
        elseif (isset($it['cosmetic']['id'])) $ids_new[] = $it['cosmetic']['id'];
    }
}

$data_shop = fetch_json($url_shop);
$ids_shop = [];
if ($data_shop && isset($data_shop['data'])) {
    
    foreach ($data_shop['data'] as $section) {
        if (isset($section['entries']) && is_array($section['entries'])) {
            foreach ($section['entries'] as $entry) {
                
                if (isset($entry['items']) && is_array($entry['items'])) {
                    foreach ($entry['items'] as $it) {
                        if (isset($it['id'])) $ids_shop[] = $it['id'];
                        elseif (isset($it['offer']['id'])) $ids_shop[] = $it['offer']['id'] ?? null;
                    }
                }
                
                if (isset($entry['id'])) $ids_shop[] = $entry['id'];
            }
        }
       
        if (isset($section['id'])) $ids_shop[] = $section['id'];
    }
}


$ids_new = array_values(array_filter(array_unique($ids_new)));
$ids_shop = array_values(array_filter(array_unique($ids_shop)));

$totalInseridos = 0;
$totalAtualizados = 0;


$pdo->exec("UPDATE cosmeticos SET is_novo = 0, is_venda = 0");


$limitProcess = null; 
foreach ($data_all['data'] as $i => $item) {
    if ($limitProcess !== null && $i >= $limitProcess) break;

    $id_externo = $item['id'] ?? '';
    $nome = $item['name'] ?? 'Sem nome';
    $tipo = $item['type']['value'] ?? ($item['type'] ?? 'desconhecido');
    $raridade = $item['rarity']['value'] ?? ($item['rarity'] ?? 'comum');
    $imagem = $item['images']['icon'] ?? ($item['images']['icon'] ?? null);
    $preco = rand(200, 2500);
    $data_inclusao = isset($item['added']) ? substr($item['added'], 0, 10) : null;
    $dados_raw = json_encode($item, JSON_UNESCAPED_UNICODE);

    
    $is_novo = in_array($id_externo, $ids_new) ? 1 : 0;
    $is_venda = in_array($id_externo, $ids_shop) ? 1 : 0;
   
    $is_promocao = 0;

    
    $stmt = $pdo->prepare("SELECT id FROM cosmeticos WHERE id_externo = :id_externo");
    $stmt->execute([':id_externo' => $id_externo]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        $sql = "UPDATE cosmeticos SET nome=:nome, tipo=:tipo, raridade=:raridade, imagem=:imagem,
                preco=:preco, data_inclusao=:data_inclusao, dados_raw=:dados_raw,
                is_novo=:is_novo, is_venda=:is_venda, is_promocao=:is_promocao
                WHERE id_externo = :id_externo";
        $stmt2 = $pdo->prepare($sql);
        $stmt2->execute([
            ':nome'=>$nome, ':tipo'=>$tipo, ':raridade'=>$raridade, ':imagem'=>$imagem,
            ':preco'=>$preco, ':data_inclusao'=>$data_inclusao, ':dados_raw'=>$dados_raw,
            ':is_novo'=>$is_novo, ':is_venda'=>$is_venda, ':is_promocao'=>$is_promocao,
            ':id_externo'=>$id_externo
        ]);
        $totalAtualizados++;
    } else {
        $sql = "INSERT INTO cosmeticos (id_externo, nome, tipo, raridade, imagem, preco, data_inclusao, dados_raw, is_novo, is_venda, is_promocao)
                VALUES (:id_externo, :nome, :tipo, :raridade, :imagem, :preco, :data_inclusao, :dados_raw, :is_novo, :is_venda, :is_promocao)";
        $stmt2 = $pdo->prepare($sql);
        $stmt2->execute([
            ':id_externo'=>$id_externo, ':nome'=>$nome, ':tipo'=>$tipo, ':raridade'=>$raridade, ':imagem'=>$imagem,
            ':preco'=>$preco, ':data_inclusao'=>$data_inclusao, ':dados_raw'=>$dados_raw,
            ':is_novo'=>$is_novo, ':is_venda'=>$is_venda, ':is_promocao'=>$is_promocao
        ]);
        $totalInseridos++;
    }

    if ($i % 50 == 0) {
        echo "→ Processados: $i itens...<br>";
        flush(); ob_flush();
    }
}

echo "<h3>✅ Sincronização concluída!</h3>";
echo "<p>Inseridos: $totalInseridos — Atualizados: $totalAtualizados</p>";
echo "<p>Novos marcados: " . count($ids_new) . " — Em shop: " . count($ids_shop) . "</p>";
echo "<p><a href='index.php'>Voltar para a loja</a></p>";
