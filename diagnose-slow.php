<?php
/**
 * Script di diagnostica per WordPress lento
 * Esegui: php diagnose-slow.php
 */

// Verifica che siamo nella directory WordPress
if (!file_exists(__DIR__ . '/wp-load.php')) {
    echo "ERRORE: wp-load.php non trovato!\n";
    echo "Assicurati di eseguire lo script dalla directory WordPress.\n";
    exit(1);
}

// Carica WordPress
try {
    require_once __DIR__ . '/wp-load.php';
} catch (Exception $e) {
    echo "ERRORE nel caricamento WordPress: " . $e->getMessage() . "\n";
    exit(1);
}

// Verifica che WordPress sia caricato
if (!function_exists('get_option')) {
    echo "ERRORE: WordPress non caricato correttamente!\n";
    exit(1);
}

echo "=== Diagnostica WordPress Lento ===\n\n";

// 1. Verifica debug mode
echo "1. Debug Mode:\n";
if (defined('WP_DEBUG') && WP_DEBUG) {
    echo "   ⚠️ WP_DEBUG è ATTIVO (rallenta!)\n";
} else {
    echo "   ✅ WP_DEBUG disattivo\n";
}

if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
    echo "   ⚠️ WP_DEBUG_LOG attivo\n";
}

if (defined('SAVEQUERIES') && SAVEQUERIES) {
    echo "   ⚠️ SAVEQUERIES attivo (salva tutte le query)\n";
}

// 2. Conta query
echo "\n2. Query Database:\n";
global $wpdb;
$query_count = get_num_queries();
echo "   Query eseguite: $query_count\n";

if (isset($wpdb->queries) && is_array($wpdb->queries)) {
    $slow_queries = 0;
    $total_time = 0;
    foreach ($wpdb->queries as $query) {
        if (isset($query[1]) && $query[1] > 0.1) {
            $slow_queries++;
        }
        if (isset($query[1])) {
            $total_time += $query[1];
        }
    }
    echo "   Query lente (>0.1s): $slow_queries\n";
    echo "   Tempo totale query: " . number_format($total_time, 3) . "s\n";
}

// 3. Plugin attivi
echo "\n3. Plugin Attivi:\n";
$active_plugins = get_option('active_plugins');
echo "   Numero plugin: " . count($active_plugins) . "\n";
if (count($active_plugins) > 20) {
    echo "   ⚠️ Troppi plugin attivi!\n";
}

// 4. Transients
echo "\n4. Transients:\n";
global $wpdb;
$transients = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'");
echo "   Transients totali: $transients\n";
$expired = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()");
echo "   Transients scaduti: $expired\n";
if ($expired > 50) {
    echo "   ⚠️ Molti transients scaduti (rallentano!)\n";
}

// 5. Dimensioni database
echo "\n5. Dimensioni Database:\n";
$tables = $wpdb->get_results("SHOW TABLE STATUS");
$total_size = 0;
foreach ($tables as $table) {
    $size = ($table->Data_length + $table->Index_length) / 1024 / 1024;
    $total_size += $size;
    if ($size > 10) {
        echo "   {$table->Name}: " . number_format($size, 2) . " MB\n";
    }
}
echo "   Dimensione totale: " . number_format($total_size, 2) . " MB\n";

// 6. Autoload options
echo "\n6. Opzioni Autoload:\n";
$autoload = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->options} WHERE autoload = 'yes'");
echo "   Opzioni autoload: $autoload\n";
if ($autoload > 500) {
    echo "   ⚠️ Troppe opzioni autoload (rallenta!)\n";
}

// 7. Post revisions
echo "\n7. Revisioni Post:\n";
$revisions = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'revision'");
echo "   Revisioni totali: $revisions\n";
if ($revisions > 1000) {
    echo "   ⚠️ Troppe revisioni (rallenta!)\n";
}

// 8. Cache
echo "\n8. Cache:\n";
if (defined('WP_CACHE') && WP_CACHE) {
    echo "   ✅ Cache attiva\n";
} else {
    echo "   ⚠️ Cache NON attiva\n";
}

// 9. Raccomandazioni
echo "\n=== RACCOMANDAZIONI ===\n";
$recommendations = [];

if (defined('WP_DEBUG') && WP_DEBUG) {
    $recommendations[] = "Disattiva WP_DEBUG in produzione";
}

if ($expired > 50) {
    $recommendations[] = "Pulisci transients scaduti";
}

if ($revisions > 1000) {
    $recommendations[] = "Limita o elimina revisioni vecchie";
}

if ($autoload > 500) {
    $recommendations[] = "Riduci opzioni autoload";
}

if (count($active_plugins) > 20) {
    $recommendations[] = "Disattiva plugin non necessari";
}

if (!defined('WP_CACHE') || !WP_CACHE) {
    $recommendations[] = "Attiva cache (WP Super Cache o W3 Total Cache)";
}

if (empty($recommendations)) {
    echo "✅ Nessun problema critico rilevato\n";
} else {
    foreach ($recommendations as $i => $rec) {
        echo ($i + 1) . ". $rec\n";
    }
}

echo "\n=== Fine Diagnostica ===\n";

