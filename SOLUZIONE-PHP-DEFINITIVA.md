# Soluzione Definitiva: PHP su macOS

## 🔴 Problema Attuale

- PHP Homebrew: errore librerie (libtiff)
- PHP di sistema: non esiste su macOS moderno
- Server locale non si avvia

## ✅ Soluzioni (in ordine di semplicità)

### Soluzione 1: Fix Librerie (Prova Prima)

```bash
cd /Volumes/Data/dev/archivio
chmod +x fix-php-libtiff.sh
./fix-php-libtiff.sh
```

Poi:
```bash
make server
```

### Soluzione 2: Reinstallazione Completa PHP

```bash
# Rimuovi PHP
brew uninstall php

# Reinstalla tutto
brew install php

# Test
php -v
make server
```

### Soluzione 3: MAMP (Più Semplice - CONSIGLIATO)

**MAMP è la soluzione più semplice per WordPress su macOS:**

1. **Download**: https://www.mamp.info/en/downloads/
2. **Installa** MAMP (gratuito)
3. **Configura**:
   - Avvia MAMP
   - Clicca "Start Servers"
   - Document Root: `/Volumes/Data/dev/archivio`
   - Porta: 8888 (default)
4. **Apri**: http://localhost:8888

**Vantaggi:**
- ✅ Include PHP, MySQL, Apache
- ✅ Nessun problema librerie
- ✅ Interfaccia grafica
- ✅ Perfetto per WordPress

### Soluzione 4: Docker (Avanzato)

```bash
# Installa Docker Desktop
# Poi:
cd /Volumes/Data/dev/archivio
docker-compose up
```

### Soluzione 5: Valet (Laravel)

```bash
brew install php
composer global require laravel/valet
valet install
cd /Volumes/Data/dev/archivio
valet link archivio
# Apri: http://archivio.test
```

## 🎯 Raccomandazione

**Per sviluppo WordPress su macOS, MAMP è la scelta migliore:**
- ✅ Zero configurazione
- ✅ Funziona subito
- ✅ Include tutto (PHP, MySQL)
- ✅ Nessun problema librerie

## 📋 Setup MAMP (Passo-Passo)

1. **Scarica MAMP**: https://www.mamp.info/en/downloads/
2. **Installa** (trascina in Applications)
3. **Avvia MAMP**
4. **Configura Document Root**:
   - Preferenze → Web Server
   - Document Root: `/Volumes/Data/dev/archivio`
5. **Start Servers**
6. **Apri**: http://localhost:8888

## 🔧 Se Vuoi Continuare con Homebrew

### Fix Completo

```bash
# 1. Fix librerie
./fix-php-libtiff.sh

# 2. Se non funziona, reinstallazione
brew uninstall php
brew install php

# 3. Test
php -v
make server
```

### Verifica Installazione

```bash
# Verifica PHP
which php
php -v

# Verifica librerie
otool -L $(which php) | grep libtiff

# Verifica symlink
ls -la /usr/local/opt/libtiff/lib/
```

## 💡 Alternative Rapide

### Usa Server Remoto per Test

Se il server locale continua a dare problemi, puoi:
1. Testare ottimizzazioni direttamente su produzione (con backup!)
2. Usare staging se disponibile
3. Usare MAMP (più semplice)

## ✅ Checklist

- [ ] Prova `./fix-php-libtiff.sh`
- [ ] Se non funziona, installa MAMP
- [ ] Configura MAMP con Document Root corretto
- [ ] Testa server locale
- [ ] Verifica WordPress funziona

## 🚀 Prossimi Passi

1. **Scegli soluzione** (MAMP consigliato)
2. **Configura server locale**
3. **Testa ottimizzazioni database**
4. **Sviluppa in sicurezza locale**


