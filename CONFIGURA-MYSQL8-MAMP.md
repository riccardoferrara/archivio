# Configura MySQL 8.0 in MAMP

## 🔍 Verifica Installazione MySQL 8.0

Prima di tutto, verifica dove hai installato MySQL 8.0:

```bash
# Verifica se MySQL 8.0 è nel PATH
which mysql
mysql --version

# Cerca MySQL installato via Homebrew
brew list mysql 2>/dev/null
ls -la /opt/homebrew/opt/mysql*/bin/mysql 2>/dev/null
ls -la /usr/local/opt/mysql*/bin/mysql 2>/dev/null

# Cerca MySQL installato manualmente
ls -la /usr/local/mysql*/bin/mysql 2>/dev/null
```

## 🔧 Soluzioni per Configurare MySQL 8.0 in MAMP

### Soluzione 1: Symlink (Più Semplice) ⭐ CONSIGLIATO

Crea un symlink per far usare a MAMP il MySQL 8.0 esterno:

```bash
# 1. Ferma MAMP (Stop Servers)

# 2. Trova il percorso di MySQL 8.0
# Se installato via Homebrew:
MYSQL8_PATH="/opt/homebrew/opt/mysql@8.0/bin"  # o mysql@8.1, mysql@8.2, etc.
# Oppure:
MYSQL8_PATH="/usr/local/opt/mysql@8.0/bin"

# Se installato manualmente:
MYSQL8_PATH="/usr/local/mysql/bin"

# 3. Verifica che il percorso esista
ls -la "$MYSQL8_PATH/mysql"

# 4. Backup dei binari MySQL di MAMP (opzionale ma consigliato)
cd /Applications/MAMP/Library/bin
mkdir -p mysql_backup_5.7
mv mysql mysqldump mysqladmin mysqld mysqlcheck mysqlimport mysqld_safe mysql_secure_installation mysql_upgrade mysql_config mysql_install_db mysql_plugin mysql_ssl_rsa_setup mysql_tzinfo_to_sql mysqlbinlog mysqld_multi mysqlpump mysqlslap mysqlclient_test mysqldump mysqltest mysql_client_test_embedded mysqld-debug mysqlshow mysql_client_test_embedded mysqld_multi mysql_secure_installation mysql_config_editor mysqld_safe mysql_ssl_rsa_setup mysql_install_db mysqldump mysql_upgrade mysql_plugin mysql_tzinfo_to_sql mysqlbinlog mysqlpump mysqlslap mysqltest mysqlshow mysql_config_editor 2>/dev/null
mv mysql* mysql_backup_5.7/ 2>/dev/null || true

# 5. Crea symlink a MySQL 8.0
ln -s "$MYSQL8_PATH/mysql" /Applications/MAMP/Library/bin/mysql
ln -s "$MYSQL8_PATH/mysqldump" /Applications/MAMP/Library/bin/mysqldump
ln -s "$MYSQL8_PATH/mysqladmin" /Applications/MAMP/Library/bin/mysqladmin
ln -s "$MYSQL8_PATH/mysqld" /Applications/MAMP/Library/bin/mysqld

# 6. Verifica
/Applications/MAMP/Library/bin/mysql --version
# Dovrebbe mostrare: mysql  Ver 8.0.x
```

### Soluzione 2: Modifica Script MAMP

MAMP Free usa uno script per avviare MySQL. Puoi modificarlo:

```bash
# Trova lo script di avvio MySQL di MAMP
find /Applications/MAMP -name "*mysql*" -type f | grep -E "start|launch|init" | head -5

# Di solito è in:
# /Applications/MAMP/bin/startMysql.sh
# o
# /Applications/MAMP/bin/startMysql.sh
```

### Soluzione 3: Usa MySQL 8.0 come Servizio di Sistema

Se MySQL 8.0 è installato come servizio di sistema:

```bash
# 1. Ferma MySQL di MAMP
# (Stop Servers in MAMP)

# 2. Avvia MySQL 8.0 di sistema
brew services start mysql@8.0
# oppure
sudo /usr/local/mysql/support-files/mysql.server start

# 3. Configura MAMP per usare MySQL di sistema
# Modifica lo script di avvio MAMP per non avviare il suo MySQL
```

### Soluzione 4: Aggiorna MAMP all'Ultima Versione

L'ultima versione di MAMP include già MySQL 8.0:

1. **Scarica MAMP** dall'ultima versione: https://www.mamp.info/en/downloads/
2. **Installa** (sovrascrive la versione esistente)
3. **Verifica** che MySQL 8.0 sia incluso

## 🎯 Soluzione Rapida: Script Automatico

Ho creato uno script che fa tutto automaticamente. Esegui:

```bash
cd /Volumes/Data/dev/archivio
chmod +x configure-mysql8-mamp.sh
./configure-mysql8-mamp.sh
```

## ⚠️ Importante: Backup Prima di Modificare

Prima di modificare i binari MySQL di MAMP, fai un backup:

```bash
# Backup binari MySQL MAMP
sudo cp -r /Applications/MAMP/Library/bin /Applications/MAMP/Library/bin_backup_$(date +%Y%m%d)
```

## 🔍 Verifica Configurazione

Dopo aver configurato, verifica:

```bash
# Verifica versione
/Applications/MAMP/Library/bin/mysql --version

# Test connessione
/Applications/MAMP/Library/bin/mysql -uroot -proot -hlocalhost -e "SELECT VERSION();"
```

## 📋 Passi Successivi

Dopo aver configurato MySQL 8.0:

1. **Riavvia MAMP** (Stop → Start)
2. **Verifica versione**: `mysql --version` dovrebbe mostrare 8.0.x
3. **Importa database** senza conversioni:

```bash
cd /Volumes/Data/dev/archivio
./import-db-production.sh backups/db/Swp1868342-prod.sql
```

## 🐛 Risoluzione Problemi

### Errore: "mysql: command not found"
- Verifica che il symlink sia corretto: `ls -la /Applications/MAMP/Library/bin/mysql`
- Verifica che il percorso MySQL 8.0 sia corretto

### Errore: "Can't connect to MySQL server"
- Verifica che MySQL 8.0 sia avviato
- Verifica le credenziali (root/root per MAMP)
- Verifica la porta (3306 di default)

### MAMP non si avvia
- Ripristina i binari originali dalla cartella backup
- Oppure reinstall MAMP

