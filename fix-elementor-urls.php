<?php
/**
 * Script per sostituire URL nei dati serializzati di Elementor
 * Uso: php fix-elementor-urls.php
 */

require_once 'wp-load.php';

$old_url = 'https://www.archiviowebsite.com';
$new_url = 'http://localhost:8888';

echo "=== Sostituzione URL Elementor ===\n";
echo "Da: $old_url\n";
echo "A: $new_url\n\n";

global $wpdb;

// Trova tutti i postmeta di Elementor
$elementor_metas = $wpdb->get_results("
    SELECT post_id, meta_key, meta_value 
    FROM {$wpdb->postmeta} 
    WHERE meta_key LIKE '_elementor%'
    AND meta_value LIKE '%{$old_url}%'
");

echo "Trovati " . count($elementor_metas) . " postmeta di Elementor da aggiornare\n\n";

$updated = 0;
foreach ($elementor_metas as $meta) {
    // Sostituisce URL nel valore serializzato
    $new_value = str_replace($old_url, $new_url, $meta->meta_value);
    
    if ($new_value !== $meta->meta_value) {
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
echo "\nOra pulisci la cache:\n";
echo "1. WP Admin → WP Fastest Cache → Delete Cache\n";
echo "2. Ricarica la pagina con Cmd+Shift+R\n";

