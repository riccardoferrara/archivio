# Aumentare Limiti Upload PHP in MAMP

Se vuoi comunque usare phpMyAdmin, devi aumentare i limiti di PHP.

## Metodo 1: Modifica php.ini di MAMP

1. Trova il file `php.ini`:
   ```bash
   /Applications/MAMP/bin/php/php7.4.21/conf/php.ini
   ```
   (Sostituisci `php7.4.21` con la versione PHP che stai usando)

2. Apri il file e modifica queste righe:
   ```ini
   upload_max_filesize = 512M
   post_max_size = 512M
   max_execution_time = 300
   memory_limit = 512M
   ```

3. Riavvia MAMP

4. Verifica i nuovi limiti:
   ```bash
   /Applications/MAMP/bin/php/php7.4.21/bin/php -i | grep -E "(upload_max_filesize|post_max_size)"
   ```

## Metodo 2: Usa la Riga di Comando (Consigliato)

Il file è troppo grande per phpMyAdmin. Usa lo script che ho creato:

```bash
./import-large-database.sh
```

Questo script:
- Bypassa i limiti di PHP
- Usa MySQL direttamente dalla riga di comando
- Gestisce automaticamente la password
- Crea un backup prima dell'import
- Mostra il progresso e verifica i risultati

## Metodo 3: Dividi il File SQL

Se preferisci usare phpMyAdmin, puoi dividere il file:

```bash
# Installa split se non ce l'hai
# Su macOS: brew install coreutils

# Dividi il file in parti da 50MB
split -b 50M backups/db/Swp1868342-prod.sql.local backups/db/Swp1868342-prod.sql.local.part

# Poi importa ogni parte in sequenza via phpMyAdmin
```

## Raccomandazione

**Usa il Metodo 2** (script `import-large-database.sh`) - è il più semplice e affidabile per file grandi.

