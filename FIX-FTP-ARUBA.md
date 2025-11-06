# Risoluzione Problemi FTP Aruba

## Errore: "530 Errore critico: impossibile collegarsi al server"

Questo errore indica che il **filtro accessi FTP** è attivo sul tuo account Aruba.

### Soluzione 1: Configurare il filtro accessi FTP

1. **Accedi al pannello Aruba:**
   - Vai su https://admin.aruba.it
   - Effettua il login

2. **Vai alla sezione FTP:**
   - Cerca "Gestione Hosting" o "Hosting"
   - Clicca su "Accesso FTP" o "FTP"

3. **Configura il filtro accessi:**
   - Cerca "Filtro Accessi FTP" o "FTP Access Filter"
   - Aggiungi il tuo **IP pubblico** alla whitelist
   - Oppure disattiva temporaneamente il filtro per testare

4. **Come trovare il tuo IP pubblico:**
   ```bash
   curl ifconfig.me
   # oppure
   curl ipinfo.io/ip
   ```

### Soluzione 2: Disattivare temporaneamente il filtro

Se stai testando la connessione, puoi disattivare temporaneamente il filtro:
- Vai su pannello Aruba → Accesso FTP → Filtro Accessi
- Disattiva il filtro temporaneamente
- **Ricorda di riattivarlo dopo** per sicurezza

### Soluzione 3: Verificare le credenziali

1. **Verifica username:**
   - Deve essere nel formato: `username@aruba.it` o solo `username`
   - Controlla nel pannello Aruba quale formato è corretto

2. **Verifica password:**
   - Assicurati che non ci siano spazi prima/dopo
   - Prova a resettare la password FTP dal pannello

3. **Verifica hostname:**
   - Prova diversi hostname:
     - `ftp.archiviowebsite.com`
     - `archiviowebsite.com` (senza ftp.)
     - `ftp.aruba.it`
     - L'IP del server (se fornito)

### Soluzione 4: Testare con client FTP grafico

Prima di usare lo script, testa la connessione con un client grafico:

**FileZilla:**
1. Scarica FileZilla: https://filezilla-project.org/
2. File → Site Manager → New Site
3. Inserisci:
   - Host: `ftp.archiviowebsite.com`
   - Port: `21`
   - Protocol: `FTP - File Transfer Protocol`
   - Logon Type: `Normal`
   - User: `9329510@aruba.it`
   - Password: `Archivio_2023`
4. Clicca "Connect"

Se FileZilla funziona, lo script dovrebbe funzionare anche.

### Soluzione 5: Usare SFTP invece di FTP

Se FTP non funziona, prova SFTP (porta 22):

1. Modifica `sync-wp-ftp.sh`:
   ```bash
   REMOTE_FTP_PORT="22"
   ```

2. Lo script proverà automaticamente SFTP se FTP fallisce

### Verifica configurazione corretta

Dopo aver configurato il filtro accessi, testa la connessione:

```bash
./test-ftp-aruba.sh
```

Se il test funziona, puoi usare:

```bash
./sync-wp-ftp.sh pull --files-only
```

## Altri errori comuni

### "Connection refused" o "Connection timeout"

**Possibili cause:**
- Firewall che blocca la porta 21
- Porta errata
- Hostname errato

**Soluzioni:**
- Verifica che la porta sia 21 (o 22 per SFTP)
- Prova hostname alternativi
- Controlla il firewall del tuo router/computer

### "Permission denied" dopo la connessione

**Possibili cause:**
- Percorso errato
- Permessi insufficienti

**Soluzioni:**
- Verifica il percorso `REMOTE_FTP_PATH` nello script
- Connettiti con FileZilla e verifica il percorso corretto
- Controlla i permessi delle directory sul server

## Supporto Aruba

Se nessuna soluzione funziona:
- Contatta il supporto Aruba: https://assistenza.aruba.it
- Fornisci il messaggio di errore completo
- Indica che stai cercando di connetterti via FTP

