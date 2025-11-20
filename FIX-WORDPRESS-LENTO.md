# Fix: WordPress Lento (22 secondi prima richiesta)

## 🔴 Problema

La prima richiesta alla homepage impiega **22 secondi** - estremamente lento!

## 🔍 Diagnostica

Esegui la diagnostica automatica:

```bash
make diagnose-slow
```

Questo analizza:
- Query database lente
- Plugin attivi
- Transients scaduti
- Debug mode
- Cache
- Dimensioni database

## 🎯 Cause Comuni (in ordine di probabilità)

### 1. **Transients Scaduti** (MOLTO COMUNE) ⚠️

**Problema**: WordPress cerca di pulire migliaia di transients scaduti ad ogni richiesta.

**Fix**:
```bash
# Pulisci transients scaduti
wp transient delete --expired --path="$(pwd)"

# Oppure via SQL
wp db query "DELETE FROM wp_options WHERE option_name LIKE '_transient_timeout_%' AND option_value < UNIX_TIMESTAMP()" --path="$(pwd)"
```

### 2. **Debug Mode Attivo** ⚠️

**Problema**: `WP_DEBUG` attivo rallenta tutto.

**Fix**: In `wp-config.php`:
```php
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('SAVEQUERIES', false);
```

### 3. **Troppe Query Database** ⚠️

**Problema**: Plugin o tema fanno troppe query.

**Diagnostica**:
```bash
make diagnose-slow
# Controlla "Query eseguite" e "Query lente"
```

**Fix**:
- Disattiva plugin non necessari
- Usa Query Monitor plugin per vedere query lente
- Attiva cache

### 4. **Opzioni Autoload Eccessive** ⚠️

**Problema**: Troppe opzioni caricate ad ogni richiesta.

**Fix**:
```bash
# Vedi quante opzioni autoload
wp db query "SELECT COUNT(*) FROM wp_options WHERE autoload = 'yes'" --path="$(pwd)"

# Se > 500, riduci
wp db query "UPDATE wp_options SET autoload = 'no' WHERE option_name LIKE '_transient_%' AND autoload = 'yes'" --path="$(pwd)"
```

### 5. **Revisioni Post Eccessive** ⚠️

**Problema**: Migliaia di revisioni post rallentano query.

**Fix**:
```bash
# Limita revisioni future
wp config set WP_POST_REVISIONS 5 --path="$(pwd)"

# Elimina revisioni vecchie (ATTENZIONE: backup prima!)
wp db query "DELETE FROM wp_posts WHERE post_type = 'revision' AND post_date < DATE_SUB(NOW(), INTERVAL 30 DAY)" --path="$(pwd)"
```

### 6. **Plugin Pesanti** ⚠️

**Problema**: Alcuni plugin rallentano molto.

**Diagnostica**:
- Disattiva plugin uno alla volta
- Testa velocità dopo ogni disattivazione
- Plugin comuni che rallentano:
  - Security plugin (Wordfence, iThemes)
  - Backup plugin
  - SEO plugin con scansioni
  - Analytics/tracking plugin

### 7. **Nessuna Cache** ⚠️

**Problema**: Ogni richiesta ricarica tutto.

**Fix**: Installa plugin cache:
- WP Super Cache (gratuito)
- W3 Total Cache (gratuito)
- WP Rocket (a pagamento, migliore)

### 8. **Database Non Ottimizzato** ⚠️

**Problema**: Tabelle frammentate.

**Fix**:
```bash
make db-optimize
```

### 9. **Font/Risorse Esterne** ⚠️

**Problema**: Google Fonts o altre risorse esterne lente.

**Fix**:
- Usa font locali
- Rimuovi risorse esterne non necessarie
- Usa CDN

## 🚀 Fix Automatico

Esegui il fix automatico:

```bash
make fix-slow
```

Questo applica:
- ✅ Pulisce transients scaduti
- ✅ Ottimizza database
- ✅ Limita revisioni
- ✅ Verifica debug mode

## 📊 Workflow Completo

### 1. Diagnostica

```bash
make diagnose-slow
```

### 2. Fix Automatico

```bash
make fix-slow
```

### 3. Fix Manuali

#### Disattiva Debug

In `wp-config.php`:
```php
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('SAVEQUERIES', false);
```

#### Pulisci Transients

```bash
wp transient delete --expired --path="$(pwd)"
```

#### Limita Revisioni

```bash
wp config set WP_POST_REVISIONS 5 --path="$(pwd)"
```

#### Ottimizza Database

```bash
make db-optimize
```

#### Attiva Cache

Installa WP Super Cache o W3 Total Cache.

### 4. Test Performance

```bash
# Test velocità
time curl -o /dev/null -s http://localhost:8000

# Dovrebbe essere < 1 secondo dopo i fix
```

## 🎯 Fix Rapido (Copia e Incolla)

```bash
# 1. Diagnostica
make diagnose-slow

# 2. Fix automatico
make fix-slow

# 3. Disattiva debug (modifica wp-config.php manualmente)
# define('WP_DEBUG', false);

# 4. Pulisci transients
wp transient delete --expired --path="$(pwd)"

# 5. Ottimizza database
make db-optimize

# 6. Test
time curl -o /dev/null -s http://localhost:8000
```

## 📈 Risultati Attesi

**Prima**: 22 secondi ❌
**Dopo fix**: < 1 secondo ✅

## 🔍 Debug Avanzato

### Query Monitor Plugin

Installa Query Monitor per vedere query lente:
1. Installa plugin "Query Monitor"
2. Vai su homepage
3. Controlla quante query e quali sono lente

### Profiling PHP

Aggiungi in `wp-config.php`:
```php
define('SAVEQUERIES', true);
```

Poi in `diagnose-slow.php` vedrai tutte le query.

## 💡 Prevenzione

1. **Limita revisioni**: `WP_POST_REVISIONS = 5`
2. **Disattiva debug**: `WP_DEBUG = false`
3. **Pulisci transients**: Periodicamente
4. **Ottimizza database**: Mensilmente
5. **Usa cache**: Sempre attiva
6. **Monitora plugin**: Disattiva quelli non necessari

## ✅ Checklist

- [ ] Debug mode disattivato
- [ ] Transients scaduti puliti
- [ ] Revisioni limitate
- [ ] Database ottimizzato
- [ ] Plugin non necessari disattivati
- [ ] Cache attiva
- [ ] Opzioni autoload ridotte
- [ ] Test velocità < 1 secondo

## 🎯 Prossimi Passi

1. **Esegui diagnostica**: `make diagnose-slow`
2. **Applica fix**: `make fix-slow`
3. **Testa velocità**: Dovrebbe essere molto più veloce!
4. **Se ancora lento**: Controlla plugin specifici

Esegui `make diagnose-slow` e condividi i risultati per un'analisi più precisa!


