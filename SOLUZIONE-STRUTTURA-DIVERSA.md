# 🔍 Soluzione: Struttura Homepage Diversa

## Problema Identificato

Il database è **recente** (7 novembre 2025), ma la **homepage locale ha una struttura diversa** da quella online.

### Analisi

✅ **Database aggiornato**: 7 novembre 2025  
✅ **URL sostituiti correttamente**: 22 occorrenze `localhost:8888`  
❌ **Immagini 2025/02 NON nei dati Elementor**: 0 trovati  
❌ **Homepage non contiene le nuove immagini**

### Possibili Cause

1. **Homepage modificata DOPO l'esportazione** (dopo il 7 novembre)
2. **Immagini aggiunte ma non salvate** nei postmeta di Elementor
3. **Cache di Elementor** che mostra dati vecchi

## Soluzioni

### Opzione 1: Esporta Nuovo Database (CONSIGLIATO)

Se la homepage è stata modificata dopo il 7 novembre:

1. **Accedi a phpMyAdmin su Aruba**
2. **Esporta il database** (Export → Go)
3. **Importa** con:
   ```bash
   ./import-db-production.sh database_produzione_2025.sql
   ```

### Opzione 2: Sincronizza Solo Homepage

Se non puoi esportare tutto il database:

1. **Online**: Vai su WP Admin → Pagine → Homepage → Modifica con Elementor
2. **Copia il contenuto** di `_elementor_data` da phpMyAdmin online:
   ```sql
   SELECT meta_value FROM wp_postmeta 
   WHERE post_id = 9954 AND meta_key = '_elementor_data';
   ```
3. **Locale**: Sostituisci `_elementor_data`:
   ```bash
   /Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "
   UPDATE wp_postmeta 
   SET meta_value = '[INCOLLA_QUI_IL_VALORE_ONLINE]' 
   WHERE post_id = 9954 AND meta_key = '_elementor_data';
   "
   ```

### Opzione 3: Rigenera Cache Elementor

1. **WP Admin → Elementor → Tools → Regenerate CSS & Data**
2. **Pulisci cache**: WP Fastest Cache → Delete Cache
3. **Hard refresh**: Cmd+Shift+R

## Verifica

Dopo aver applicato la soluzione:

```bash
# Verifica che le immagini 2025/02 siano nei dati Elementor
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "
SELECT COUNT(*) FROM wp_postmeta 
WHERE post_id = 9954 
AND meta_key = '_elementor_data' 
AND meta_value LIKE '%2025/02%';
"
```

## Conclusione

Il database è recente, ma la **homepage potrebbe essere stata modificata dopo l'esportazione**. La soluzione migliore è esportare un nuovo database o sincronizzare solo la homepage.

