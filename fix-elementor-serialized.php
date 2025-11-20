<?php
/**
 * Script per sostituire URL nei dati serializzati di Elementor
 * Gestisce correttamente JSON serializzato
 */

// Carica WordPress
require_once('wp-load.php');

global $wpdb;

$old_urls = array(
    'https://www.archiviowebsite.com',
    'http://www.archiviowebsite.com',
    'https://archiviowebsite.com',
    'http://archiviowebsite.com',
    'archiviowebsite.com',
    'www.archiviowebsite.com'
);

$new_url = 'localhost:8888';

echo "=== Sostituzione URL Elementor (Serializzato) ===\n\n";

// Trova tutti i postmeta di Elementor
$elementor_metas = $wpdb->get_results("
    SELECT post_id, meta_key, meta_value 
    FROM {$wpdb->postmeta} 
    WHERE meta_key LIKE '_elementor%'
    AND meta_value LIKE '%archiviowebsite%'
");

echo "Trovati " . count($elementor_metas) . " postmeta di Elementor\n\n";

$updated = 0;
foreach ($elementor_metas as $meta) {
    $new_value = $meta->meta_value;
    $changed = false;
    
    // Sostituisce tutte le varianti di URL
    foreach ($old_urls as $old_url) {
        // Con protocollo completo
        if (strpos($new_value, $old_url) !== false) {
            $new_value = str_replace($old_url, 'http://' . $new_url, $new_value);
            $changed = true;
        }
        
        // Senza protocollo (con escape)
        $old_escaped = str_replace('/', '\/', $old_url);
        $new_escaped = str_replace('/', '\/', $new_url);
        if (strpos($new_value, $old_escaped) !== false) {
            $new_value = str_replace($old_escaped, $new_escaped, $new_value);
            $changed = true;
        }
        
        // Doppio escape
        $old_double = str_replace('/', '\\\\/', $old_url);
        $new_double = str_replace('/', '\\\\/', $new_url);
        if (strpos($new_value, $old_double) !== false) {
            $new_value = str_replace($old_double, $new_double, $new_value);
            $changed = true;
        }
    }
    
    if ($changed && $new_value !== $meta->meta_value) {
        $wpdb->update(
            $wpdb->postmeta,
            array('meta_value' => $new_value),
            array(
                'post_id' => $meta->post_id,
                'meta_key' => $meta->meta_key
            ),
            array('%s'),
            array('%d', '%s')
        );
        
        $post_title = $wpdb->get_var($wpdb->prepare(
            "SELECT post_title FROM {$wpdb->posts} WHERE ID = %d",
            $meta->post_id
        ));
        
        echo "✓ Aggiornato: {$post_title} (ID: {$meta->post_id}) - {$meta->meta_key}\n";
        $updated++;
    }
}

echo "\n=== Completato ===\n";
echo "Aggiornati: $updated postmeta\n";

// Verifica rimanenti
$remaining = $wpdb->get_var("
    SELECT COUNT(*) 
    FROM {$wpdb->postmeta} 
    WHERE meta_key LIKE '_elementor%'
    AND meta_value LIKE '%archiviowebsite%'
");

echo "Rimasti: $remaining postmeta con URL vecchi\n";
echo "\nOra pulisci la cache:\n";
echo "1. WP Admin → WP Fastest Cache → Delete Cache\n";
echo "2. Ricarica la pagina con Cmd+Shift+R\n";

