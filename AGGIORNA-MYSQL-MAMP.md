# Aggiorna MySQL in MAMP a 8.0

## ✅ Soluzione Semplice

Invece di convertire il file SQL, aggiorna MySQL locale a 8.0 per essere compatibile con il database di produzione.

## 🔧 Passi per Aggiornare MySQL in MAMP

### 1. Verifica Versione Attuale

```bash
/Applications/MAMP/Library/bin/mysql --version
```

### 2. Aggiorna MySQL in MAMP

**Opzione A - MAMP PRO (se hai la versione PRO):**

1. Apri **MAMP PRO**
2. Vai su **Preferences** → **MySQL**
3. Seleziona **MySQL 8.0** dal menu a tendina
4. Clicca **Apply** o **OK**
5. **Riavvia** i server MAMP

**Opzione B - MAMP Free (versione gratuita):**

MAMP Free include già MySQL 8.0, ma potrebbe essere configurato per usare MySQL 5.7.

1. Apri **MAMP**
2. Vai su **Preferences** (⚙️) → **Ports**
3. Verifica che MySQL sia sulla porta **3306** (default)
4. Se necessario, **reinstalla MAMP** o **aggiorna MAMP** all'ultima versione che include MySQL 8.0

**Opzione C - Download MySQL 8.0 Manualmente:**

1. **Scarica MySQL 8.0** da: https://dev.mysql.com/downloads/mysql/
2. **Installa** MySQL 8.0
3. **Configura MAMP** per usare MySQL 8.0 esterno:
   - MAMP → Preferences → MySQL
   - Seleziona "Use MySQL from system PATH" o specifica il percorso

### 3. Verifica Versione Aggiornata

```bash
/Applications/MAMP/Library/bin/mysql --version
```

Dovrebbe mostrare: `mysql  Ver 8.0.x`

### 4. Riavvia Database

1. **Ferma** MySQL in MAMP
2. **Avvia** di nuovo MySQL
3. Verifica che funzioni:

```bash
/Applications/MAMP/Library/bin/mysql -uroot -proot -hlocalhost -e "SELECT VERSION();"
```

### 5. Importa Database (Senza Conversioni!)

Ora puoi importare il database direttamente senza dover convertire nulla:

```bash
cd /Volumes/Data/dev/archivio
./import-db-production.sh backups/db/Swp1868342-prod.sql
```

Lo script ora dovrà solo sostituire gli URL, non le collation o la sintassi!

## ⚠️ Nota: Backup Database Esistente

Prima di aggiornare MySQL, fai un backup del database locale esistente:

```bash
# Backup database locale
/Applications/MAMP/Library/bin/mysqldump -uroot -proot -hlocalhost archivio_local > backups/db/backup_before_mysql8_upgrade_$(date +%Y%m%d_%H%M%S).sql
```

## 🎯 Vantaggi

- ✅ Nessuna conversione SQL necessaria
- ✅ Compatibilità totale con database produzione
- ✅ Supporto completo per sintassi MySQL 8.0
- ✅ Nessun problema con collation `utf8mb4_0900_ai_ci`
- ✅ Nessun problema con tipi numerici senza lunghezza

## 📋 Checklist

- [ ] Backup database locale esistente
- [ ] Aggiorna MySQL a 8.0 in MAMP
- [ ] Verifica versione MySQL
- [ ] Riavvia MySQL
- [ ] Importa database produzione (senza conversioni!)
- [ ] Verifica che tutto funzioni

