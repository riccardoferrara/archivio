# Import Database con Password MySQL 8.0

## 🔐 Password MySQL 8.0

Se MySQL 8.0 installato esternamente richiede una password, puoi specificarla in due modi:

### Opzione 1: Variabile d'Ambiente (Consigliato)

```bash
# Imposta la password come variabile d'ambiente
export MYSQL_PASSWORD="tua_password_qui"

# Poi esegui lo script
cd /Volumes/Data/dev/archivio
./import-db-production.sh backups/db/Swp1868342-prod.sql
```

### Opzione 2: Inline nel Comando

```bash
cd /Volumes/Data/dev/archivio
MYSQL_PASSWORD="tua_password_qui" ./import-db-production.sh backups/db/Swp1868342-prod.sql
```

### Opzione 3: Modifica Script (Permanente)

Se vuoi impostare la password permanentemente nello script:

```bash
# Modifica import-db-production.sh
# Cambia la riga:
DB_PASS="root"
# Con:
DB_PASS="tua_password_qui"
```

## 🔍 Verifica Password Corretta

Prima di importare, verifica che la password funzioni:

```bash
# Test connessione
/Applications/MAMP/Library/bin/mysql -uroot -p"tua_password" -hlocalhost -e "SELECT VERSION();"
```

Se funziona, vedrai la versione di MySQL. Se non funziona, vedrai un errore di accesso negato.

## 📋 Password Comuni MySQL 8.0

- **MAMP default**: `root` (senza password o con password "root")
- **Homebrew MySQL 8.0**: spesso **senza password** (vuota) o password impostata durante installazione
- **MySQL installer ufficiale**: password impostata durante installazione

## 🔧 Se Non Ricordi la Password

### Reset Password MySQL 8.0

Se hai dimenticato la password:

```bash
# 1. Ferma MySQL
brew services stop mysql@8.0
# oppure
sudo /usr/local/mysql/support-files/mysql.server stop

# 2. Avvia MySQL in modalità sicura (skip-grant-tables)
mysqld_safe --skip-grant-tables &

# 3. Connettiti senza password
mysql -u root

# 4. Nel prompt MySQL:
ALTER USER 'root'@'localhost' IDENTIFIED BY 'nuova_password';
FLUSH PRIVILEGES;
exit;

# 5. Riavvia MySQL normalmente
```

### Oppure: Usa MySQL di MAMP

Se MySQL 8.0 esterno dà problemi, puoi:
1. Usare MySQL di MAMP (anche se 5.7, lo script convertirà automaticamente)
2. Oppure reinstallare MySQL 8.0 con password conosciuta

## ✅ Dopo Aver Impostato la Password

```bash
# Verifica che funzioni
export MYSQL_PASSWORD="tua_password"
/Applications/MAMP/Library/bin/mysql -uroot -p"$MYSQL_PASSWORD" -hlocalhost -e "SELECT VERSION();"

# Se funziona, importa database
cd /Volumes/Data/dev/archivio
./import-db-production.sh backups/db/Swp1868342-prod.sql
```

