<?php
/**
 * Script completo per verificare e risolvere il problema delle pagine non visibili
 * Questo script verifica il database corretto e risolve i problemi comuni
 */

// Carica WordPress
require_once 'wp-load.php';

echo "=== VERIFICA E FIX PAGINE ===\n\n";

global $wpdb;

// Verifica connessione database
$db_name = DB_NAME;
echo "Database configurato: $db_name\n";
echo "Database effettivo: {$wpdb->dbname}\n\n";

// 1. Conta tutte le pagine
echo "1. CONTEGGIO PAGINE:\n";
$all_pages = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page'");
$published = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'");
$draft = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'draft'");
$private = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'private'");
$trash = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'trash'");

echo "   Totale: $all_pages\n";
echo "   Pubblicate: $published\n";
echo "   Draft: $draft\n";
echo "   Private: $private\n";
echo "   Cestino: $trash\n";
echo "\n";

if ($all_pages == 0) {
    echo "⚠️ PROBLEMA: Nessuna pagina trovata nel database!\n";
    echo "\n";
    echo "Possibili cause:\n";
    echo "1. Il database non è stato importato correttamente\n";
    echo "2. Il database configurato in wp-config.php non corrisponde a quello importato\n";
    echo "3. Le tabelle hanno un prefisso diverso da 'wp_'\n";
    echo "\n";
    echo "Verifica:\n";
    echo "- Controlla che il database 'archivio_local' esista\n";
    echo "- Verifica che le tabelle siano state create (wp_posts, wp_options, etc.)\n";
    echo "- Controlla il prefisso delle tabelle in wp-config.php\n";
    exit(1);
}

// 2. Pubblica pagine in draft/private
if ($draft > 0 || $private > 0) {
    echo "2. PUBBLICAZIONE PAGINE:\n";
    $updated = $wpdb->query("
        UPDATE {$wpdb->posts} 
        SET post_status = 'publish' 
        WHERE post_type = 'page' 
        AND post_status IN ('draft', 'private', 'pending')
        AND post_name NOT IN ('sample-page', 'privacy-policy')
    ");
    echo "   ✓ $updated pagine pubblicate\n";
    echo "\n";
}

// 3. Verifica Polylang
echo "3. VERIFICA POLYLANG:\n";
if (function_exists('pll_languages_list')) {
    echo "   ✓ Polylang attivo\n";
    $languages = pll_languages_list();
    echo "   Lingue: " . implode(', ', $languages) . "\n";
    
    $default_lang = pll_default_language();
    echo "   Lingua default: $default_lang\n";
    
    // Trova pagine senza lingua
    $pages_without_lang = $wpdb->get_results("
        SELECT p.ID, p.post_title
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'language'
        WHERE p.post_type = 'page' 
        AND p.post_status = 'publish'
        AND tr.object_id IS NULL
        LIMIT 50
    ");
    
    if (!empty($pages_without_lang)) {
        echo "   ⚠️ " . count($pages_without_lang) . " pagine senza lingua\n";
        
        // Ottieni term_taxonomy_id della lingua di default
        $lang_term = $wpdb->get_row($wpdb->prepare("
            SELECT tt.term_taxonomy_id
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
            WHERE tt.taxonomy = 'language'
            AND t.slug = %s
        ", $default_lang));
        
        if ($lang_term) {
            echo "   Assegnazione lingua $default_lang...\n";
            $assigned = 0;
            foreach ($pages_without_lang as $page) {
                // Verifica se già esiste
                $exists = $wpdb->get_var($wpdb->prepare("
                    SELECT COUNT(*) 
                    FROM {$wpdb->term_relationships}
                    WHERE object_id = %d 
                    AND term_taxonomy_id = %d
                ", $page->ID, $lang_term->term_taxonomy_id));
                
                if (!$exists) {
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
            }
            echo "   ✓ $assigned pagine assegnate alla lingua $default_lang\n";
        }
    } else {
        echo "   ✓ Tutte le pagine hanno una lingua\n";
    }
} else {
    echo "   Polylang non attivo\n";
}
echo "\n";

// 4. Flush rewrite rules
echo "4. AGGIORNAMENTO PERMALINK:\n";
flush_rewrite_rules();
echo "   ✓ Rewrite rules aggiornate\n";
echo "\n";

// 5. Lista pagine pubblicate
echo "5. PRIME 30 PAGINE PUBBLICATE:\n";
$pages = $wpdb->get_results("
    SELECT ID, post_title, post_name, post_status, post_date
    FROM {$wpdb->posts} 
    WHERE post_type = 'page' 
    AND post_status = 'publish'
    ORDER BY menu_order, post_date DESC
    LIMIT 30
");

if (empty($pages)) {
    echo "   ✗ Nessuna pagina pubblicata!\n";
} else {
    foreach ($pages as $page) {
        $url = get_permalink($page->ID);
        echo "   ID: {$page->ID} | {$page->post_title} | {$page->post_name}\n";
        echo "      URL: $url\n";
    }
}
echo "\n";

// 6. Verifica homepage
echo "6. VERIFICA HOMEPAGE:\n";
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

echo "=== COMPLETATO ===\n";
echo "\n";
echo "Ora verifica in wp-admin:\n";
echo "1. Vai su wp-admin → Pagine\n";
echo "2. Controlla i filtri:\n";
echo "   - 'Mostra tutte le date'\n";
echo "   - 'Tutti gli stati'\n";
if (function_exists('pll_languages_list')) {
    echo "   - 'Tutte le lingue' (in alto a destra)\n";
}
echo "3. Fai un hard refresh: Cmd+Shift+R\n";
echo "\n";

