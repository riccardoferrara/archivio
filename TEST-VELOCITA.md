# Test Velocità WordPress Locale

## ✅ Ottimizzazioni Applicate

Ho già aggiunto in `wp-config.php`:
- ✅ `WP_CACHE = true` (cache object attiva)
- ✅ `WP_POST_REVISIONS = 5` (limita revisioni)
- ✅ `AUTOSAVE_INTERVAL = 300` (disattiva autosave frequente)

## 🚀 Prossimi Passi

### 1. Riavvia Server

```bash
make server-stop
make server
```

### 2. Pulisci Database (senza wp-cli)

wp-cli ha problemi, usa SQL diretto:

```bash
make fix-db-sql
```

Questo:
- Pulisce transients scaduti
- Rimuove transients da autoload
- Ottimizza tabelle principali

### 3. Testa Velocità

```bash
# Test prima richiesta
time curl -o /dev/null -s http://localhost:8000

# Test richieste successive (dovrebbero essere più veloci)
time curl -o /dev/null -s http://localhost:8000
time curl -o /dev/null -s http://localhost:8000
```

## 📊 Risultati Attesi

**Prima**: 22 secondi ❌

**Dopo ottimizzazioni wp-config.php**: 2-5 secondi ⚠️
- Cache object attiva
- Revisioni limitate
- Autosave disattivato

**Dopo pulizia database**: 1-3 secondi ⚠️
- Transients puliti
- Database ottimizzato

**Con plugin cache**: < 1 secondo ✅
- WP Super Cache installato
- Cache HTML attiva

## 🔍 Se Ancora Lento

### Verifica Plugin

1. Vai su Admin → Plugin
2. Disattiva plugin uno alla volta
3. Testa velocità dopo ogni disattivazione
4. Identifica plugin lento

### Verifica Query

Installa Query Monitor:
1. Plugin → Aggiungi nuovo → "Query Monitor"
2. Vai su homepage
3. Controlla quante query e quali sono lente

### Verifica Tema

1. Attiva tema default WordPress
2. Se veloce → problema nel tema
3. Se lento → problema plugin/database

## 💡 Nota Importante

La **prima richiesta** è sempre più lenta perché:
- WordPress carica tutto
- Cache non ancora popolata
- Query iniziali

Le **richieste successive** dovrebbero essere molto più veloci.

## ✅ Checklist

- [ ] Server riavviato
- [ ] Database pulito (`make fix-db-sql`)
- [ ] Test velocità eseguito
- [ ] Prima richiesta < 5 secondi
- [ ] Richieste successive < 2 secondi
- [ ] Plugin cache installato (opzionale)

## 🎯 Comandi Rapidi

```bash
# Riavvia server
make server-stop && make server

# Pulisci database
make fix-db-sql

# Test velocità
time curl -o /dev/null -s http://localhost:8000

# Diagnostica
make diagnose-simple
```


