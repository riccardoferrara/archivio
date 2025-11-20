# Analisi Performance Sito WordPress

## Comandi Disponibili

```bash
# Test rapido tempi di risposta
make perf-simple

# Analisi completa
make perf-check
```

## Possibili Cause di Lentezza

### 1. **Plugin Troppo Pesanti**
**Come verificare:**
- Vai su `wp-admin` → Plugin
- Disattiva temporaneamente i plugin uno alla volta e testa la velocità
- Plugin comuni che rallentano:
  - Security plugin (Wordfence, iThemes Security)
  - Backup plugin
  - SEO plugin con scansioni continue
  - Slider/Carousel plugin

**Soluzione:**
- Mantieni solo plugin essenziali
- Usa alternative più leggere

### 2. **Immagini Non Ottimizzate**
**Come verificare:**
```bash
# Trova immagini grandi (>5MB)
make img-find-large

# Report completo dimensioni immagini
make img-report
```

**Problema trovato:**
- **37 immagini sopra 5MB** (fino a 17MB!)
- Queste immagini rallentano drasticamente il caricamento del sito

**Soluzione:**
```bash
# Ottimizza automaticamente le immagini (>1MB)
make img-optimize

# Oppure con conferma automatica
make img-optimize FORCE=yes
```

**Note:**
- Le immagini verranno compresse con qualità 80% (JPG) o ottimizzate (PNG)
- Le prime 50 immagini >1MB verranno processate
- Dopo l'ottimizzazione, esegui `make img-report` per vedere i risultati
- Considera anche:
  - Convertire in formato WebP (più efficiente)
  - Abilitare lazy loading
  - Usare plugin come Smush o ShortPixel per ottimizzazione automatica

### 3. **Database Non Ottimizzato**
**Come verificare:**
```bash
make db-info
```

**Soluzione:**
```bash
# Ottimizza database
make db-optimize

# Verifica tabelle corrotte
make db-repair
```

### 4. **Nessuna Cache Attiva**
**Come verificare:**
```bash
curl -I https://www.archiviowebsite.com/ | grep -i cache
```

**Soluzione:**
- Installa plugin cache: WP Super Cache, W3 Total Cache, o WP Rocket
- Abilita cache del server (se disponibile su Aruba)

### 5. **Query Database Lente**
**Come verificare:**
- Installa plugin Query Monitor
- Controlla query lente nel debug log

**Soluzione:**
- Aggiungi indici alle tabelle
- Rimuovi query ridondanti
- Usa transients per cache dati

### 6. **Tema Non Ottimizzato**
**Come verificare:**
- Controlla se il tema carica troppi file JS/CSS
- Verifica se ci sono font esterni pesanti

**Soluzione:**
- Minifica JS e CSS
- Carica solo risorse necessarie
- Usa font locali invece di Google Fonts

### 7. **Hosting Lento (Aruba Shared)**
**Problemi comuni:**
- Hosting condiviso = risorse limitate
- Server lontano geograficamente
- Nessun CDN

**Soluzione:**
- Considera upgrade a VPS o hosting dedicato
- Usa CDN (Cloudflare gratuito)
- Scegli datacenter più vicino agli utenti

### 8. **Troppe Richieste HTTP**
**Come verificare:**
- Apri DevTools (F12) → Network
- Conta quante richieste vengono fatte
- Verifica dimensioni totali

**Soluzione:**
- Combina file JS/CSS
- Usa sprite per icone
- Minimizza richieste esterne

### 9. **🚨 RENDER BLOCKING RESOURCES (PROBLEMA CRITICO)**

**Problema Rilevato da Lighthouse:**
- **483.1 KiB** di risorse che bloccano il rendering
- **Est. savings: 3,050 ms** (oltre 3 secondi!)
- Blocca First Contentful Paint (FCP) e Largest Contentful Paint (LCP)

#### File CSS/JS che Bloccano il Rendering

**CSS Principali da Ottimizzare:**
- `eae.min.css` (38.9 KiB) - 600ms
- `dashicons.min.css` (35 KiB) - 900ms
- `main.min.css` (34.1 KiB)
- `custom-pro-frontend.min.css` (40.3 KiB)
- `woolentor-widgets.css` (21 KiB)
- Multiple icon libraries (FontAwesome, Ionicons, Elegant Icons, etc.)

**JavaScript Bloccanti:**
- `jquery.min.js` (29.7 KiB)
- `plupload` e `moxie` (32.3 KiB totale)
- `photoswipe.min.js` (11.8 KiB)

**CDN Esterni Lenti:**
- FontAwesome CDN: 32.3 KiB, 2,430ms delay
- Google Fonts: 1.6 KiB, 1,050ms delay

#### 🔧 SOLUZIONI IMMEDIATE

##### Soluzione 1: Plugin WP Rocket (CONSIGLIATO)
WP Rocket è il modo più semplice e completo per risolvere questo problema.

**Installazione:**
1. Scarica WP Rocket (plugin premium, €49/anno)
2. Installa e attiva
3. Configura le seguenti opzioni:

**Configurazione WP Rocket:**
```
Cache → ✅ Enable caching for mobile devices
Cache → ✅ Enable caching for logged-in users

File Optimization:
  CSS Files → ✅ Minify CSS files
  CSS Files → ✅ Combine CSS files
  CSS Files → ✅ Optimize CSS delivery (Critical CSS)
  
  JavaScript Files → ✅ Minify JavaScript files
  JavaScript Files → ✅ Combine JavaScript files
  JavaScript Files → ✅ Load JavaScript deferred
  JavaScript Files → ✅ Delay JavaScript execution

Media:
  LazyLoad → ✅ Enable for images
  LazyLoad → ✅ Enable for iframes and videos
  
Advanced:
  Optimize Google Fonts → ✅ Host fonts locally
  Remove unused CSS → ✅ Enable
```

##### Soluzione 2: Autoptimize + Asset CleanUp (GRATIS)

**Plugin da installare:**
1. Autoptimize (ottimizzazione CSS/JS)
2. Asset CleanUp: Page Speed Booster (rimuovi CSS/JS non necessari)

**Configurazione Autoptimize:**
```
JavaScript Options:
  ✅ Optimize JavaScript Code
  ✅ Aggregate JS-files
  ✅ Also aggregate inline JS
  
CSS Options:
  ✅ Optimize CSS Code
  ✅ Aggregate CSS-files
  ✅ Also aggregate inline CSS
  ✅ Generate data: URIs for images
  ✅ Inline all CSS (per critical CSS)
  
Misc Options:
  ✅ Optimize HTML Code
  ✅ Save aggregated script/css as static files
```

**Configurazione Asset CleanUp:**
1. Vai su homepage
2. Aggiungi `?wpacu_list` all'URL (es: archiviowebsite.com/?wpacu_list)
3. Disabilita CSS/JS non necessari sulla homepage:
   - Dashicons (non serve nel frontend)
   - Media Views CSS (serve solo in admin)
   - Elegant Icons (se non usati)
   - Ionicons (se non usati)
   - Linear Icons (se non usati)
   - Dripicons (se non usati)

##### Soluzione 3: Ottimizzare Font (FONDAMENTALE)

**Problema:** FontAwesome e Google Fonts caricati da CDN esterni rallentano il sito.

**Soluzione A - Plugin OMGF (Optimize My Google Fonts):**
```bash
# Installa via WP CLI (se disponibile)
wp plugin install host-webfonts-local --activate

# Oppure installa manualmente da:
# https://wordpress.org/plugins/host-webfonts-local/
```

**Configurazione OMGF:**
- ✅ Host fonts locally
- ✅ Remove Google Fonts
- Font Display: swap

**Soluzione B - FontAwesome Locale:**
1. Scarica FontAwesome 5.15.3 (versione usata nel sito)
2. Carica nella cartella `/wp-content/themes/valeska-child/fonts/`
3. Aggiungi in `functions.php`:

```php
// Rimuovi FontAwesome CDN
function remove_fontawesome_cdn() {
    wp_dequeue_style('fontawesome-cdn');
    wp_deregister_style('fontawesome-cdn');
}
add_action('wp_enqueue_scripts', 'remove_fontawesome_cdn', 100);

// Carica FontAwesome locale
function load_local_fontawesome() {
    wp_enqueue_style('fontawesome-local', 
        get_stylesheet_directory_uri() . '/fonts/fontawesome/css/all.min.css', 
        array(), 
        '5.15.3'
    );
}
add_action('wp_enqueue_scripts', 'load_local_fontawesome');
```

##### Soluzione 4: Defer JavaScript Non Critico

Se non usi plugin, aggiungi in `functions.php`:

```php
// Defer JavaScript (escludi jQuery se necessario)
function defer_parsing_of_js($url) {
    if (is_admin()) return $url;
    if (FALSE === strpos($url, '.js')) return $url;
    if (strpos($url, 'jquery.min.js')) return $url; // jQuery serve subito
    return str_replace(' src', ' defer src', $url);
}
add_filter('script_loader_tag', 'defer_parsing_of_js', 10);
```

##### Soluzione 5: Critical CSS Inline

**Genera Critical CSS:**
1. Vai su https://jonassebastianohlsson.com/criticalpathcssgenerator/
2. Inserisci URL: https://www.archiviowebsite.com/
3. Copia il CSS generato
4. Aggiungi in `functions.php`:

```php
function inline_critical_css() {
    if (is_front_page()) {
        echo '<style id="critical-css">';
        // Incolla qui il CSS critico generato
        echo '</style>';
    }
}
add_action('wp_head', 'inline_critical_css', 1);
```

#### 📊 PRIORITÀ DI INTERVENTO

**ALTA PRIORITÀ (fai subito):**
1. ✅ Installa WP Rocket o Autoptimize
2. ✅ Host Google Fonts localmente (OMGF plugin)
3. ✅ Defer JavaScript non critico
4. ✅ Rimuovi librerie icone non utilizzate (Asset CleanUp)

**MEDIA PRIORITÀ:**
5. ⏺ Host FontAwesome localmente
6. ⏺ Critical CSS inline
7. ⏺ Combina e minifica CSS/JS

**BASSA PRIORITÀ:**
8. ⚪ Rimuovi plugin pesanti non essenziali
9. ⚪ Ottimizza query database

#### 🎯 RISULTATI ATTESI

Dopo l'ottimizzazione dovresti vedere:
- **FCP (First Contentful Paint):** < 1.8s (da ~5s)
- **LCP (Largest Contentful Paint):** < 2.5s (da ~6s)
- **Render blocking time:** < 500ms (da 3,050ms)
- **PageSpeed Score:** > 85 (mobile), > 90 (desktop)

#### 🧪 TEST DOPO OTTIMIZZAZIONE

```bash
# Test velocità
make perf-check

# Oppure usa Lighthouse
# 1. Apri Chrome DevTools (F12)
# 2. Vai su tab "Lighthouse"
# 3. Seleziona "Performance"
# 4. Click "Analyze page load"
```

#### 🔗 RISORSE UTILI

- WP Rocket: https://wp-rocket.me/
- Autoptimize: https://wordpress.org/plugins/autoptimize/
- Asset CleanUp: https://wordpress.org/plugins/wp-asset-clean-up/
- OMGF: https://wordpress.org/plugins/host-webfonts-local/
- Critical CSS Generator: https://jonassebastianohlsson.com/criticalpathcssgenerator/

## Checklist Ottimizzazione

**Performance Critica:**
- [ ] 🔴 **Render blocking risolto** (WP Rocket/Autoptimize)
- [ ] 🔴 **Font hostati localmente** (Google Fonts + FontAwesome)
- [ ] 🔴 **JavaScript deferred** (defer/async)
- [ ] 🔴 **CSS/JS non utilizzati rimossi** (Asset CleanUp)

**Ottimizzazioni Base:**
- [ ] Cache attiva (plugin o server)
- [ ] Immagini ottimizzate e compresse
- [ ] Database ottimizzato
- [ ] Plugin non necessari disattivati
- [ ] JS/CSS minificati e combinati
- [ ] Lazy loading immagini
- [ ] CDN configurato
- [ ] Gzip compression attiva
- [ ] Query database ottimizzate
- [ ] Tema performante

**Ottimizzazioni Avanzate:**
- [ ] Critical CSS inline
- [ ] Preload risorse chiave
- [ ] HTTP/2 abilitato
- [ ] Browser caching configurato

## Strumenti di Test Online

1. **PageSpeed Insights**: https://pagespeed.web.dev/
2. **GTmetrix**: https://gtmetrix.com/
3. **WebPageTest**: https://www.webpagetest.org/
4. **Pingdom**: https://tools.pingdom.com/

## Comandi Utili

```bash
# Verifica dimensioni database
make db-info

# Ottimizza database
make db-optimize

# Test performance rapido
make perf-simple

# Analisi completa
make perf-check

# Trova immagini grandi
make img-find-large

# Report immagini
make img-report

# Ottimizza immagini
make img-optimize
```

## Note Specifiche per Aruba

- Aruba hosting condiviso può essere lento
- Verifica se hai accesso a cache del server
- Considera upgrade a hosting più performante
- Usa Cloudflare CDN (gratuito) per migliorare velocità

