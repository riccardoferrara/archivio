<?php
/**
 * Script per registrare immagini mancanti nel database WordPress
 * Trova immagini nella cartella uploads che non sono nel database
 */

require_once('wp-load.php');

global $wpdb;

$upload_dir = wp_upload_dir();
$base_dir = $upload_dir['basedir'];
$base_url = $upload_dir['baseurl'];

echo "=== Registrazione Immagini Mancanti ===\n\n";
echo "Cartella uploads: $base_dir\n";
echo "URL base: $base_url\n\n";

// Trova tutte le immagini nella cartella 2025/02
$images_dir = $base_dir . '/2025/02';
if (!is_dir($images_dir)) {
    echo "❌ Cartella $images_dir non trovata!\n";
    exit(1);
}

echo "Cercando immagini in: $images_dir\n\n";

$images = glob($images_dir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
echo "Trovate " . count($images) . " immagini\n\n";

$registered = 0;
$skipped = 0;

foreach ($images as $image_path) {
    $filename = basename($image_path);
    $relative_path = str_replace($base_dir . '/', '', $image_path);
    $url = $base_url . '/' . $relative_path;
    
    // Verifica se esiste già nel database
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE guid = %s AND post_type = 'attachment'",
        $url
    ));
    
    if ($existing) {
        $skipped++;
        continue;
    }
    
    // Ottieni informazioni file
    $file_info = wp_check_filetype($filename);
    $mime_type = $file_info['type'];
    
    if (!$mime_type) {
        $mime_type = 'image/jpeg'; // Default
    }
    
    // Crea attachment
    $attachment = array(
        'post_mime_type' => $mime_type,
        'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
        'guid' => $url
    );
    
    $attachment_id = wp_insert_attachment($attachment, $relative_path);
    
    if (!is_wp_error($attachment_id)) {
        // Aggiungi metadati
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata($attachment_id, $image_path);
        wp_update_attachment_metadata($attachment_id, $attach_data);
        
        $registered++;
        echo "✓ Registrata: $filename (ID: $attachment_id)\n";
    } else {
        echo "✗ Errore: $filename - " . $attachment_id->get_error_message() . "\n";
    }
    
    // Limita a 100 per volta per non sovraccaricare
    if ($registered >= 100) {
        echo "\n⚠️  Limite di 100 immagini raggiunto. Esegui di nuovo lo script per continuare.\n";
        break;
    }
}

echo "\n=== Completato ===\n";
echo "Registrate: $registered\n";
echo "Saltate (già esistenti): $skipped\n";
echo "\nOra rigenera la cache di Elementor:\n";
echo "1. WP Admin → Elementor → Tools → Regenerate CSS & Data\n";
echo "2. WP Admin → WP Fastest Cache → Delete Cache\n";

