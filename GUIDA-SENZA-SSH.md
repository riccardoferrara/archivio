# Guida Sincronizzazione WordPress senza SSH

Questa guida ti mostra come sincronizzare WordPress tra locale e produzione quando **non hai accesso SSH** su Aruba.

## Metodo 1: Script FTP Automatico (Consigliato)

### Prerequisiti

1. **Installa lftp** (client FTP da linea di comando):
   ```bash
   # macOS
   brew install lftp
   
   # Linux
   sudo apt-get install lftp
   ```

2. **Ottieni le credenziali FTP da Aruba:**
   - Accedi al pannello Aruba (https://admin.aruba.it)
   - Vai in "Gestione Hosting" → "Accesso FTP"
   - Annota: hostname, username, password, porta (solitamente 21)

### Configurazione

1. Apri `sync-wp-ftp.sh` e modifica:
   ```bash
   REMOTE_FTP_HOST="ftp.tuo-sito.com"  # o ftp.aruba.it
   REMOTE_FTP_USER="tuo-username"
   REMOTE_FTP_PASS="tua-password"
   REMOTE_FTP_PORT="21"
   REMOTE_FTP_PATH="/public_html"  # Percorso dalla root FTP
   ```

2. Verifica il percorso FTP:
   - Connettiti con un client FTP (FileZilla, Cyberduck)
   - Trova la cartella dove si trova `wp-config.php`
   - Il percorso relativo dalla root FTP è quello da usare

### Utilizzo

```bash
# Scarica file da produzione
./sync-wp-ftp.sh pull --files-only

# Carica file in produzione
./sync-wp-ftp.sh push --files-only
```

**Per il database:** Lo script ti guiderà passo-passo nell'esportazione/importazione manuale.

---

## Metodo 2: Plugin WordPress

### WP Migrate DB (Gratuito)

1. **Installa il plugin** sul sito WordPress (locale e produzione):
   - Vai su WordPress Admin → Plugin → Aggiungi nuovo
   - Cerca "WP Migrate DB"
   - Installa e attiva

2. **Esporta database da produzione:**
   - Vai su Tools → Migrate DB
   - Clicca "Export"
   - Sostituisci gli URL se necessario
   - Scarica il file `.sql`

3. **Importa in locale:**
   - Apri il plugin sul sito locale
   - Vai su "Import"
   - Carica il file `.sql` scaricato
   - Il plugin sostituirà automaticamente gli URL

4. **Per i file:** Usa lo script FTP o sincronizza manualmente

### All-in-One WP Migration

1. **Installa il plugin** su entrambi i siti

2. **Esporta da produzione:**
   - Vai su All-in-One WP Migration → Export
   - Clicca "Export To" → "File"
   - Scarica il file `.wpress`

3. **Importa in locale:**
   - Vai su All-in-One WP Migration → Import
   - Carica il file `.wpress`
   - Attendi il completamento

**Vantaggi:** Include tutto (file + database) in un unico file.

---

## Metodo 3: Manuale con phpMyAdmin

### Passo 1: Esporta Database Locale

```bash
# Con wp-cli locale
wp db export backup.sql --path=/percorso/wordpress

# Oppure con phpMyAdmin locale
# Vai su http://localhost/phpmyadmin
# Seleziona database → Esporta → Go
```

### Passo 2: Modifica URL nel Database

Prima di importare, sostituisci gli URL:

```bash
# Da localhost a produzione
sed -i.bak 's|http://localhost|https://tuo-sito.com|g' backup.sql

# Oppure usa un editor di testo per cercare/sostituire
```

### Passo 3: Importa in phpMyAdmin Aruba

1. **Accedi a phpMyAdmin su Aruba:**
   - Vai su https://admin.aruba.it
   - Cerca "phpMyAdmin" o "Database"
   - Oppure: `https://tuo-sito.com/phpmyadmin` (se abilitato)

2. **Seleziona il database WordPress**

3. **Importa:**
   - Clicca su "Importa"
   - Scegli il file `backup.sql`
   - Clicca "Go"
   - Attendi il completamento

### Passo 4: Sincronizza File

Usa lo script FTP o un client FTP manuale per sincronizzare:
- `wp-content/uploads/`
- `wp-content/themes/`
- `wp-content/plugins/`

---

## Metodo 4: Client FTP Grafico

### FileZilla (Gratuito)

1. **Scarica FileZilla:** https://filezilla-project.org/

2. **Connetti ad Aruba:**
   - Host: `ftp.tuo-sito.com` (o quello fornito da Aruba)
   - Username: tuo username Aruba
   - Password: tua password Aruba
   - Porta: 21

3. **Sincronizza file:**
   - Naviga a `public_html/wp-content/` sul server
   - Trascina le cartelle `uploads`, `themes`, `plugins` dal locale al remoto (o viceversa)

### Cyberduck (Gratuito, macOS/Windows)

1. **Scarica Cyberduck:** https://cyberduck.io/

2. **Connetti:**
   - Clicca "Apri connessione"
   - Scegli "FTP"
   - Inserisci credenziali Aruba

3. **Sincronizza:** Usa la funzione "Sincronizza" per confrontare e aggiornare i file

---

## Confronto Metodi

| Metodo | File | Database | Automazione | Difficoltà |
|--------|------|----------|-------------|------------|
| **Script FTP** | ✅ Automatico | ⚠️ Manuale | ⭐⭐⭐ | Media |
| **WP Migrate DB** | ⚠️ Manuale | ✅ Automatico | ⭐⭐ | Facile |
| **All-in-One WP** | ✅ Automatico | ✅ Automatico | ⭐⭐⭐ | Facile |
| **phpMyAdmin** | ⚠️ Manuale | ⚠️ Manuale | ⭐ | Facile |
| **Client FTP** | ⚠️ Manuale | ❌ No | ⭐ | Facile |

---

## Troubleshooting

### Errore: "lftp: command not found"

**Soluzione:**
```bash
brew install lftp  # macOS
sudo apt-get install lftp  # Linux
```

### Errore: "Connection refused" o "Timeout"

**Possibili cause:**
- Porta FTP errata (prova 21, 22, o quella fornita da Aruba)
- Firewall che blocca la connessione
- Credenziali errate

**Soluzioni:**
- Verifica le credenziali nel pannello Aruba
- Prova a connetterti con un client FTP grafico per testare
- Contatta il supporto Aruba se il problema persiste

### File non si sincronizzano correttamente

**Soluzioni:**
- Verifica i permessi delle directory su Aruba (dovrebbero essere 755)
- Assicurati che il percorso FTP sia corretto
- Controlla che non ci siano file troppo grandi (limiti FTP)

### Database troppo grande per phpMyAdmin

**Soluzioni:**
1. Aumenta il limite in phpMyAdmin (se possibile)
2. Usa `wp-cli` locale per esportare/importare
3. Dividi il database in parti più piccole
4. Usa un plugin come "WP Migrate DB" che gestisce file grandi

---

## Suggerimenti Finali

1. **Sempre fare backup** prima di sincronizzare
2. **Testare in locale** prima di pubblicare in produzione
3. **Sostituire sempre gli URL** quando si sposta il database
4. **Sincronizzare i file prima del database** per evitare problemi di compatibilità
5. **Considerare di richiedere SSH** ad Aruba per una soluzione più robusta

---

## Supporto

- **Aruba Support:** https://assistenza.aruba.it
- **Documentazione wp-cli:** https://wp-cli.org/
- **FileZilla Guide:** https://wiki.filezilla-project.org/

