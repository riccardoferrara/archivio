<?php
/**
 * Script per verificare le pagine nel database
 * Uso: php check-pages.php
 */

require_once 'wp-load.php';

echo "=== Verifica Pagine WordPress ===\n\n";

global $wpdb;

// 1. Conta pagine per status
echo "1. Pagine per status:\n";
$statuses = $wpdb->get_results("
    SELECT post_status, COUNT(*) as count 
    FROM {$wpdb->posts} 
    WHERE post_type = 'page' 
    GROUP BY post_status
    ORDER BY count DESC
");

foreach ($statuses as $status) {
    echo "   {$status->post_status}: {$status->count}\n";
}
echo "\n";

// 2. Pagine pubblicate
echo "2. Prime 10 pagine pubblicate:\n";
$pages = $wpdb->get_results("
    SELECT ID, post_title, post_name, post_status, post_date
    FROM {$wpdb->posts} 
    WHERE post_type = 'page' 
    AND post_status = 'publish'
    ORDER BY post_date DESC
    LIMIT 10
");

if (empty($pages)) {
    echo "   ✗ Nessuna pagina pubblicata trovata!\n";
} else {
    foreach ($pages as $page) {
        echo "   ID: {$page->ID} | {$page->post_title} | {$page->post_name} | {$page->post_status}\n";
    }
}
echo "\n";

// 3. Verifica homepage
echo "3. Configurazione homepage:\n";
$show_on_front = get_option('show_on_front');
$page_on_front = get_option('page_on_front');
echo "   show_on_front: $show_on_front\n";
echo "   page_on_front: $page_on_front\n";

if ($page_on_front) {
    $homepage = get_post($page_on_front);
    if ($homepage) {
        echo "   Homepage trovata: {$homepage->post_title} (ID: {$homepage->ID}, Status: {$homepage->post_status})\n";
    } else {
        echo "   ✗ Homepage con ID $page_on_front non trovata!\n";
    }
}
echo "\n";

// 4. Verifica Polylang
echo "4. Verifica Polylang:\n";
if (function_exists('pll_languages_list')) {
    echo "   ✓ Polylang attivo\n";
    $languages = pll_languages_list();
    echo "   Lingue: " . implode(', ', $languages) . "\n";
    
    // Conta pagine per lingua
    foreach ($languages as $lang) {
        $count = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) 
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE p.post_type = 'page' 
            AND p.post_status = 'publish'
            AND tt.taxonomy = 'language'
            AND t.slug = %s
        ", $lang));
        echo "   Pagine in $lang: $count\n";
    }
} else {
    echo "   Polylang non attivo\n";
}
echo "\n";

// 5. Verifica filtri admin
echo "5. Verifica filtri admin:\n";
$all_pages = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page'");
$published_pages = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'");
echo "   Totale pagine: $all_pages\n";
echo "   Pagine pubblicate: $published_pages\n";
echo "\n";

// 6. Suggerimenti
echo "=== Suggerimenti ===\n";
if ($published_pages == 0) {
    echo "✗ Nessuna pagina pubblicata! Le pagine potrebbero essere in stato 'draft' o 'private'.\n";
    echo "  Soluzione: Vai su wp-admin → Pagine e verifica i filtri (Mostra tutte le date, Tutti gli stati)\n";
} elseif ($published_pages > 0 && $page_on_front) {
    $homepage = get_post($page_on_front);
    if ($homepage && $homepage->post_status != 'publish') {
        echo "⚠️ La homepage (ID: $page_on_front) non è pubblicata! Status: {$homepage->post_status}\n";
        echo "  Soluzione: Pubblica la pagina o cambia homepage\n";
    }
}

if (function_exists('pll_languages_list')) {
    echo "ℹ️ Polylang è attivo. Le pagine potrebbero essere filtrate per lingua.\n";
    echo "  In wp-admin → Pagine, verifica il filtro lingua in alto a destra.\n";
}

echo "\n=== Completato ===\n";
