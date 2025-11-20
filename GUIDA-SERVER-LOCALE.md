# Guida: Server Locale e Test Ottimizzazione

## 🎯 Verificare se l'Ottimizzazione ha Funzionato

### Metodo 1: Usando `make db-optimize` (Automatico)

Il comando ora mostra le dimensioni **prima e dopo**:

```bash
make db-optimize
```

Vedrai:
```
=== Ottimizzazione Database ===
Dimensioni PRIMA dell'ottimizzazione:
Database: 45.2 MB

✓ Database ottimizzato

Dimensioni DOPO l'ottimizzazione:
Database: 42.1 MB  ← Spazio recuperato!
```

### Metodo 2: Verifica Manuale

```bash
# Prima dell'ottimizzazione
make db-info

# Dopo l'ottimizzazione
make db-info
```

Confronta le dimensioni delle tabelle.

### Metodo 3: Test Performance Query

```bash
# Test velocità query
make db-query SQL="SELECT COUNT(*) FROM wp_posts"
```

Se l'ottimizzazione ha funzionato, le query dovrebbero essere più veloci.

## 🖥️ Server Locale WordPress

### Prerequisiti

1. **PHP installato**:
   ```bash
   # Verifica se hai PHP
   php -v
   
   # Se non ce l'hai, installalo (macOS)
   brew install php
   ```

2. **WordPress configurato localmente**:
   - Deve esistere `wp-config.php`
   - Database locale configurato
   - URL locale configurato (es. `http://localhost`)

### Avviare il Server

```bash
make server
```

Questo:
- ✅ Avvia server PHP built-in
- ✅ Disponibile su `http://localhost:8000`
- ✅ Funziona come un server web normale
- ✅ Supporta tutti i file PHP di WordPress

### Fermare il Server

**Opzione 1**: Premi `Ctrl+C` nel terminale dove gira il server

**Opzione 2**: In un altro terminale:
```bash
make server-stop
```

## 📋 Setup Completo Server Locale

### 1. Configura Database Locale

Se non hai ancora configurato WordPress localmente:

```bash
# Crea database locale (se usi MySQL/MariaDB)
mysql -u root -p
CREATE DATABASE archivio_local;
exit

# Configura wp-config.php
# Modifica con le credenziali del tuo database locale
```

### 2. Importa Database da Produzione

```bash
# Scarica database da produzione (via phpMyAdmin o altro metodo)
# Poi importa:
make db-import FILE=backup_produzione.sql

# Sostituisci URL produzione → locale
make db-replace
```

### 3. Avvia Server

```bash
make server
```

### 4. Apri nel Browser

Vai su: **http://localhost:8000**

## 🔍 Test Ottimizzazione con Server Locale

### Workflow Completo

```bash
# 1. Verifica dimensioni PRIMA
make db-info

# 2. Ottimizza database
make db-optimize
# (vedrai dimensioni prima/dopo)

# 3. Avvia server locale
make server

# 4. Testa nel browser
# Apri http://localhost:8000
# Naviga il sito e verifica velocità

# 5. Confronta performance
# Prima: lento?
# Dopo: più veloce?
```

## ⚙️ Configurazione URL Locale

Assicurati che `sync-wp-ftp.sh` abbia:

```bash
LOCAL_URL="http://localhost:8000"
```

Quando importi il database da produzione, usa:

```bash
make db-replace
```

Questo sostituisce automaticamente gli URL produzione → locale.

## 🐛 Risoluzione Problemi

### Errore: "PHP non trovato"
```bash
# Installa PHP
brew install php

# Verifica installazione
php -v
```

### Errore: "wp-config.php non trovato"
- Assicurati di essere nella directory corretta
- Configura WordPress localmente prima

### Errore: "Porta 8000 già in uso"
```bash
# Ferma il server esistente
make server-stop

# Oppure usa porta diversa (modifica Makefile)
```

### Errore: "Database connection failed"
- Verifica credenziali in `wp-config.php`
- Assicurati che MySQL/MariaDB sia in esecuzione
- Verifica che il database esista

### Sito non carica correttamente
- Verifica che gli URL siano sostituiti: `make db-replace`
- Controlla permalink: vai su Admin → Impostazioni → Permalink → Salva

## 📊 Confronto Performance

### Prima dell'Ottimizzazione
```bash
make db-info
# Nota le dimensioni

make server
# Apri http://localhost:8000
# Nota la velocità di caricamento
```

### Dopo l'Ottimizzazione
```bash
make db-optimize
# Confronta dimensioni prima/dopo

make server
# Apri http://localhost:8000
# Confronta velocità
```

## 💡 Suggerimenti

1. **Usa sempre backup prima di ottimizzare**:
   ```bash
   make db-export
   make db-optimize
   ```

2. **Testa regolarmente**:
   - Ottimizza mensilmente
   - Verifica performance con server locale

3. **Confronta locale vs produzione**:
   - Locale: per test e sviluppo
   - Produzione: per utenti finali

4. **Usa strumenti browser**:
   - Chrome DevTools (F12) → Network
   - Vedi tempi di caricamento risorse
   - Confronta prima/dopo ottimizzazione

## 🎯 Comandi Rapidi

```bash
# Info database
make db-info

# Ottimizza (mostra prima/dopo)
make db-optimize

# Avvia server
make server

# Ferma server
make server-stop

# Backup prima di ottimizzare
make db-export
```

## ✅ Checklist Setup

- [ ] PHP installato (`php -v`)
- [ ] wp-cli installato (`wp --version`)
- [ ] Database locale configurato
- [ ] wp-config.php presente
- [ ] URL locale configurato in sync-wp-ftp.sh
- [ ] Database importato da produzione (opzionale)
- [ ] URL sostituiti (`make db-replace`)

Una volta completato, puoi:
- ✅ Testare ottimizzazioni localmente
- ✅ Sviluppare senza toccare produzione
- ✅ Verificare performance in sicurezza


