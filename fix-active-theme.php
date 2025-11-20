<?php
/**
 * Script per verificare e fixare il tema attivo
 * Uso: php fix-active-theme.php
 */

require_once 'wp-load.php';

echo "=== Verifica Tema Attivo ===\n\n";

// Verifica tema attivo
$template = get_option('template');
$stylesheet = get_option('stylesheet');

echo "Tema template: $template\n";
echo "Tema stylesheet: $stylesheet\n\n";

// Verifica se il tema esiste
$theme_dir = get_theme_root() . '/' . $stylesheet;
if (is_dir($theme_dir)) {
    echo "✓ Tema trovato in: $theme_dir\n";
} else {
    echo "✗ Tema NON trovato in: $theme_dir\n";
    echo "\nTemi disponibili:\n";
    $themes = wp_get_themes();
    foreach ($themes as $theme_slug => $theme) {
        echo "  - $theme_slug: {$theme->get('Name')}\n";
    }
    
    // Prova ad attivare valeska se esiste
    if (isset($themes['valeska'])) {
        echo "\nTentativo di attivazione tema 'valeska'...\n";
        switch_theme('valeska');
        echo "✓ Tema 'valeska' attivato\n";
    } elseif (isset($themes['valeska-child-server'])) {
        echo "\nTentativo di attivazione tema 'valeska-child-server'...\n";
        switch_theme('valeska-child-server');
        echo "✓ Tema 'valeska-child-server' attivato\n";
    }
}

echo "\n=== Completato ===\n";
