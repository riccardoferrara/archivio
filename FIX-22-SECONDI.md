# Fix: WordPress Lento (22 secondi prima richiesta)

## 🔴 Problema Identificato

Dalla diagnostica:
- ✅ WP_DEBUG disattivo (OK)
- ⚠️ **Cache NON attiva** (PROBLEMA PRINCIPALE)
- ✅ Transients OK
- ⚠️ Opzioni autoload e revisioni da verificare

## 🚀 Fix Immediato

### 1. Attiva Cache Object (Senza Plugin)

Aggiungi in `wp-config.php` **dopo** `DB_COLLATE`:

```php
/* Attiva cache object */
define( 'WP_CACHE', true );

/* Limita revisioni post per performance */
define( 'WP_POST_REVISIONS', 5 );

/* Disattiva autosave per performance locale */
define( 'AUTOSAVE_INTERVAL', 300 );
```

### 2. Pulisci Transients Scaduti

```bash
wp transient delete --expired --path="$(pwd)"
```

### 3. Ottimizza Database

```bash
make db-optimize
```

### 4. Riduci Opzioni Autoload

Se hai più di 500 opzioni autoload:

```bash
wp db query "UPDATE wp_options SET autoload = 'no' WHERE option_name LIKE '_transient_%' AND autoload = 'yes'" --path="$(pwd)"
```

## 📋 Modifica wp-config.php Manualmente

Apri `wp-config.php` e aggiungi **dopo la riga 38** (`DB_COLLATE`):

```php
define( 'DB_COLLATE', '' );

/* ============================================
 * OTTIMIZZAZIONI PERFORMANCE
 * ============================================ */

/* Attiva cache object */
define( 'WP_CACHE', true );

/* Limita revisioni post */
define( 'WP_POST_REVISIONS', 5 );

/* Disattiva autosave frequente (locale) */
define( 'AUTOSAVE_INTERVAL', 300 );

/* ============================================ */
```

## 🎯 Fix Automatico

Esegui:

```bash
./fix-immediato.sh
```

Questo applica automaticamente tutti i fix.

## 🔍 Cause Probabili dei 22 Secondi

### 1. **Nessuna Cache** (MOLTO PROBABILE) ⚠️
- Ogni richiesta ricarica tutto
- Query database ripetute
- **Fix**: Attiva `WP_CACHE = true`

### 2. **Query Database Multiple** ⚠️
- Plugin fanno troppe query
- Tema carica troppi dati
- **Fix**: Installa Query Monitor per vedere query lente

### 3. **Plugin Pesanti** ⚠️
- Security plugin
- Backup plugin
- SEO plugin con scansioni
- **Fix**: Disattiva plugin non necessari temporaneamente

### 4. **Font/Risorse Esterne** ⚠️
- Google Fonts lento
- API esterne
- **Fix**: Usa font locali, rimuovi risorse non necessarie

### 5. **Database Non Ottimizzato** ⚠️
- Tabelle frammentate
- **Fix**: `make db-optimize`

## 📊 Test Performance

Dopo i fix, testa:

```bash
# Test velocità
time curl -o /dev/null -s http://localhost:8000

# Dovrebbe essere < 2 secondi (anche senza cache plugin)
# Con cache plugin: < 0.5 secondi
```

## 🎯 Workflow Completo

```bash
# 1. Modifica wp-config.php (aggiungi cache)
# Vedi sopra

# 2. Pulisci transients
wp transient delete --expired --path="$(pwd)"

# 3. Ottimizza database
make db-optimize

# 4. Riavvia server
make server-stop
make server

# 5. Testa
time curl -o /dev/null -s http://localhost:8000
```

## 💡 Cache Plugin (Raccomandato)

Per performance migliori, installa un plugin cache:

### WP Super Cache (Gratuito)
1. Vai su Admin → Plugin → Aggiungi nuovo
2. Cerca "WP Super Cache"
3. Installa e attiva
4. Configura: Impostazioni → WP Super Cache → Caching ON

### W3 Total Cache (Gratuito)
- Più opzioni, più complesso
- Buono per siti avanzati

### WP Rocket (A Pagamento)
- Il migliore
- Setup automatico
- Performance eccellenti

## ✅ Checklist Fix

- [ ] `WP_CACHE = true` in wp-config.php
- [ ] `WP_POST_REVISIONS = 5` in wp-config.php
- [ ] Transients scaduti puliti
- [ ] Database ottimizzato
- [ ] Server riavviato
- [ ] Test velocità < 2 secondi
- [ ] Cache plugin installato (opzionale ma consigliato)

## 🎯 Risultati Attesi

**Prima**: 22 secondi ❌
**Dopo fix base**: 2-5 secondi ⚠️
**Dopo cache plugin**: < 1 secondo ✅

## 🔍 Debug Avanzato

Se ancora lento dopo i fix:

1. **Installa Query Monitor**:
   - Plugin → Aggiungi nuovo → "Query Monitor"
   - Vedi quante query e quali sono lente

2. **Disattiva Plugin**:
   - Disattiva uno alla volta
   - Testa velocità dopo ogni disattivazione
   - Identifica plugin lento

3. **Controlla Tema**:
   - Attiva tema default WordPress
   - Se veloce, problema nel tema

## 💡 Nota Importante

La **prima richiesta** è sempre più lenta perché:
- WordPress carica tutto
- Query iniziali
- Cache non ancora popolata

Le **richieste successive** dovrebbero essere molto più veloci.

Se anche le richieste successive sono lente, il problema è diverso (plugin, tema, query).

