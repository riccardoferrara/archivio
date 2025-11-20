<?php
/**
 * Script per verificare e fixare la visibilità delle pagine
 * Uso: php fix-pages-visibility.php
 */

require_once 'wp-load.php';

echo "=== Fix Visibilità Pagine ===\n\n";

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

// 2. Pubblica tutte le pagine che sono in draft o private (tranne quelle di sistema)
echo "2. Pubblicazione pagine in draft/private...\n";
$updated = $wpdb->query("
    UPDATE {$wpdb->posts} 
    SET post_status = 'publish' 
    WHERE post_type = 'page' 
    AND post_status IN ('draft', 'private', 'pending')
    AND post_name NOT IN ('sample-page', 'privacy-policy')
");

echo "   ✓ $updated pagine pubblicate\n";
echo "\n";

// 3. Verifica Polylang
echo "3. Verifica Polylang:\n";
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
    
    // Se ci sono pagine senza lingua, assegna la lingua di default
    $pages_without_lang = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'language'
        WHERE p.post_type = 'page' 
        AND p.post_status = 'publish'
        AND tr.object_id IS NULL
    ");
    
    if ($pages_without_lang > 0) {
        echo "   ⚠️ $pages_without_lang pagine senza lingua assegnata\n";
        echo "   (Potrebbero non essere visibili in wp-admin se Polylang filtra per lingua)\n";
    }
} else {
    echo "   Polylang non attivo\n";
}
echo "\n";

// 4. Verifica homepage
echo "4. Verifica homepage:\n";
$show_on_front = get_option('show_on_front');
$page_on_front = get_option('page_on_front');
echo "   show_on_front: $show_on_front\n";
echo "   page_on_front: $page_on_front\n";

if ($page_on_front) {
    $homepage = get_post($page_on_front);
    if ($homepage) {
        echo "   Homepage: {$homepage->post_title} (ID: {$homepage->ID}, Status: {$homepage->post_status})\n";
        if ($homepage->post_status != 'publish') {
            echo "   ⚠️ Homepage non pubblicata! Pubblicazione...\n";
            wp_update_post(array('ID' => $page_on_front, 'post_status' => 'publish'));
            echo "   ✓ Homepage pubblicata\n";
        }
    }
}
echo "\n";

// 5. Lista prime 10 pagine pubblicate
echo "5. Prime 10 pagine pubblicate:\n";
$pages = $wpdb->get_results("
    SELECT ID, post_title, post_name, post_status
    FROM {$wpdb->posts} 
    WHERE post_type = 'page' 
    AND post_status = 'publish'
    ORDER BY post_date DESC
    LIMIT 10
");

foreach ($pages as $page) {
    echo "   ID: {$page->ID} | {$page->post_title} | {$page->post_name}\n";
}
echo "\n";

echo "=== Completato ===\n";
echo "Ora vai su wp-admin → Pagine e verifica:\n";
echo "1. Filtri in alto (Mostra tutte le date, Tutti gli stati)\n";
if (function_exists('pll_languages_list')) {
    echo "2. Filtro lingua in alto a destra (seleziona 'Tutte le lingue')\n";
}
echo "3. Ricarica la pagina con Cmd+Shift+R\n";
