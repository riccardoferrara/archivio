# Fix: Errore Server Locale

## 🔴 Problema

Errore quando esegui `make server`:
```
dyld: Library not loaded: '/usr/local/opt/libtiff/lib/libtiff.5.dylib'
```

## ✅ Soluzione Applicata

Ho modificato il comando `make server` per usare **PHP built-in server** invece di `wp server`.

**Vantaggi:**
- ✅ Non dipende da wp-cli per il server
- ✅ Più compatibile
- ✅ Nessun problema con librerie mancanti
- ✅ Funziona con qualsiasi versione PHP

## 🚀 Come Usare

```bash
make server
```

Ora usa `php -S` invece di `wp server`, che è più semplice e compatibile.

## 🔧 Se Ancora Non Funziona

### Opzione 1: Reinstallare Dipendenze

```bash
# Reinstalla libtiff
brew reinstall libtiff

# Reinstalla gd
brew reinstall gd

# Reinstalla PHP
brew reinstall php
```

### Opzione 2: Usare PHP Direttamente

Se `make server` ancora non funziona, puoi avviare il server manualmente:

```bash
cd /Volumes/Data/dev/archivio
php -S localhost:8000
```

Poi apri: **http://localhost:8000**

### Opzione 3: Usare Porta Diversa

Se la porta 8000 è occupata:

```bash
# Modifica il Makefile o usa direttamente:
php -S localhost:8080
```

## 📋 Verifica Setup

Assicurati di avere:

1. **PHP installato**:
   ```bash
   php -v
   ```

2. **wp-config.php presente**:
   ```bash
   ls wp-config.php
   ```

3. **Database locale configurato** in `wp-config.php`

4. **URL locale configurato**:
   ```bash
   # In sync-wp-ftp.sh
   LOCAL_URL="http://localhost:8000"
   ```

## 🎯 Test Completo

```bash
# 1. Verifica PHP
php -v

# 2. Avvia server
make server

# 3. In un altro terminale, testa:
curl http://localhost:8000

# 4. Apri nel browser
# http://localhost:8000
```

## 💡 Note

- Il server PHP built-in è **solo per sviluppo**
- Non usarlo in produzione
- Funziona perfettamente per test locali
- Supporta tutti i file PHP di WordPress

## 🐛 Altri Problemi

### Porta già in uso
```bash
# Trova processo sulla porta 8000
lsof -ti:8000

# Fermalo
kill $(lsof -ti:8000)

# Oppure usa porta diversa
php -S localhost:8080
```

### Database connection failed
- Verifica credenziali in `wp-config.php`
- Assicurati che MySQL/MariaDB sia in esecuzione
- Verifica che il database esista

### Permalink non funzionano
1. Vai su Admin → Impostazioni → Permalink
2. Clicca "Salva" (anche senza modificare)
3. Questo rigenera le regole `.htaccess`


