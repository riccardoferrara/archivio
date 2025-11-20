# Fix Immagini Homepage - Database e Cache

## Problema
Le immagini nella homepage sono vecchie anche se il database è stato importato.

## Soluzione Completa

### 1. Pulisci Cache WordPress

```bash
# Rimuovi cache file
rm -rf wp-content/cache/wpfc/*
rm -rf wp-content/cache/autoptimize/*
rm -rf wp-content/cache/*

# Rimuovi transients dal database
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "DELETE FROM wp_options WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%';"
```

### 2. Pulisci Cache da WP Admin

1. Vai su **WP Admin → WP Fastest Cache → Delete Cache**
2. Clicca "Delete Cache"
3. Se usi Autoptimize, vai su **Settings → Autoptimize → Delete Cache**

### 3. Verifica Database

```bash
# Verifica quante immagini ci sono
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%';"

# Verifica data ultima immagine
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "SELECT MAX(post_date) FROM wp_posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image%';"

# Verifica immagini 2025/02
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "SELECT COUNT(*) FROM wp_posts WHERE post_type = 'attachment' AND guid LIKE '%2025/02%';"
```

### 4. Se le Immagini sono in Elementor

Se usi Elementor, le immagini potrebbero essere salvate nei postmeta. Verifica:

```bash
# Trova ID homepage
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "SELECT ID FROM wp_posts WHERE post_type = 'page' AND post_name = 'home' LIMIT 1;"

# Sostituisci ID_HOMEPAGE con l'ID trovato sopra
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "SELECT meta_key, LEFT(meta_value, 200) FROM wp_postmeta WHERE post_id = ID_HOMEPAGE AND meta_key LIKE '%elementor%' LIMIT 10;"
```

### 5. Hard Refresh Browser

Dopo aver pulito la cache:
- **Mac**: Cmd + Shift + R
- **Windows/Linux**: Ctrl + F5

### 6. Se il Database è Vecchio

Se il database importato è vecchio, esporta un nuovo database da phpMyAdmin su Aruba e reimportalo:

```bash
./import-db-production.sh /path/to/nuovo_database.sql
```

## Script Automatico

Esegui lo script creato:

```bash
./verify-and-fix-db.sh
```

Oppure:

```bash
./fix-homepage-images.sh
```

## Verifica Finale

1. Apri http://localhost:8888
2. Fai hard refresh (Cmd+Shift+R)
3. Verifica che le immagini siano aggiornate

## Se Ancora Non Funziona

Potrebbe essere che:
1. **Il database esportato è vecchio** - Esporta un nuovo database da phpMyAdmin
2. **Le immagini sono in postmeta di Elementor** - Potrebbero essere serializzate e non sostituite
3. **Cache del browser** - Prova in modalità incognito
4. **Plugin cache** - Disattiva temporaneamente WP Fastest Cache

