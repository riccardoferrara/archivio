# Istruzioni Fix PHP - Esegui Questi Comandi

## 🚀 Soluzione Rapida

Ho creato uno script automatico. Esegui:

```bash
cd /Volumes/Data/dev/archivio
./fix-php-completo.sh
```

Questo script:
1. ✅ Verifica PHP disponibile (sistema o Homebrew)
2. ✅ Fixa librerie se necessario
3. ✅ Avvia il server automaticamente

## 🔧 Se lo Script Non Funziona

### Opzione 1: Usa PHP di Sistema

```bash
cd /Volumes/Data/dev/archivio
/usr/bin/php -S localhost:8000
```

Poi apri: **http://localhost:8000**

### Opzione 2: Fix Manuale Librerie

```bash
# Reinstalla dipendenze
brew reinstall libtiff gd php

# Poi prova
make server
```

### Opzione 3: Usa Make (Ora con Fallback)

Il Makefile è stato aggiornato per usare automaticamente PHP di sistema se Homebrew non funziona:

```bash
make server
```

## ✅ Verifica

Dopo aver avviato il server:

1. **Apri browser**: http://localhost:8000
2. **Dovresti vedere**: La homepage di WordPress
3. **Se vedi errori**: Controlla `wp-config.php` e database locale

## 📋 Checklist

- [ ] PHP disponibile (sistema o Homebrew)
- [ ] `wp-config.php` presente
- [ ] Database locale configurato
- [ ] URL locale: `http://localhost:8000`

## 🎯 Comandi Utili

```bash
# Avvia server (usa fallback automatico)
make server

# Oppure script completo
./fix-php-completo.sh

# Oppure direttamente PHP di sistema
/usr/bin/php -S localhost:8000

# Ferma server
make server-stop
# Oppure Ctrl+C
```

## 💡 Nota

Il Makefile ora prova automaticamente:
1. PHP Homebrew (se funziona)
2. PHP di sistema macOS (se Homebrew fallisce)

Quindi `make server` dovrebbe funzionare nella maggior parte dei casi!


