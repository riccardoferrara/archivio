# Soluzione: Errore Librerie PHP

## 🔴 Problema

PHP non si avvia a causa di librerie mancanti:
```
Library not loaded: '/usr/local/opt/libtiff/lib/libtiff.5.dylib'
```

## ✅ Soluzioni

### Soluzione 1: Reinstallare Dipendenze (Consigliato)

```bash
# Reinstalla le librerie problematiche
brew reinstall libtiff gd php
```

Poi prova:
```bash
make server
```

### Soluzione 2: Usare Script Automatico

```bash
chmod +x fix-php-libraries.sh
./fix-php-libraries.sh
```

### Soluzione 3: Creare Symlink Manualmente

```bash
# Trova versione libtiff installata
LIBTIFF_VER=$(brew list --versions libtiff | awk '{print $2}')

# Crea symlink
ln -s /usr/local/Cellar/libtiff/$LIBTIFF_VER /usr/local/opt/libtiff

# Verifica
ls -la /usr/local/opt/libtiff/lib/libtiff*.dylib
```

### Soluzione 4: Usare PHP di Sistema (Alternativa)

Se PHP di Homebrew continua a dare problemi, puoi usare quello di sistema:

```bash
# Verifica PHP di sistema
/usr/bin/php -v

# Modifica Makefile per usare PHP di sistema
# Cambia: php -S
# Con: /usr/bin/php -S
```

### Soluzione 5: Reinstallare Tutto PHP

```bash
# Rimuovi PHP
brew uninstall php

# Reinstalla
brew install php

# Verifica
php -v
make server
```

## 🔍 Diagnostica

### Verifica Librerie

```bash
# Verifica libtiff
brew list libtiff
ls -la /usr/local/opt/libtiff/lib/

# Verifica gd
brew list gd
ls -la /usr/local/opt/gd/lib/

# Verifica PHP
php -v
which php
```

### Verifica Dipendenze PHP

```bash
# Vedi quali librerie PHP cerca
otool -L $(which php) | grep libtiff
otool -L $(which php) | grep gd
```

## 🎯 Soluzione Rapida (Copia e Incolla)

```bash
# Fix completo
brew reinstall libtiff gd php && make server
```

## 💡 Prevenzione

Questo problema si verifica quando:
- Homebrew aggiorna le dipendenze
- PHP è stato compilato con versioni vecchie
- Symlink sono rotti o mancanti

**Soluzione permanente**: Reinstalla PHP dopo aggiornamenti Homebrew importanti.

## 🚀 Alternative: Server Diverso

Se PHP continua a dare problemi, puoi usare:

### MAMP (macOS)
- Download: https://www.mamp.info/
- Include PHP, MySQL, Apache
- Interfaccia grafica

### Docker
```bash
# Usa WordPress con Docker
docker run -p 8000:80 wordpress
```

### Valet (Laravel)
```bash
brew install php
composer global require laravel/valet
valet install
cd /Volumes/Data/dev/archivio
valet link archivio
# Apri: http://archivio.test
```

## ✅ Verifica Finale

Dopo aver applicato una soluzione:

```bash
# 1. Verifica PHP
php -v

# 2. Test server
php -S localhost:8000 &
curl http://localhost:8000

# 3. Se funziona, ferma e usa make
kill %1
make server
```

## 📞 Se Nulla Funziona

1. **Usa PHP di sistema**:
   ```bash
   /usr/bin/php -S localhost:8000
   ```

2. **Installa MAMP** (più semplice per sviluppo WordPress)

3. **Usa Docker** (isolamento completo)

4. **Chiedi supporto** con output di:
   ```bash
   php -v
   brew list | grep -E "(php|libtiff|gd)"
   ```


