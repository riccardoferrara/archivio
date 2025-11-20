# 🧹 Pulizia Pagine Non Utilizzate - Impatto Performance

## ❓ Le Pagine Non Utilizzate Rallentano?

### ✅ **Dipende dalla Quantità e dal Database**

**NON rallentano se:**
- ✅ Hai poche centinaia di pagine/posts (< 500)
- ✅ Database è ottimizzato
- ✅ Pagine sono in draft/trash (non incluse nelle query pubbliche)

**SÌ, possono rallentare se:**
- ❌ Hai **migliaia** di pagine/posts (> 1000)
- ❌ Database non è ottimizzato
- ❌ Ci sono **revisioni accumulate** (ogni modifica crea una revisione)
- ❌ Ci sono **spam comments** (migliaia di commenti)
- ❌ Le query includono tutte le pagine (anche quelle non pubblicate)

---

## 🔍 Verifica Quante Pagine Hai

### Metodo 1: Via WordPress Admin

1. Vai su `WP Admin → Pagine`
2. Guarda il contatore in alto: "X pagine totali"
3. Controlla anche:
   - `WP Admin → Articoli` → "X articoli totali"
   - `WP Admin → Commenti` → "X commenti totali"

### Metodo 2: Via Database (più preciso)

```bash
# Conta pagine pubblicate
make db-query SQL="SELECT COUNT(*) as total FROM wp_posts WHERE post_type='page' AND post_status='publish'"

# Conta pagine draft/trash
make db-query SQL="SELECT COUNT(*) as total FROM wp_posts WHERE post_type='page' AND post_status IN ('draft', 'trash')"

# Conta TUTTE le pagine (incluse revisioni)
make db-query SQL="SELECT COUNT(*) as total FROM wp_posts WHERE post_type='page'"

# Conta revisioni (queste possono essere MOLTE!)
make db-query SQL="SELECT COUNT(*) as total FROM wp_posts WHERE post_type='revision'"
```

### Metodo 3: Verifica Dimensioni Database

```bash
# Mostra dimensioni tabelle
make db-info
```

Cerca la tabella `wp_posts` - se è molto grande (> 50MB), probabilmente hai troppe revisioni o pagine.

---

## 🚨 Problemi Comuni che Rallentano

### 1. **Revisioni Accumulate** (PROBLEMA #1)

**Problema:** Ogni volta che modifichi una pagina, WordPress crea una revisione. Dopo 100 modifiche, hai 100 revisioni per quella pagina!

**Impatto:** 
- Database cresce esponenzialmente
- Query più lente
- Backup più pesanti

**Verifica:**
```bash
make db-query SQL="SELECT COUNT(*) as revisioni FROM wp_posts WHERE post_type='revision'"
```

**Soluzione:**
- Limita revisioni in `wp-config.php` (vedi sotto)
- Pulisci revisioni vecchie (vedi sotto)

### 2. **Spam Comments**

**Problema:** Migliaia di commenti spam nel database.

**Verifica:**
```bash
make db-query SQL="SELECT COUNT(*) as spam FROM wp_comments WHERE comment_approved='spam'"
```

**Soluzione:**
- Pulisci spam comments (vedi sotto)

### 3. **Post Draft/Trash Non Eliminati**

**Problema:** Pagine in draft o trash che occupano spazio.

**Verifica:**
```bash
make db-query SQL="SELECT COUNT(*) as draft FROM wp_posts WHERE post_status IN ('draft', 'trash')"
```

**Soluzione:**
- Elimina definitivamente pagine in trash (vedi sotto)

---

## ✅ SOLUZIONI

### Soluzione 1: Limita Revisioni (PREVENZIONE)

Aggiungi in `wp-config.php` (prima di `/* That's all, stop editing! */`):

```php
// Limita revisioni a 3 (invece di infinite)
define('WP_POST_REVISIONS', 3);

// Oppure disabilita completamente (non consigliato)
// define('WP_POST_REVISIONS', false);
```

**Dopo aver aggiunto questo:**
- Le nuove modifiche creeranno max 3 revisioni
- Le revisioni vecchie rimangono (vanno pulite manualmente)

---

### Soluzione 2: Pulisci Revisioni Vecchie (PULIZIA)

**Opzione A - Plugin (CONSIGLIATO - più sicuro):**

1. Installa **WP-Optimize** (gratuito):
   - `WP Admin → Plugin → Aggiungi nuovo → cerca "WP-Optimize"`
   - Installa e attiva

2. Vai su `WP Admin → WP-Optimize → Database`
3. Seleziona:
   - ✅ **Clean all post revisions**
   - ✅ **Clean all auto-drafts**
   - ✅ **Clean all trashed posts**
   - ✅ **Clean all spam comments**
4. Click **"Run optimization"**

**Opzione B - Via Database (MANUALE - più rischioso):**

⚠️ **Fai backup prima!**

```bash
# Backup database
make backup

# Elimina revisioni vecchie (mantiene solo le ultime 3)
make db-query SQL="DELETE FROM wp_posts WHERE post_type='revision' AND post_date < DATE_SUB(NOW(), INTERVAL 30 DAY)"

# Elimina auto-drafts vecchi
make db-query SQL="DELETE FROM wp_posts WHERE post_status='auto-draft' AND post_date < DATE_SUB(NOW(), INTERVAL 7 DAY)"

# Elimina definitivamente post in trash
make db-query SQL="DELETE FROM wp_posts WHERE post_status='trash'"

# Elimina spam comments
make db-query SQL="DELETE FROM wp_comments WHERE comment_approved='spam'"
```

---

### Soluzione 3: Elimina Pagine Non Utilizzate

**Metodo Sicuro (via WordPress Admin):**

1. Vai su `WP Admin → Pagine`
2. Filtra per:
   - **Stato:** "Cestino" → Elimina definitivamente
   - **Stato:** "Bozza" → Se non servono, elimina
3. Seleziona tutte → "Elimina definitivamente"

**⚠️ ATTENZIONE:** 
- Verifica prima che non servano!
- Fai backup prima di eliminare in massa

---

### Soluzione 4: Ottimizza Database (DOPO PULIZIA)

Dopo aver pulito, ottimizza il database:

```bash
# Ottimizza database locale
make db-optimize
```

Questo:
- Riorganizza le tabelle
- Recupera spazio
- Migliora performance query

**Per produzione (Aruba):**
- Usa phpMyAdmin → Seleziona tabelle → "Ottimizza tabella"
- Oppure plugin WP-Optimize

---

## 📊 Verifica Risultati

### Prima della Pulizia

```bash
# Dimensioni database
make db-info

# Conta revisioni
make db-query SQL="SELECT COUNT(*) as revisioni FROM wp_posts WHERE post_type='revision'"
```

### Dopo la Pulizia

```bash
# Dimensioni database (dovrebbe essere più piccolo)
make db-info

# Ottimizza
make db-optimize

# Verifica dimensioni dopo ottimizzazione
make db-info
```

---

## 🎯 Quando Preoccuparsi

**NON preoccuparti se:**
- ✅ Hai < 500 pagine/posts
- ✅ Database < 50MB
- ✅ Revisioni < 1000

**Preoccupati se:**
- ❌ Hai > 2000 pagine/posts
- ❌ Database > 200MB
- ❌ Revisioni > 5000
- ❌ Spam comments > 1000

---

## 📋 Checklist Pulizia

- [ ] ✅ Verificato quante pagine/posts hai
- [ ] ✅ Verificato quante revisioni hai
- [ ] ✅ Backup database fatto
- [ ] ✅ Limite revisioni aggiunto in `wp-config.php`
- [ ] ✅ Revisioni vecchie pulite (WP-Optimize)
- [ ] ✅ Spam comments eliminati
- [ ] ✅ Pagine in trash eliminate
- [ ] ✅ Database ottimizzato (`make db-optimize`)
- [ ] ✅ Test performance dopo pulizia

---

## 🔗 Plugin Consigliati

1. **WP-Optimize** (gratuito)
   - Pulisce revisioni, spam, ottimizza database
   - https://wordpress.org/plugins/wp-optimize/

2. **Advanced Database Cleaner** (gratuito)
   - Pulizia avanzata database
   - https://wordpress.org/plugins/advanced-database-cleaner/

3. **WP-Sweep** (gratuito)
   - Pulisce database in modo sicuro
   - https://wordpress.org/plugins/wp-sweep/

---

## ⚠️ ATTENZIONI

1. **Sempre backup prima di pulire:**
   ```bash
   make backup
   ```

2. **Non eliminare tutto in una volta:**
   - Pulisci gradualmente
   - Testa dopo ogni pulizia

3. **Revisioni possono essere utili:**
   - Non eliminarle tutte se ti servono per recuperare versioni vecchie
   - Limita solo le nuove (con `WP_POST_REVISIONS`)

4. **Produzione vs Locale:**
   - `make db-optimize` opera sul database **locale**
   - Per produzione, usa phpMyAdmin o plugin

---

## 🎯 Conclusione

**Le pagine non utilizzate possono rallentare se:**
- Sono MIGLIAIA
- Il database non è ottimizzato
- Ci sono revisioni accumulate

**Soluzione:**
1. Limita revisioni future (`wp-config.php`)
2. Pulisci revisioni vecchie (WP-Optimize)
3. Elimina pagine in trash
4. Ottimizza database

**Tempo stimato:** 15-30 minuti  
**Miglioramento atteso:** Database più piccolo, query più veloci, backup più rapidi


