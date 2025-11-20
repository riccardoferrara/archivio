# 🔍 Problema: Importazione Database Parziale

## Scoperta

Il database di produzione **CONTIENE** le immagini 2025/02, ma il database locale **NON** le ha.

### Verifica File SQL

Nel file `backups/db/production_20251108.sql`:
- ✅ **1263 occorrenze** di "2025/02"
- ✅ Trovato: `(184411, 22716, '_wp_attached_file', '2025/02/1479540_M-copia-scaled.jpg')`
- ✅ **1140 INSERT INTO `wp_posts`**

### Verifica Database Locale

Nel database locale `archivio_local`:
- ❌ **0 immagini 2025/02** registrate
- ❌ Homepage non contiene le immagini

## Cause Possibili

1. **Importazione fallita parzialmente**
   - Alcune righe non sono state importate
   - Errori di collation non gestiti completamente
   - Timeout durante l'importazione

2. **Errori durante l'importazione**
   - Errori MySQL ignorati
   - Righe saltate per vincoli o chiavi duplicate

3. **Database sovrascritto dopo l'importazione**
   - Un'altra importazione ha sovrascritto il database

## Soluzione

### Passo 1: Riavvia MySQL

```bash
# Avvia MAMP e verifica che MySQL sia in esecuzione
ps aux | grep mysql
```

### Passo 2: Verifica Importazione

```bash
# Conta attachment nel database locale
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "
SELECT COUNT(*) as 'Totale attachment' 
FROM wp_posts 
WHERE post_type = 'attachment';
"

# Cerca immagini 2025/02
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "
SELECT COUNT(*) as 'Immagini 2025/02' 
FROM wp_posts 
WHERE post_type = 'attachment' 
AND guid LIKE '%2025/02%';
"
```

### Passo 3: Reimporta Database

Se le immagini non ci sono, reimporta il database:

```bash
# Backup database locale attuale
make db-export

# Reimporta database di produzione
./import-db-production.sh backups/db/production_20251108.sql
```

### Passo 4: Verifica Errori

Durante la reimportazione, controlla se ci sono errori:
- Errori di collation
- Righe saltate
- Chiavi duplicate
- Timeout

## Note

Il problema è che l'importazione precedente **non ha importato tutti i dati**. Il file SQL contiene le immagini, ma il database locale no.

