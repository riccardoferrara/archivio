# Guida Sincronizzazione WordPress con Aruba

Questo script permette di sincronizzare il tuo sito WordPress tra l'ambiente locale e il server Aruba in produzione.

## Prerequisiti

### 1. Accesso SSH su Aruba

#### Come verificare se hai accesso SSH

**Metodo 1: Pannello di controllo Aruba**
1. Accedi al pannello di controllo Aruba (https://admin.aruba.it)
2. Vai nella sezione **"Hosting"** o **"Gestione Hosting"**
3. Cerca la voce **"Accesso SSH"** o **"Shell Access"**
4. Se è presente e attiva, hai accesso SSH

**Metodo 2: Test da terminale**
Prova a connetterti direttamente:

```bash
# Sostituisci con i tuoi dati Aruba
ssh tuo-username@tuo-server.aruba.it
# oppure
ssh tuo-username@ftp.tuo-sito.com
```

Se la connessione funziona, vedrai un prompt come:
```
tuo-username@server:~$
```

**Metodo 3: Script di test automatico**
Usa lo script `test-ssh-aruba.sh` (vedi sotto) per un test automatico.

**Metodo 4: Verifica informazioni hosting**
- Controlla l'email di benvenuto di Aruba - spesso contiene informazioni su SSH
- Cerca nel pannello di controllo la sezione "Informazioni Server" o "Dettagli Hosting"
- Controlla la documentazione del tuo piano hosting

#### Se non hai accesso SSH:
- **Piani condivisi base**: Potrebbero non includere SSH
- **Piani VPS/Dedicati**: Includono sempre SSH
- **Contatta il supporto Aruba** per attivarlo o per informazioni sul tuo piano

### 2. Installazione wp-cli su Aruba
Se wp-cli non è già installato sul server Aruba, installalo così:

```bash
# Connettiti via SSH al server Aruba
ssh tuo-utente@tuo-server.aruba.it

# Installa wp-cli nella tua home directory
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
mkdir -p ~/bin
mv wp-cli.phar ~/bin/wp

# Aggiungi ~/bin al PATH (aggiungi questa riga a ~/.bashrc o ~/.bash_profile)
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc

# Verifica installazione
wp --version
```

### 3. Installazione wp-cli locale
Sul tuo Mac, installa wp-cli se non ce l'hai:

```bash
brew install wp-cli
# oppure
curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
chmod +x wp-cli.phar
sudo mv wp-cli.phar /usr/local/bin/wp
```

## Configurazione

Apri il file `sync-wp.sh` e modifica queste variabili nella sezione CONFIGURAZIONE:

```bash
# Server Aruba
REMOTE_HOST="tuo-server.aruba.it"  # o l'IP del server
REMOTE_USER="tuo-username-aruba"   # Il tuo username Aruba
REMOTE_WP_PATH="/home/tuo-username-aruba/public_html"  # Percorso WordPress
REMOTE_SSH_PORT="22"  # Solitamente 22 per Aruba

# URL del sito
REMOTE_URL="https://tuo-sito.com"
LOCAL_URL="http://localhost"  # o http://tuo-sito.local se usi MAMP/XAMPP

# Database locale (se diverso da quello nel wp-config.php)
LOCAL_DB_NAME="local"
LOCAL_DB_USER="root"
LOCAL_DB_PASS="root"
```

### Trovare il percorso WordPress su Aruba

Il percorso WordPress su Aruba è solitamente:
- **Dominio principale**: `/home/username/public_html`
- **Domini aggiuntivi**: `/home/username/nome-dominio.com`

Per verificare il percorso esatto:
```bash
ssh tuo-utente@tuo-server.aruba.it
pwd  # Mostra la directory corrente
cd public_html
pwd  # Mostra il percorso completo
```

## Utilizzo

### PULL: Scaricare da produzione a locale

```bash
./sync-wp.sh pull
```

Questo comando:
1. Crea un backup del database locale
2. Scarica il database da Aruba
3. Lo importa in locale
4. Sostituisce gli URL (da produzione a localhost)
5. Sincronizza i file (uploads, themes, plugins)

### PUSH: Caricare da locale a produzione

```bash
./sync-wp.sh push
```

⚠️ **ATTENZIONE**: Questo sovrascriverà i dati in produzione! Lo script chiederà conferma.

Questo comando:
1. Crea un backup del database remoto
2. Esporta il database locale
3. Sostituisce gli URL (da localhost a produzione)
4. Carica e importa il database su Aruba
5. Sincronizza i file verso Aruba

### Opzioni

```bash
# Sincronizza solo il database
./sync-wp.sh pull --db-only
./sync-wp.sh push --db-only

# Sincronizza solo i file
./sync-wp.sh pull --files-only
./sync-wp.sh push --files-only
```

## Troubleshooting

### Errore: "Impossibile connettersi via SSH"

**Possibili cause:**
1. SSH non è abilitato sul tuo piano Aruba
2. Credenziali errate
3. Firewall che blocca la porta SSH

**Soluzioni:**
- Contatta il supporto Aruba per verificare l'accesso SSH
- Verifica username e hostname corretti
- Prova a connetterti manualmente: `ssh username@server.aruba.it`

### Errore: "wp-cli non trovato sul server remoto"

**Soluzione:**
Installa wp-cli su Aruba seguendo le istruzioni nella sezione "Prerequisiti" sopra.

### Errore: "Permission denied"

**Possibili cause:**
1. Permessi insufficienti sulla directory WordPress
2. Utente SSH diverso dal proprietario dei file

**Soluzioni:**
```bash
# Su Aruba, verifica i permessi
ssh tuo-utente@server.aruba.it
cd /home/tuo-utente/public_html
ls -la

# Se necessario, correggi i permessi
chmod 755 wp-content
chmod -R 755 wp-content/uploads
```

### Errore durante la sincronizzazione dei file

**Possibili cause:**
1. `rsync` non installato
2. Percorsi errati

**Soluzioni:**
- Installa rsync: `brew install rsync` (su Mac)
- Verifica i percorsi in `REMOTE_WP_PATH` e `LOCAL_WP_PATH`

## Sicurezza

⚠️ **Importante:**
- Non committare mai il file `sync-wp.sh` con password o chiavi SSH nel repository
- Usa chiavi SSH invece delle password quando possibile
- Fai sempre backup prima di fare PUSH in produzione
- Testa sempre le modifiche in locale prima di pubblicarle

## Alternative se SSH non è disponibile

Se il tuo piano Aruba non include SSH, hai diverse opzioni:

### Opzione 1: Script FTP automatico (consigliato)

Usa lo script `sync-wp-ftp.sh` che sincronizza i file automaticamente via FTP:

```bash
# Installa lftp (se non ce l'hai)
brew install lftp

# Configura sync-wp-ftp.sh con le tue credenziali FTP Aruba
# Poi esegui:
./sync-wp-ftp.sh pull --files-only   # Scarica solo i file
./sync-wp-ftp.sh push --files-only   # Carica solo i file
```

**Per il database:**
- Lo script ti guiderà nell'esportazione/importazione manuale
- Puoi usare phpMyAdmin su Aruba per importare il database

### Opzione 2: Plugin WordPress

Installa uno di questi plugin sul sito WordPress:

**WP Migrate DB / WP Migrate DB Pro**
- Permette di esportare/importare database con sostituzione URL automatica
- Funziona anche senza SSH
- Download: https://wordpress.org/plugins/wp-migrate-db/

**Duplicator**
- Crea pacchetti completi del sito (file + database)
- Download: https://wordpress.org/plugins/duplicator/

**All-in-One WP Migration**
- Semplice da usare, esporta tutto il sito
- Download: https://wordpress.org/plugins/all-in-one-wp-migration/

### Opzione 3: Metodo manuale

**Per i file:**
1. Usa un client FTP come FileZilla, Cyberduck, o Transmit
2. Connettiti al server Aruba con le credenziali FTP
3. Sincronizza manualmente le cartelle `wp-content/uploads`, `wp-content/themes`, `wp-content/plugins`

**Per il database:**
1. **Esporta database locale:**
   ```bash
   wp db export backup.sql --path=/percorso/wordpress
   ```

2. **Accedi a phpMyAdmin su Aruba:**
   - Vai su https://admin.aruba.it
   - Cerca "phpMyAdmin" nel pannello di controllo
   - Oppure vai direttamente a: `https://tuo-sito.com/phpmyadmin`

3. **Importa il database:**
   - Seleziona il database WordPress
   - Vai su "Importa"
   - Carica il file `.sql` esportato
   - Prima di importare, sostituisci gli URL nel file SQL:
     ```bash
     sed -i.bak 's|http://localhost|https://tuo-sito.com|g' backup.sql
     ```

### Opzione 4: Richiedere SSH ad Aruba

Se possibile, contatta il supporto Aruba per attivare l'accesso SSH:
- Alcuni piani lo includono già
- Altri lo possono attivare su richiesta
- I piani VPS/Dedicati lo hanno sempre incluso

**Vantaggi SSH:**
- Sincronizzazione completamente automatica
- Accesso a wp-cli sul server
- Maggiore controllo e flessibilità

## Supporto

Per problemi specifici di Aruba, contatta il supporto tecnico Aruba:
- https://assistenza.aruba.it

