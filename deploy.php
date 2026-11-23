<?php
set_time_limit(0);
header('Content-Type: text/plain; charset=utf-8');
$base = __DIR__;

echo "=== LOCALIZANDO ARQUIVOS index.blade.php ===\n\n";

// 1. Procura todos os arquivos index.blade.php no projeto
echo "-> Buscando arquivos 'index.blade.php' na pasta resources/...\n";
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base . '/resources', FilesystemIterator::SKIP_DOTS)
);

$encontrados = [];
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getFilename() === 'index.blade.php') {
        $path = $file->getPathname();
        $linhas = file($path);
        $linha19 = isset($linhas[18]) ? trim($linhas[18]) : 'N/A';
        echo "   ARQUIVO: {$path}\n";
        echo "   Linha 19: {$linha19}\n\n";
        $encontrados[] = $path;
    }
}

if (empty($encontrados)) {
    echo "   ERRO: Nenhum arquivo 'index.blade.php' encontrado em resources/!\n";
    exit;
}

// 2. Verifica qual arquivo está sendo compilado no cache
echo "-> Verificando arquivos de cache de views...\n";
$viewsDir = $base . '/storage/framework/views/';
if (is_dir($viewsDir)) {
    $files = glob($viewsDir . '*.php');
    echo "   Total de views compiladas no cache: " . count($files) . "\n";
    foreach ($files as $file) {
        $conteudo = file_get_contents($file);
        if (strpos($conteudo, '$aviso->alocacao->posto->nome') !== false) {
            echo "   ⚠️ CACHE CONTÉM CÓDIGO ANTIGO: " . basename($file) . "\n";
            // Mostra a linha que contém o problema
            $linhas = explode("\n", $conteudo);
            foreach ($linhas as $i => $linha) {
                if (strpos($linha, '$aviso->alocacao->posto->nome') !== false) {
                    echo "   Linha " . ($i + 1) . ": " . trim($linha) . "\n";
                    break;
                }
            }
        }
    }
}

// 3. Verifica se o arquivo principal foi realmente alterado
echo "\n-> Verificando se o arquivo principal foi alterado...\n";
$viewPrincipal = $base . '/resources/views/admin/ausencias/index.blade.php';
if (file_exists($viewPrincipal)) {
    $conteudo = file_get_contents($viewPrincipal);
    if (strpos($conteudo, '@if ($aviso->alocacao && $aviso->alocacao->posto') !== false) {
        echo "   ✅ O arquivo principal JÁ CONTÉM a proteção @if.\n";
    } else {
        echo "   ❌ O arquivo principal NÃO CONTÉM a proteção @if. O código antigo ainda está lá.\n";
        echo "   Conteúdo da linha 19: " . (isset(explode("\n", $conteudo)[18]) ? trim(explode("\n", $conteudo)[18]) : 'N/A') . "\n";
    }
} else {
    echo "   ❌ Arquivo principal não existe em: {$viewPrincipal}\n";
}

echo "\n=== FIM DA VERIFICAÇÃO ===\n";