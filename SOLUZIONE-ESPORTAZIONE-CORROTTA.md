# ❌ Problema: File SQL Corrotto

## Scoperta

Il file `production_20251108.sql` **non può essere importato** a causa di errori:

```
ERROR 1068 (42000): Multiple primary key defined
ERROR 1061 (42000): Duplicate key name 'original_fulltext'
ERROR 1061 (42000): Duplicate key name 'mkey'
```

**Risultato**: **0 tabelle importate**

## Cause

1. **Export da phpMyAdmin mal configurato**
2. **Incompatibilità MySQL 8.0 → MySQL 5.7**
3. **Database online corrotto**
4. **Tabelle wpstg (WordPress Staging) causano conflitti**

## Soluzione: Esporta Nuovo Database

### Passo 1: Accedi a phpMyAdmin su Aruba

1. Vai su **Aruba → phpMyAdmin**
2. Seleziona il database di produzione

### Passo 2: Export con Opzioni Corrette

1. Clicca **Export**
2. Seleziona **Custom** (Personalizzato)
3. Configura:
   - ✅ **Format**: SQL
   - ✅ **Tables**: Seleziona SOLO le tabelle che iniziano con `wp_` (NON `wpstg0_`)
   - ✅ **Structure**:
     - ✅ Add DROP TABLE / VIEW / PROCEDURE / FUNCTION / EVENT / TRIGGER
     - ✅ Add CREATE TABLE
     - ❌ Add CREATE PROCEDURE / FUNCTION / EVENT
   - ✅ **Data**:
     - ✅ Complete inserts
     - ❌ Extended inserts (causa problemi)
     - ✅ Disable foreign key checks
   - ✅ **Export Method**: Quick
4. Clicca **Go**

### Passo 3: Importa Nuovo Database

```bash
# Salva il nuovo file come database_produzione_nuovo.sql
./import-db-production.sh database_produzione_nuovo.sql
```

## Alternativa: Escludi Tabelle wpstg

Se non puoi esportare di nuovo, escludi le tabelle wpstg:

```bash
# Rimuovi righe wpstg dal file SQL
grep -v "wpstg0_" backups/db/production_20251108.sql > backups/db/production_clean.sql

# Importa file pulito
./import-db-production.sh backups/db/production_clean.sql
```

## Verifica

Dopo l'import:

```bash
# Conta tabelle importate
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "SHOW TABLES;"

# Verifica immagini 2025/02
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local -e "
SELECT COUNT(*) FROM wp_posts 
WHERE post_type = 'attachment' 
AND guid LIKE '%2025/02%';
"
```

## Conclusione

Il problema è il **file SQL corrotto**. Serve un nuovo export da phpMyAdmin con le opzioni corrette.

