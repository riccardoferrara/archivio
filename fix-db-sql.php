<?php
/**
 * Fix database via SQL diretto (senza wp-cli)
 * Esegui: php fix-db-sql.php
 */

// Carica WordPress
require_once __DIR__ . '/wp-load.php';

global $wpdb;

echo "=== Fix Database (SQL Diretto) ===\n\n";

// 1. Pulisci transients scaduti
echo "1. Pulisco transients scaduti...\n";
$deleted = $wpdb->query("
    DELETE FROM {$wpdb->options} 
    WHERE option_name LIKE '_transient_timeout_%' 
    AND option_value < UNIX_TIMESTAMP()
");
echo "   ✓ Eliminati: $deleted transients scaduti\n\n";

// 2. Rimuovi transients da autoload
echo "2. Rimuovo transients da autoload...\n";
$updated = $wpdb->query("
    UPDATE {$wpdb->options} 
    SET autoload = 'no' 
    WHERE option_name LIKE '_transient_%' 
    AND autoload = 'yes'
");
echo "   ✓ Aggiornate: $updated opzioni\n\n";

// 3. Ottimizza tabelle principali
echo "3. Ottimizzo tabelle...\n";
$tables = ['wp_options', 'wp_posts', 'wp_postmeta', 'wp_terms', 'wp_term_relationships'];
foreach ($tables as $table) {
    $wpdb->query("OPTIMIZE TABLE {$table}");
    echo "   ✓ Ottimizzata: $table\n";
}

echo "\n=== Fix Completato ===\n";


