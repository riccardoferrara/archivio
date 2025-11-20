# 🔍 Diagnosi: Database Vecchio o Incompleto

## Problema Identificato

La **homepage** (ID: 9954) mostra una struttura diversa da quella online perché:

### ⚠️ **Homepage Non Aggiornata**
- **Ultima modifica locale**: 2022-04-22 13:02:16
- **Database esportato**: 2025-11-08 19:47:15
- **Gap temporale**: ~3.5 anni!

### ⚠️ **Duplicati nei Postmeta**
- Ci sono **4 copie duplicate** di `_elementor_data` per la homepage
- Questo può causare conflitti e comportamenti imprevedibili

## Soluzioni

### Opzione 1: Esporta Nuovo Database (CONSIGLIATO)

Il database che hai importato è **vecchio** (homepage del 2022). Devi esportare un **nuovo database** da phpMyAdmin su Aruba:

1. **Accedi a phpMyAdmin su Aruba**
2. **Seleziona il database di produzione**
3. **Esporta** (Export) → **Go**
4. **Salva il file** (es: `database_produzione_2025.sql`)
5. **Importa** con:
   ```bash
   ./import-db-production.sh database_produzione_2025.sql
   ```

### Opzione 2: Sincronizza Solo la Homepage

Se non puoi esportare tutto il database, puoi sincronizzare solo la homepage:

1. **Online**: Vai su WP Admin → Pagine → Homepage → Modifica con Elementor
2. **Copia il contenuto** di `_elementor_data` da phpMyAdmin online
3. **Locale**: Sostituisci `_elementor_data` nella homepage locale

### Opzione 3: Rimuovi Duplicati

Prima di tutto, rimuovi i duplicati:

```bash
# Rimuove duplicati di _elementor_data per la homepage
# Mantiene solo il più recente
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local << 'SQL'
DELETE pm1 FROM wp_postmeta pm1
INNER JOIN wp_postmeta pm2 
WHERE pm1.post_id = 9954
AND pm1.meta_key = '_elementor_data'
AND pm2.post_id = 9954
AND pm2.meta_key = '_elementor_data'
AND pm1.meta_id < pm2.meta_id;
SQL
```

## Verifica

Dopo aver importato il nuovo database:

```bash
# Verifica data ultima modifica homepage
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "SELECT post_modified FROM wp_posts WHERE ID = 9954;"

# Verifica che non ci siano duplicati
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "SELECT COUNT(*) FROM wp_postmeta WHERE post_id = 9954 AND meta_key = '_elementor_data';"
```

## Conclusione

**Il problema principale è che il database importato è vecchio** (homepage del 2022). Devi esportare un nuovo database da produzione per avere la struttura aggiornata.

