# 🚀 Guida Rapida: Fix Render Blocking Resources

**Problema:** Homepage lenta con 3+ secondi di render blocking (483 KiB di risorse)

**Obiettivo:** Ridurre il render blocking time da 3,050ms a < 500ms

---

## ⚡ SOLUZIONE RAPIDA (30 minuti)

### Step 1: Installa Plugin di Ottimizzazione

Scegli **UNA** delle seguenti opzioni:

#### 🏆 OPZIONE A: WP Rocket (Più Facile - Premium)

```bash
# 1. Acquista WP Rocket da: https://wp-rocket.me/ (€49/anno)
# 2. Scarica il file .zip
# 3. Vai su wp-admin → Plugin → Aggiungi nuovo → Carica plugin
# 4. Carica e attiva WP Rocket
```

**Configurazione WP Rocket (3 minuti):**

1. Vai su `Settings → WP Rocket`

2. **Tab "Cache":**
   - ✅ Enable caching for mobile devices
   - ✅ Enable caching for logged-in WordPress users

3. **Tab "File Optimization":**
   - **CSS Files:**
     - ✅ Minify CSS files
     - ✅ Combine CSS files
     - ✅ Optimize CSS delivery
   
   - **JavaScript Files:**
     - ✅ Minify JavaScript files
     - ✅ Load JavaScript deferred
     - ✅ Delay JavaScript execution
     - Delay timeout: 0
   
4. **Tab "Media":**
   - ✅ Enable for images
   - ✅ Enable for iframes and videos
   - ✅ Replace YouTube iframe with preview image

5. **Tab "Advanced Rules":**
   - **Optimize Google Fonts:**
     - ✅ Combine Google Fonts
     - ✅ Host fonts locally
     - Font Display: swap

6. Click "Save Changes"

7. **Importante:** Vai su `Tools → Clear Cache` ogni volta che fai modifiche

#### 💚 OPZIONE B: Autoptimize + Asset CleanUp (Gratis)

```bash
# Installa via SSH (se disponibile WP-CLI)
wp plugin install autoptimize --activate
wp plugin install wp-asset-clean-up --activate

# Oppure installa manualmente da wp-admin → Plugin → Aggiungi nuovo
```

**Configurazione Autoptimize (5 minuti):**

1. Vai su `Settings → Autoptimize`

2. **Tab "JS, CSS & HTML":**
   
   **JavaScript Options:**
   - ✅ Optimize JavaScript Code?
   - ✅ Aggregate JS-files?
   - ⚠️ Non selezionare "Also Aggregate inline JS" (può causare problemi)
   
   **CSS Options:**
   - ✅ Optimize CSS Code?
   - ✅ Aggregate CSS-files?
   - ✅ Generate data: URIs for images?
   - ✅ Inline all CSS? (oppure "Inline and Defer CSS" se preferisci)
   
   **HTML Options:**
   - ✅ Optimize HTML Code?
   
   **Misc Options:**
   - ✅ Save aggregated script/css as static files?

3. Click "Save Changes and Empty Cache"

**Configurazione Asset CleanUp (10 minuti):**

1. Vai sulla homepage del tuo sito

2. Aggiungi `?wpacu_list` alla fine dell'URL:
   ```
   https://www.archiviowebsite.com/?wpacu_list
   ```

3. Scorri la pagina e **disabilita** i seguenti file CSS/JS (cerca e unload):

   **CSS da disabilitare sulla homepage:**
   - ✅ `dashicons.min.css` (non serve nel frontend)
   - ✅ `admin-bar.min.css` (solo se admin bar non visibile)
   - ✅ `media-views.min.css` (serve solo in admin)
   - ✅ `imgareaselect.css` (serve solo in admin)
   - ✅ Icone non utilizzate (verifica prima!):
     - `elegant-icons.min.css` (se non usi Elegant Icons)
     - `ionicons.min.css` (se non usi Ionicons)
     - `linear-icons.min.css` (se non usi Linear Icons)
     - `dripicons.min.css` (se non usi Dripicons)
     - `simple-line-icons.css` (se non usi Simple Line Icons)

   **⚠️ ATTENZIONE:** Non disabilitare:
   - jQuery
   - CSS del tema principale
   - CSS di Elementor
   - FontAwesome (se usato nel sito)

4. Click "Update" in fondo alla pagina

---

### Step 2: Ottimizza Font (15 minuti)

#### 🔤 Google Fonts Locale

```bash
# Installa plugin OMGF
wp plugin install host-webfonts-local --activate

# Oppure da wp-admin → Plugin → cerca "OMGF"
```

**Configurazione OMGF:**
1. Vai su `Settings → Optimize Google Fonts`
2. Click "Start Optimization"
3. Attendi che finisca la scansione
4. Impostazioni:
   - Font Display: `swap`
   - ✅ Remove Google Fonts
5. Click "Save Changes"

#### 🎨 FontAwesome Locale

**Metodo 1 - Via Plugin (più facile):**

```bash
# Installa plugin
wp plugin install font-awesome-more-icons --activate
```

Vai su `Settings → Font Awesome` e seleziona:
- ✅ Use hosted version (CDN disabilitato)
- Version: 5.15.3

**Metodo 2 - Manuale (più performance):**

1. Scarica FontAwesome 5.15.3:
   ```bash
   cd wp-content/themes/valeska-child/
   mkdir -p fonts
   cd fonts
   wget https://use.fontawesome.com/releases/v5.15.3/fontawesome-free-5.15.3-web.zip
   unzip fontawesome-free-5.15.3-web.zip
   mv fontawesome-free-5.15.3-web fontawesome
   ```

2. Aggiungi in `wp-content/themes/valeska-child/functions.php`:

   ```php
   // === FONTAWESOME LOCALE ===
   // Rimuovi FontAwesome CDN
   function archivio_remove_fontawesome_cdn() {
       // Rimuovi tutti i possibili handle di FontAwesome CDN
       $handles = ['fontawesome', 'fontawesome-cdn', 'font-awesome', 'font-awesome-official'];
       foreach ($handles as $handle) {
           wp_dequeue_style($handle);
           wp_deregister_style($handle);
           wp_dequeue_script($handle);
           wp_deregister_script($handle);
       }
   }
   add_action('wp_enqueue_scripts', 'archivio_remove_fontawesome_cdn', 100);

   // Carica FontAwesome locale
   function archivio_load_local_fontawesome() {
       wp_enqueue_style('fontawesome-local', 
           get_stylesheet_directory_uri() . '/fonts/fontawesome/css/all.min.css', 
           array(), 
           '5.15.3'
       );
   }
   add_action('wp_enqueue_scripts', 'archivio_load_local_fontawesome');
   ```

3. Salva e testa il sito

---

### Step 3: Defer JavaScript (5 minuti)

**Solo se NON usi WP Rocket** (WP Rocket lo fa già).

Aggiungi in `functions.php`:

```php
// === DEFER JAVASCRIPT ===
function archivio_defer_parsing_of_js($tag, $handle, $src) {
    // Non applicare in admin
    if (is_admin()) {
        return $tag;
    }
    
    // Script da NON deferire (necessari subito)
    $exclude = [
        'jquery',
        'jquery-core',
        'jquery-migrate',
    ];
    
    // Se lo script è nella lista esclusa, non deferire
    if (in_array($handle, $exclude)) {
        return $tag;
    }
    
    // Aggiungi defer a tutti gli altri script
    if (FALSE !== strpos($tag, '<script') && FALSE === strpos($tag, 'defer')) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'archivio_defer_parsing_of_js', 10, 3);
```

---

### Step 4: Test Performance (5 minuti)

```bash
# Test locale
make perf-check

# Oppure test online
# 1. Apri: https://pagespeed.web.dev/
# 2. Inserisci: https://www.archiviowebsite.com/
# 3. Click "Analyze"
```

**Risultati attesi:**
- ✅ Render blocking: < 500ms (prima: 3,050ms)
- ✅ FCP: < 1.8s
- ✅ LCP: < 2.5s
- ✅ PageSpeed Score: > 85 (mobile), > 90 (desktop)

---

## 🔍 PROBLEMI COMUNI

### Problema 1: Sito rotto dopo Autoptimize

**Soluzione:**
1. Vai su `Settings → Autoptimize`
2. Disabilita "Aggregate JS-files"
3. Oppure aggiungi script problematici a "Exclude scripts from Autoptimize"
4. Svuota cache: `Tools → Empty Cache`

### Problema 2: Font non si caricano

**Soluzione:**
1. Verifica che i file font siano stati caricati correttamente
2. Controlla permessi: `chmod 644 wp-content/themes/*/fonts/**/*`
3. Svuota cache browser (Ctrl+Shift+R)
4. Svuota cache plugin

### Problema 3: JavaScript non funzionano

**Soluzione:**
1. Escludi jQuery dal defer:
   - Se usi WP Rocket: Settings → File Optimization → JavaScript Files → Excluded JavaScript Files → aggiungi `/jquery(?:\.min)?\.js`
   - Se usi Autoptimize: disabilita "Aggregate JS-files"
2. Svuota cache

### Problema 4: CSS non si applica

**Soluzione:**
1. In Autoptimize, disabilita "Inline all CSS"
2. Prova "Inline and Defer CSS" invece
3. Oppure disabilita solo "Generate data: URIs for images"
4. Svuota cache

---

## 📊 VERIFICA RISULTATI

### Prima dell'ottimizzazione:
- ❌ Render blocking: 3,050ms
- ❌ Transfer Size: 483.1 KiB
- ❌ ~100+ richieste HTTP
- ❌ FCP: ~5s
- ❌ LCP: ~6s

### Dopo l'ottimizzazione:
- ✅ Render blocking: < 500ms
- ✅ Transfer Size: < 200 KiB
- ✅ ~40-50 richieste HTTP
- ✅ FCP: < 1.8s
- ✅ LCP: < 2.5s

---

## 🎯 PROSSIMI STEP (opzionale)

Se vuoi ottimizzare ulteriormente:

1. **Critical CSS Inline:**
   - Genera: https://jonassebastianohlsson.com/criticalpathcssgenerator/
   - Aggiungi in `functions.php`

2. **Preload risorse chiave:**
   ```php
   function archivio_preload_assets() {
       echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/fonts/fontawesome/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>';
   }
   add_action('wp_head', 'archivio_preload_assets', 1);
   ```

3. **CDN Cloudflare:**
   - Registrati su https://www.cloudflare.com/
   - Aggiungi il tuo dominio
   - Cambia nameserver da Aruba

4. **Lazy Load avanzato:**
   - Plugin: https://wordpress.org/plugins/rocket-lazy-load/

---

## 📝 CHECKLIST COMPLETA

- [ ] ✅ Plugin di ottimizzazione installato (WP Rocket o Autoptimize)
- [ ] ✅ Asset CleanUp configurato (se Autoptimize)
- [ ] ✅ Google Fonts hostati localmente (OMGF)
- [ ] ✅ FontAwesome hostato localmente
- [ ] ✅ JavaScript deferred
- [ ] ✅ Cache svuotata (plugin + browser)
- [ ] ✅ Test PageSpeed Insights eseguito
- [ ] ✅ Sito testato (navigazione OK, no errori)

---

## 🆘 SUPPORTO

Se qualcosa non funziona:

1. **Backup:** prima di qualsiasi modifica, fai backup!
   ```bash
   make backup
   ```

2. **Ripristino:** se qualcosa va storto
   ```bash
   # Disattiva plugin via SSH
   wp plugin deactivate autoptimize wp-asset-clean-up
   
   # Oppure rinomina cartella plugin via FTP
   # wp-content/plugins/autoptimize → autoptimize-disabled
   ```

3. **Log errori:** controlla `wp-content/debug.log`

4. **Cache:** svuota SEMPRE la cache dopo modifiche

---

## 🔗 RISORSE UTILI

- **WP Rocket:** https://wp-rocket.me/
- **Autoptimize:** https://wordpress.org/plugins/autoptimize/
- **Asset CleanUp:** https://wordpress.org/plugins/wp-asset-clean-up/
- **OMGF:** https://wordpress.org/plugins/host-webfonts-local/
- **PageSpeed Insights:** https://pagespeed.web.dev/
- **GTmetrix:** https://gtmetrix.com/

---

**Tempo stimato totale: 30-60 minuti**

**Miglioramento atteso: -2.5 secondi di loading time** 🚀


