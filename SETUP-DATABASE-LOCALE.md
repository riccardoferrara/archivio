# Setup Database Locale MAMP

## ✅ wp-config.php Già Configurato!

Il tuo `wp-config.php` è già configurato per MAMP:

```php
DB_NAME: 'archivio_local'
DB_USER: 'root'
DB_PASSWORD: 'root'
DB_HOST: 'localhost'
```

Questo è perfetto per MAMP! ✅

## 📋 Prossimi Passi

### 1. Crea Database in MAMP

1. **Apri phpMyAdmin**: http://localhost:8888/phpMyAdmin
2. **Clicca su "Nuovo"** (New) nel menu sinistro
3. **Nome database**: `archivio_local`
4. **Collation**: `utf8mb4_unicode_ci` (o lascia default)
5. **Clicca "Crea"**

### 2. Importa Database da Produzione

Hai due opzioni:

#### Opzione A: Via phpMyAdmin (Semplice)

1. **Scarica database da produzione**:
   - Vai su phpMyAdmin di Aruba
   - Seleziona database produzione
   - Esporta → Go
   - Salva file `.sql`

2. **Importa in locale**:
   - Apri phpMyAdmin locale: http://localhost:8888/phpMyAdmin
   - Seleziona database `archivio_local`
   - Tab "Importa"
   - Scegli file `.sql`
   - Clicca "Esegui"

#### Opzione B: Via Makefile (Se hai accesso)

```bash
# Se hai già scaricato il database
make db-import FILE=backup_produzione.sql

# Sostituisci URL produzione → locale
make db-replace
```

### 3. Sostituisci URL nel Database

Dopo aver importato, sostituisci gli URL:

```bash
make db-replace
```

Questo sostituisce:
- `https://www.archiviowebsite.com/` → `http://localhost:8000`

### 4. Verifica Configurazione

```bash
# Verifica connessione database
make db-check

# Info database
make db-info
```

## 🔍 Verifica Setup Completo

### Test Database

1. **Apri phpMyAdmin**: http://localhost:8888/phpMyAdmin
2. **Seleziona database**: `archivio_local`
3. **Dovresti vedere**: Tabelle WordPress (wp_posts, wp_users, etc.)

### Test WordPress

1. **Apri browser**: http://localhost:8000 (o 8888 se usi MAMP diretto)
2. **Dovresti vedere**: Homepage WordPress
3. **Login**: http://localhost:8000/wp-admin

## ⚙️ Configurazione wp-config.php

Il file è già configurato correttamente, ma se devi modificarlo:

### Credenziali MAMP Default

```php
define( 'DB_NAME', 'archivio_local' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'root' );  // Default MAMP
define( 'DB_HOST', 'localhost' );
```

### Se MAMP Usa Porta Diversa

Se MySQL MAMP usa porta diversa (es. 8889):

```php
define( 'DB_HOST', 'localhost:8889' );
```

### URL Locale

Assicurati che in `sync-wp-ftp.sh` ci sia:

```bash
LOCAL_URL="http://localhost:8000"
```

## 🎯 Workflow Completo

```bash
# 1. Crea database in phpMyAdmin (manuale)

# 2. Importa database (via phpMyAdmin o make)
make db-import FILE=backup.sql

# 3. Sostituisci URL
make db-replace

# 4. Verifica
make db-check

# 5. Avvia server
make server

# 6. Apri browser
# http://localhost:8000
```

## ✅ Checklist

- [ ] Database `archiviowebsite_local` creato in phpMyAdmin
- [ ] Database importato da produzione
- [ ] URL sostituiti (`make db-replace`)
- [ ] `make db-check` funziona
- [ ] WordPress locale accessibile
- [ ] Login admin funziona

## 🐛 Troubleshooting

### Errore: "Error establishing database connection"

**Causa**: Database non esiste o credenziali sbagliate

**Soluzione**:
1. Verifica database esiste in phpMyAdmin
2. Controlla credenziali in wp-config.php
3. Verifica MySQL MAMP è avviato

### Errore: "Table doesn't exist"

**Causa**: Database vuoto

**Soluzione**: Importa database da produzione

### URL non funzionano

**Causa**: URL ancora puntano a produzione

**Soluzione**: Esegui `make db-replace`

## 💡 Nota Importante

**wp-config.php NON dovrebbe essere nel repository Git!**

Verifica che sia in `.gitignore`:
```bash
grep wp-config .gitignore
```

Se non c'è, aggiungilo per evitare di committare credenziali.


