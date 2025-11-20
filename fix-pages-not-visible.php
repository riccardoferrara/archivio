<?php
/**
 * Script completo per risolvere il problema delle pagine non visibili
 * Uso: php fix-pages-not-visible.php
 */

// Carica WordPress
require_once 'wp-load.php';

echo "=== Fix Pagine Non Visibili ===\n\n";

global $wpdb;

// 1. Diagnostica iniziale
echo "1. DIAGNOSTICA INIZIALE:\n";
$all_pages = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page'");
$published_pages = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'");
$draft_pages = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'draft'");
$private_pages = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'private'");

echo "   Totale pagine: $all_pages\n";
echo "   Pubblicate: $published_pages\n";
echo "   Draft: $draft_pages\n";
echo "   Private: $private_pages\n";
echo "\n";

// 2. Pubblica tutte le pagine in draft/private
if ($draft_pages > 0 || $private_pages > 0) {
    echo "2. PUBBLICAZIONE PAGINE IN DRAFT/PRIVATE:\n";
    $updated = $wpdb->query("
        UPDATE {$wpdb->posts} 
        SET post_status = 'publish' 
        WHERE post_type = 'page' 
        AND post_status IN ('draft', 'private', 'pending')
        AND post_name NOT IN ('sample-page', 'privacy-policy')
    ");
    echo "   ✓ $updated pagine pubblicate\n";
    echo "\n";
} else {
    echo "2. Nessuna pagina in draft/private da pubblicare\n\n";
}

// 3. Verifica e fix Polylang
echo "3. VERIFICA POLYLANG:\n";
if (function_exists('pll_languages_list')) {
    echo "   ✓ Polylang attivo\n";
    $languages = pll_languages_list();
    echo "   Lingue configurate: " . implode(', ', $languages) . "\n";
    
    $default_lang = pll_default_language();
    echo "   Lingua di default: $default_lang\n";
    
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
    
    // Trova pagine senza lingua e assegna lingua di default
    $pages_without_lang = $wpdb->get_results("
        SELECT p.ID, p.post_title
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'language'
        WHERE p.post_type = 'page' 
        AND p.post_status = 'publish'
        AND tr.object_id IS NULL
        LIMIT 20
    ");
    
    if (!empty($pages_without_lang)) {
        echo "   ⚠️ Trovate " . count($pages_without_lang) . " pagine senza lingua assegnata\n";
        echo "   Assegnazione lingua di default ($default_lang)...\n";
        
        // Ottieni il term_id della lingua di default
        $lang_term = $wpdb->get_row($wpdb->prepare("
            SELECT tt.term_taxonomy_id
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'language'
            AND t.slug = %s
        ", $default_lang));
        
        if ($lang_term) {
            $assigned = 0;
            foreach ($pages_without_lang as $page) {
                $wpdb->insert(
                    $wpdb->term_relationships,
                    array(
                        'object_id' => $page->ID,
                        'term_taxonomy_id' => $lang_term->term_taxonomy_id
                    ),
                    array('%d', '%d')
                );
                $assigned++;
            }
            echo "   ✓ $assigned pagine assegnate alla lingua $default_lang\n";
        }
    } else {
        echo "   ✓ Tutte le pagine hanno una lingua assegnata\n";
    }
} else {
    echo "   Polylang non attivo\n";
}
echo "\n";

// 4. Verifica homepage
echo "4. VERIFICA HOMEPAGE:\n";
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
    } else {
        echo "   ✗ Homepage con ID $page_on_front non trovata!\n";
    }
} else {
    echo "   ℹ️ Nessuna pagina impostata come homepage\n";
}
echo "\n";

// 5. Flush rewrite rules
echo "5. AGGIORNAMENTO PERMALINK:\n";
flush_rewrite_rules();
echo "   ✓ Rewrite rules aggiornate\n";
echo "\n";

// 6. Lista prime 20 pagine pubblicate
echo "6. PRIME 20 PAGINE PUBBLICATE:\n";
$pages = $wpdb->get_results("
    SELECT ID, post_title, post_name, post_status, post_date
    FROM {$wpdb->posts} 
    WHERE post_type = 'page' 
    AND post_status = 'publish'
    ORDER BY menu_order, post_date DESC
    LIMIT 20
");

if (empty($pages)) {
    echo "   ✗ Nessuna pagina pubblicata trovata!\n";
} else {
    foreach ($pages as $page) {
        $url = get_permalink($page->ID);
        echo "   ID: {$page->ID} | {$page->post_title} | {$page->post_name} | URL: $url\n";
    }
}
echo "\n";

// 7. Verifica URL nel database
echo "7. VERIFICA URL NEL DATABASE:\n";
$home_url = get_option('home');
$site_url = get_option('siteurl');
echo "   Home URL: $home_url\n";
echo "   Site URL: $site_url\n";

$prod_urls = $wpdb->get_var("
    SELECT COUNT(*) 
    FROM {$wpdb->posts} 
    WHERE post_content LIKE '%archiviowebsite.com%' 
    OR guid LIKE '%archiviowebsite.com%'
");
echo "   Righe con URL produzione: $prod_urls\n";
if ($prod_urls > 0) {
    echo "   ⚠️ Ci sono ancora URL di produzione nel database!\n";
    echo "   Esegui: wp search-replace 'https://www.archiviowebsite.com' '$home_url'\n";
}
echo "\n";

echo "=== COMPLETATO ===\n";
echo "\n";
echo "PROSSIMI PASSI:\n";
echo "1. Vai su wp-admin → Pagine\n";
echo "2. Verifica i filtri in alto:\n";
echo "   - 'Mostra tutte le date'\n";
echo "   - 'Tutti gli stati'\n";
if (function_exists('pll_languages_list')) {
    echo "   - 'Tutte le lingue' (filtro in alto a destra)\n";
}
echo "3. Ricarica la pagina con Cmd+Shift+R (hard refresh)\n";
echo "4. Se ancora non vedi le pagine, verifica:\n";
echo "   - wp-admin → Impostazioni → Permalink (salva di nuovo)\n";
echo "   - wp-admin → Impostazioni → Generale (verifica URL)\n";
echo "\n";

