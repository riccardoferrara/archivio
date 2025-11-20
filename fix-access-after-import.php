<?php
/**
 * Script per risolvere problemi di accesso dopo l'import
 * Uso: php fix-access-after-import.php
 */

require_once 'wp-load.php';

echo "=== Fix Accesso Dopo Import ===\n\n";

global $wpdb;

// 1. Verifica e fix URL
echo "1. VERIFICA URL:\n";
$home_url = get_option('home');
$site_url = get_option('siteurl');
echo "   Home URL attuale: $home_url\n";
echo "   Site URL attuale: $site_url\n";

$correct_url = 'http://localhost:8888';
if ($home_url != $correct_url || $site_url != $correct_url) {
    echo "   ⚠️ URL non corretti! Aggiornamento...\n";
    update_option('home', $correct_url);
    update_option('siteurl', $correct_url);
    echo "   ✓ URL aggiornati a: $correct_url\n";
} else {
    echo "   ✓ URL corretti\n";
}
echo "\n";

// 2. Sostituisci URL produzione nel database
echo "2. SOSTITUZIONE URL PRODUZIONE:\n";
$prod_urls = $wpdb->get_var("
    SELECT COUNT(*) 
    FROM wp_options 
    WHERE option_value LIKE '%archiviowebsite.com%'
");

if ($prod_urls > 0) {
    echo "   Trovate $prod_urls opzioni con URL produzione\n";
    echo "   Sostituzione in corso...\n";
    
    $wpdb->query("
        UPDATE wp_options 
        SET option_value = REPLACE(option_value, 'https://www.archiviowebsite.com', '$correct_url')
        WHERE option_value LIKE '%archiviowebsite.com%'
    ");
    
    $wpdb->query("
        UPDATE wp_options 
        SET option_value = REPLACE(option_value, 'http://www.archiviowebsite.com', '$correct_url')
        WHERE option_value LIKE '%archiviowebsite.com%'
    ");
    
    $updated = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM wp_options 
        WHERE option_value LIKE '%archiviowebsite.com%'
    ");
    
    if ($updated == 0) {
        echo "   ✓ Tutti gli URL sostituiti\n";
    } else {
        echo "   ⚠️ Rimangono $updated opzioni con URL produzione\n";
    }
} else {
    echo "   ✓ Nessun URL produzione trovato\n";
}
echo "\n";

// 3. Sostituisci URL in wp_posts
echo "3. SOSTITUZIONE URL IN POST E PAGINE:\n";
$posts_with_prod = $wpdb->get_var("
    SELECT COUNT(*) 
    FROM wp_posts 
    WHERE post_content LIKE '%archiviowebsite.com%' 
    OR guid LIKE '%archiviowebsite.com%'
");

if ($posts_with_prod > 0) {
    echo "   Trovati $posts_with_prod post/pagine con URL produzione\n";
    echo "   Sostituzione in corso (può richiedere tempo)...\n";
    
    $wpdb->query("
        UPDATE wp_posts 
        SET post_content = REPLACE(post_content, 'https://www.archiviowebsite.com', '$correct_url'),
            guid = REPLACE(guid, 'https://www.archiviowebsite.com', '$correct_url')
        WHERE post_content LIKE '%archiviowebsite.com%' 
        OR guid LIKE '%archiviowebsite.com%'
    ");
    
    $wpdb->query("
        UPDATE wp_posts 
        SET post_content = REPLACE(post_content, 'http://www.archiviowebsite.com', '$correct_url'),
            guid = REPLACE(guid, 'http://www.archiviowebsite.com', '$correct_url')
        WHERE post_content LIKE '%archiviowebsite.com%' 
        OR guid LIKE '%archiviowebsite.com%'
    ");
    
    $remaining = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM wp_posts 
        WHERE post_content LIKE '%archiviowebsite.com%' 
        OR guid LIKE '%archiviowebsite.com%'
    ");
    
    if ($remaining == 0) {
        echo "   ✓ Tutti gli URL sostituiti\n";
    } else {
        echo "   ⚠️ Rimangono $remaining post con URL produzione\n";
    }
} else {
    echo "   ✓ Nessun URL produzione nei post\n";
}
echo "\n";

// 4. Flush rewrite rules
echo "4. AGGIORNAMENTO PERMALINK:\n";
flush_rewrite_rules();
echo "   ✓ Rewrite rules aggiornate\n";
echo "\n";

// 5. Verifica connessione database
echo "5. VERIFICA CONNESSIONE:\n";
$test_query = $wpdb->get_var("SELECT COUNT(*) FROM wp_posts");
if ($test_query !== false) {
    echo "   ✓ Connessione database OK ($test_query post trovati)\n";
} else {
    echo "   ✗ Errore connessione database!\n";
    echo "   Verifica wp-config.php\n";
}
echo "\n";

// 6. Verifica file .htaccess
echo "6. VERIFICA .htaccess:\n";
if (file_exists('.htaccess')) {
    echo "   ✓ File .htaccess presente\n";
    $htaccess = file_get_contents('.htaccess');
    if (strpos($htaccess, 'RewriteEngine On') !== false) {
        echo "   ✓ RewriteEngine attivo\n";
    } else {
        echo "   ⚠️ RewriteEngine non trovato\n";
    }
} else {
    echo "   ⚠️ File .htaccess non trovato\n";
    echo "   WordPress potrebbe non funzionare correttamente\n";
}
echo "\n";

echo "=== COMPLETATO ===\n";
echo "\n";
echo "Ora prova ad accedere a:\n";
echo "  Frontend: $correct_url\n";
echo "  Admin: $correct_url/wp-admin\n";
echo "\n";
echo "Se ancora non funziona:\n";
echo "1. Verifica che MAMP sia avviato\n";
echo "2. Controlla che il database 'archivio_local' esista\n";
echo "3. Verifica wp-config.php (DB_NAME, DB_USER, DB_PASSWORD)\n";
echo "4. Controlla i log di errore PHP in MAMP\n";
echo "\n";

