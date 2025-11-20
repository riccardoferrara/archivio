# Soluzione: Pagine Non Visibili

## Problema Identificato

Il database `archivio_local` è stato importato ma contiene solo **1 riga** in `wp_posts` invece delle migliaia attese. Questo significa che l'import non è andato a buon fine.

## Diagnosi

- ✅ Database `archivio_local` esiste
- ✅ Tabelle WordPress create (13 tabelle)
- ❌ Solo 1 riga in `wp_posts` (dovrebbero essere migliaia)
- ❌ Nessuna pagina visibile

## Cause Possibili

1. **Password MySQL errata durante l'import** - Lo script di import usa password "root" ma potrebbe non essere corretta
2. **Import fallito silenziosamente** - Il file SQL (223MB) non è stato importato completamente
3. **Errori durante l'import** - Potrebbero esserci errori nel file SQL che hanno interrotto l'import

## Soluzioni

### Opzione 1: Reimport Manuale via phpMyAdmin (Consigliato)

1. Apri phpMyAdmin: http://localhost:8888/phpMyAdmin
2. Seleziona database `archivio_local`
3. Vai su "Importa" (Import)
4. Scegli file: `backups/db/Swp1868342-prod.sql.local`
5. Impostazioni:
   - Formato: SQL
   - Dimensione massima: 512MB (o aumenta se necessario)
6. Clicca "Esegui" (Go)
7. Attendi il completamento (può richiedere 5-10 minuti)

### Opzione 2: Reimport via Terminale (con password corretta)

1. Trova la password MySQL corretta:
   - Controlla `wp-config.php`: `DB_PASSWORD`
   - O prova password comuni: "", "root", "password"

2. Esegui:
```bash
cd /Volumes/Data/dev/archivio
MYSQL_PASSWORD="tua_password_qui" ./import-full-database.sh
```

### Opzione 3: Usa wp-cli (se disponibile)

```bash
cd /Volumes/Data/dev/archivio
wp db import backups/db/Swp1868342-prod.sql.local
```

## Dopo il Reimport

Dopo aver reimportato correttamente, esegui:

```bash
php verify-and-fix-pages.php
```

Questo script:
- Pubblica tutte le pagine in draft/private
- Assegna lingua Polylang se necessario
- Aggiorna i permalink
- Mostra l'elenco delle pagine importate

## Verifica Finale

1. Vai su wp-admin → Pagine
2. Verifica i filtri:
   - "Mostra tutte le date"
   - "Tutti gli stati"
   - Se Polylang è attivo: "Tutte le lingue"
3. Dovresti vedere tutte le 131 pagine

## Note

- Il file SQL originale contiene 131 pagine (verificato)
- Il database attuale ha solo 1 riga in wp_posts
- L'import deve essere completato per vedere le pagine

