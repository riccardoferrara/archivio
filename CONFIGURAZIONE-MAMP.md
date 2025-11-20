# Configurazione MAMP per WordPress

## ✅ MAMP Installato e Funzionante

Ottimo! Ora configura MAMP per usare la tua directory WordPress.

## 🔧 Setup MAMP

### 1. Configura Document Root

1. **Apri MAMP**
2. **Vai su**: Preferenze (⚙️) → Web Server
3. **Document Root**: `/Volumes/Data/dev/archivio`
4. **Porta**: 8888 (default) o 80 (se preferisci)
5. **Salva**

### 2. Avvia Server

1. **Clicca "Start Servers"** in MAMP
2. Aspetta che Apache e MySQL siano verdi (avviati)

### 3. Accedi al Sito

Apri nel browser:
- **http://localhost:8888** (se porta 8888)
- **http://localhost** (se porta 80)

## 🎯 Usa `make server` con MAMP

Ora il Makefile cerca automaticamente PHP di MAMP se Homebrew non funziona.

```bash
make server
```

Dovrebbe trovare automaticamente PHP di MAMP e avviare il server su **http://localhost:8000**

## ⚠️ Nota: Due Server

Con MAMP avviato, potresti avere:
- **MAMP**: http://localhost:8888 (Apache completo)
- **make server**: http://localhost:8000 (PHP built-in)

**Scegli uno:**
- **MAMP (8888)**: Più completo, include Apache
- **make server (8000)**: Più semplice, solo PHP

## 🔍 Verifica Configurazione

### Test PHP MAMP

```bash
# Trova PHP MAMP
find /Applications/MAMP/bin/php -name php -type f | head -1

# Test versione
/Applications/MAMP/bin/php/php*/bin/php -v
```

### Test Database MAMP

1. Apri phpMyAdmin: http://localhost:8888/phpMyAdmin
2. Verifica che il database WordPress esista
3. Se non esiste, importalo da produzione

## 📋 Configurazione Database WordPress

### 1. Crea Database in MAMP

1. Apri phpMyAdmin: http://localhost:8888/phpMyAdmin
2. Crea nuovo database: `archivio_local`
3. Importa database da produzione (se necessario)

### 2. Configura wp-config.php

Assicurati che `wp-config.php` abbia:

```php
define('DB_NAME', 'archivio_local');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');  // Default MAMP
define('DB_HOST', 'localhost');
```

### 3. Sostituisci URL nel Database

**IMPORTANTE:** Il database contiene ancora gli URL di produzione. Devi sostituirli con l'URL locale.

**Opzione A - Usa il comando Makefile (se WP-CLI funziona):**
```bash
# Sostituisci URL produzione → locale
make db-replace
```

**Opzione B - Sostituzione manuale via MySQL:**

Se `make db-replace` non funziona, sostituisci manualmente gli URL:

```bash
# Sostituisci URL nel database
/Applications/MAMP/Library/bin/mysql -uroot -proot archivio_local <<EOF
UPDATE wp_options SET option_value = 'http://localhost:8888' WHERE option_name = 'siteurl';
UPDATE wp_options SET option_value = 'http://localhost:8888' WHERE option_name = 'home';
EOF
```

**Nota:** Se usi una porta diversa da 8888, cambia `localhost:8888` con la tua porta (es: `localhost:80`).

## ✅ Checklist Setup Completo

- [ ] MAMP installato e avviato
- [ ] Document Root configurato: `/Volumes/Data/dev/archivio`
- [ ] Server MAMP avviati (Apache + MySQL)
- [ ] Database locale creato (`archivio_local`)
- [ ] wp-config.php configurato (DB_NAME, DB_USER, DB_PASSWORD, DB_HOST)
- [x] **URL locali configurati in wp-config.php** (già fatto, sicuro!)
- [ ] Test: http://localhost:8888 funziona

## 🔧 Passaggi da Completare per Vedere il Sito

### 1. Verifica Document Root in MAMP

1. Apri **MAMP**
2. Vai su **Preferenze** (⚙️) → **Web Server**
3. Verifica che **Document Root** sia: `/Volumes/Data/dev/archivio`
4. Se non lo è, cambialo e **salva**
5. **Riavvia** i server MAMP (Stop → Start)

### 2. Verifica Database

Il database `archivio_local` esiste già e contiene i dati. Verifica:

```bash
# Verifica connessione database
make db-info
```

Dovresti vedere le dimensioni delle tabelle. Se funziona, il database è OK.

### 3. Configura URL Locali (Senza Modificare il Database) ✅ SICURO

**Soluzione Sicura:** Invece di modificare il database (che causerebbe problemi quando fai push online), abbiamo aggiunto le definizioni in `wp-config.php` che forzano gli URL locali.

**✅ Già Configurato:** Il file `wp-config.php` contiene già:
```php
define('WP_HOME', 'http://localhost:8888');
define('WP_SITEURL', 'http://localhost:8888');
```

**Vantaggi:**
- ✅ Non modifica il database
- ✅ Puoi fare push del database online senza problemi
- ✅ Funziona immediatamente
- ✅ Facile da rimuovere quando non serve più

**Se usi porta 80 invece di 8888:**
Modifica `wp-config.php` e cambia:
```php
define('WP_HOME', 'http://localhost');
define('WP_SITEURL', 'http://localhost');
```

**⚠️ IMPORTANTE:** Quando fai push online, **rimuovi queste righe** da `wp-config.php` prima di caricare il file sul server!

### 4. Testa il Sito

Apri nel browser:
- **http://localhost:8888** (se porta 8888)
- **http://localhost** (se porta 80)

Dovresti vedere il sito WordPress!

## 🚀 Comandi Utili

```bash
# Avvia server con PHP MAMP
make server

# Verifica database
make db-check

# Ottimizza database
make db-optimize

# Info database
make db-info
```

## 💡 Vantaggi MAMP

- ✅ PHP funziona senza problemi librerie
- ✅ MySQL incluso
- ✅ phpMyAdmin incluso
- ✅ Interfaccia grafica
- ✅ Perfetto per WordPress

## 🎯 Prossimi Passi (Ordine di Esecuzione)

1. ✅ **MAMP è già avviato** (verificato)
2. ⚠️ **Verifica Document Root** in MAMP → `/Volumes/Data/dev/archivio`
3. ✅ **URL locali configurati** in `wp-config.php` (già fatto, non tocca il database!)
4. ✅ **Testa**: http://localhost:8888

## 🐛 Risoluzione Problemi

### Errore 404 Not Found
- Verifica che Document Root sia `/Volumes/Data/dev/archivio`
- Riavvia MAMP dopo aver cambiato Document Root

### Errore "Errore nella connessione al database"
- Verifica che MySQL sia avviato in MAMP (icona verde)
- Verifica credenziali in `wp-config.php`:
  - DB_NAME: `archivio_local`
  - DB_USER: `root`
  - DB_PASSWORD: `root`
  - DB_HOST: `localhost`

### Il sito si carica ma mostra URL sbagliati
- Gli URL nel database non sono stati sostituiti
- Esegui il comando della sezione 3 per sostituire gli URL

### Immagini/CSS non si caricano
- Gli URL nel database potrebbero essere ancora quelli di produzione
- Verifica con: `make db-info` e controlla gli URL nel database

Tutto pronto! 🎉

