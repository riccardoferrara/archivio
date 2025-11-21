.PHONY: help test-ssh test-ftp pull push pull-ftp push-ftp pull-ssh push-ssh \
	pull-ftp-files push-ftp-files pull-ssh-files push-ssh-files \
	push-file push-file-ftp push-file-ssh \
	update-core update-plugins update-themes update-all \
	db-check db-info db-export db-export-gz db-import db-replace db-replace-reverse \
	db-optimize db-repair db-reset db-query \
	backup clean

# PATH - Assicura che wp-cli e MySQL siano trovati
# Cerca MySQL in percorsi comuni
MYSQL_BIN := $(shell find /usr/local -name mysqldump 2>/dev/null | head -1 | xargs dirname 2>/dev/null || echo "")
export PATH := $(HOME)/bin:$(MYSQL_BIN):$(PATH)

# Colori per output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[1;33m
RED := \033[0;31m
NC := \033[0m # No Color

# ============================================
# HELP - Mostra tutti i comandi disponibili
# ============================================
help:
	@echo "$(BLUE)╔════════════════════════════════════════════════════════════╗$(NC)"
	@echo "$(BLUE)║  WordPress Development Makefile                            ║$(NC)"
	@echo "$(BLUE)╚════════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	@echo "$(GREEN)TEST CONNESSIONI:$(NC)"
	@echo "  make test-ssh          Testa connessione SSH al server remoto"
	@echo "  make test-ftp           Testa connessione FTP al server remoto"
	@echo ""
	@echo "$(GREEN)SINCRONIZZAZIONE (SSH - Consigliato):$(NC)"
	@echo "  make pull-ssh          Scarica da produzione a locale (SSH)"
	@echo "  make push-ssh          Carica da locale a produzione (SSH)"
	@echo "  make pull-ssh-files    Scarica solo i file (SSH)"
	@echo "  make push-ssh-files    Carica solo i file (SSH)"
	@echo ""
	@echo "$(GREEN)SINCRONIZZAZIONE (FTP - Se SSH non disponibile):$(NC)"
	@echo "  make pull-ftp          Scarica da produzione a locale (FTP)"
	@echo "  make push-ftp          Carica da locale a produzione (FTP)"
	@echo "  make pull-ftp-files    Scarica solo i file (FTP)"
	@echo "  make push-ftp-files    Carica solo i file (FTP)"
	@echo ""
	@echo "$(GREEN)UPLOAD SINGOLO FILE:$(NC)"
	@echo "  make push-file FILE=   Carica un singolo file su produzione (FTP)"
	@echo "                         Es: make push-file FILE=wp-content/themes/valeska/functions.php"
	@echo ""
	@echo "$(GREEN)AGGIORNAMENTI WORDPRESS:$(NC)"
	@echo "  make update-core       Aggiorna WordPress core"
	@echo "  make update-plugins    Aggiorna tutti i plugin"
	@echo "  make update-themes     Aggiorna tutti i temi"
	@echo "  make update-all        Aggiorna core, plugin e temi"
	@echo ""
	@echo "$(GREEN)DATABASE:$(NC)"
	@echo "  make db-check          Verifica connessione database"
	@echo "  make db-info           Mostra informazioni e dimensioni database"
	@echo "  make db-export         Esporta database locale"
	@echo "  make db-export-gz      Esporta database compresso (.gz)"
	@echo "  make db-import FILE=   Importa database (es: make db-import FILE=backup.sql)"
	@echo "  make db-replace        Sostituisce URL remoto→locale nel database"
	@echo "  make db-replace-reverse Sostituisce URL locale→remoto nel database"
	@echo "  make db-optimize       Ottimizza le tabelle del database"
	@echo "  make db-repair         Ripara le tabelle del database"
	@echo "  make db-reset           Reset completo database (ATTENZIONE!)"
	@echo "  make db-query SQL=     Esegue query SQL personalizzata"
	@echo ""
	@echo "$(GREEN)BACKUP E PULIZIA:$(NC)"
	@echo "  make backup            Crea backup completo (file + database)"
	@echo "  make clean             Pulisce file temporanei e cache"
	@echo ""
	@echo "$(YELLOW)NOTA:$(NC) Configura gli script sync-wp.sh e sync-wp-ftp.sh prima di usare i comandi di sincronizzazione"

# ============================================
# TEST CONNESSIONI
# ============================================
test-ssh:
	@echo "$(BLUE)=== Test Connessione SSH ===$(NC)"
	@if [ -f test-ssh-aruba.sh ]; then \
		./test-ssh-aruba.sh; \
	else \
		echo "$(RED)File test-ssh-aruba.sh non trovato$(NC)"; \
	fi

test-ftp:
	@echo "$(BLUE)=== Test Connessione FTP ===$(NC)"
	@if [ -f test-ftp-aruba.sh ]; then \
		./test-ftp-aruba.sh; \
	else \
		echo "$(RED)File test-ftp-aruba.sh non trovato$(NC)"; \
	fi

# ============================================
# SINCRONIZZAZIONE SSH
# ============================================
pull-ssh:
	@echo "$(BLUE)=== PULL da produzione (SSH) ===$(NC)"
	@if [ -f sync-wp.sh ]; then \
		./sync-wp.sh pull; \
	else \
		echo "$(RED)File sync-wp.sh non trovato$(NC)"; \
	fi

push-ssh:
	@echo "$(BLUE)=== PUSH verso produzione (SSH) ===$(NC)"
	@if [ -f sync-wp.sh ]; then \
		./sync-wp.sh push; \
	else \
		echo "$(RED)File sync-wp.sh non trovato$(NC)"; \
	fi

pull-ssh-files:
	@echo "$(BLUE)=== PULL file da produzione (SSH) ===$(NC)"
	@if [ -f sync-wp.sh ]; then \
		./sync-wp.sh pull --files-only; \
	else \
		echo "$(RED)File sync-wp.sh non trovato$(NC)"; \
	fi

push-ssh-files:
	@echo "$(BLUE)=== PUSH file verso produzione (SSH) ===$(NC)"
	@if [ -f sync-wp.sh ]; then \
		./sync-wp.sh push --files-only; \
	else \
		echo "$(RED)File sync-wp.sh non trovato$(NC)"; \
	fi

# ============================================
# SINCRONIZZAZIONE FTP
# ============================================
pull-ftp:
	@echo "$(BLUE)=== PULL da produzione (FTP) ===$(NC)"
	@if [ -f sync-wp-ftp.sh ]; then \
		./sync-wp-ftp.sh pull; \
	else \
		echo "$(RED)File sync-wp-ftp.sh non trovato$(NC)"; \
	fi

push-ftp:
	@echo "$(BLUE)=== PUSH verso produzione (FTP) ===$(NC)"
	@if [ -f sync-wp-ftp.sh ]; then \
		./sync-wp-ftp.sh push; \
	else \
		echo "$(RED)File sync-wp-ftp.sh non trovato$(NC)"; \
	fi

pull-ftp-files:
	@echo "$(BLUE)=== PULL file da produzione (FTP) ===$(NC)"
	@if [ -f sync-wp-ftp.sh ]; then \
		./sync-wp-ftp.sh pull --files-only; \
	else \
		echo "$(RED)File sync-wp-ftp.sh non trovato$(NC)"; \
	fi

push-ftp-files:
	@echo "$(BLUE)=== PUSH file verso produzione (FTP) ===$(NC)"
	@if [ -f sync-wp-ftp.sh ]; then \
		./sync-wp-ftp.sh push --files-only; \
	else \
		echo "$(RED)File sync-wp-ftp.sh non trovato$(NC)"; \
	fi

# ============================================
# UPLOAD SINGOLO FILE
# ============================================
push-file:
	@if [ -z "$(FILE)" ]; then \
		echo "$(RED)Errore: Specifica il file con FILE=percorso/file$(NC)"; \
		echo "Esempio: make push-file FILE=wp-content/themes/valeska/functions.php"; \
		exit 1; \
	fi
	@if [ ! -f "$(FILE)" ]; then \
		echo "$(RED)File non trovato: $(FILE)$(NC)"; \
		echo "$(YELLOW)Assicurati di essere nella directory root di WordPress$(NC)"; \
		exit 1; \
	fi
	@echo "$(BLUE)=== Upload singolo file verso produzione (FTP) ===$(NC)"
	@if [ -f sync-wp-ftp.sh ]; then \
		./sync-wp-ftp.sh upload-file "$(FILE)"; \
	else \
		echo "$(RED)File sync-wp-ftp.sh non trovato$(NC)"; \
	fi

push-file-ftp: push-file
	@echo "$(YELLOW)NOTA: Usato FTP. Per SSH usa: make push-file-ssh$(NC)"

push-file-ssh:
	@if [ -z "$(FILE)" ]; then \
		echo "$(RED)Errore: Specifica il file con FILE=percorso/file$(NC)"; \
		echo "Esempio: make push-file-ssh FILE=wp-content/themes/valeska/functions.php"; \
		exit 1; \
	fi
	@if [ ! -f "$(FILE)" ]; then \
		echo "$(RED)File non trovato: $(FILE)$(NC)"; \
		echo "$(YELLOW)Assicurati di essere nella directory root di WordPress$(NC)"; \
		exit 1; \
	fi
	@echo "$(BLUE)=== Upload singolo file verso produzione (SSH) ===$(NC)"
	@if [ -f sync-wp.sh ]; then \
		echo "$(YELLOW)NOTA: La funzione upload-file non è ancora implementata per SSH$(NC)"; \
		echo "$(YELLOW)Usa: make push-file FILE=$(FILE)$(NC)"; \
	else \
		echo "$(RED)File sync-wp.sh non trovato$(NC)"; \
	fi

# ============================================
# AGGIORNAMENTI WORDPRESS
# ============================================
update-core:
	@echo "$(BLUE)=== Aggiornamento WordPress Core ===$(NC)"
	@wp core update --path=$(PWD) || echo "$(RED)Errore: wp-cli non trovato o WordPress non configurato$(NC)"

update-plugins:
	@echo "$(BLUE)=== Aggiornamento Plugin ===$(NC)"
	@wp plugin update --all --path=$(PWD) || echo "$(RED)Errore: wp-cli non trovato o WordPress non configurato$(NC)"

update-themes:
	@echo "$(BLUE)=== Aggiornamento Temi ===$(NC)"
	@wp theme update --all --path=$(PWD) || echo "$(RED)Errore: wp-cli non trovato o WordPress non configurato$(NC)"

update-all: update-core update-plugins update-themes
	@echo "$(GREEN)✓ Tutti gli aggiornamenti completati$(NC)"

# ============================================
# DATABASE
# ============================================
db-check:
	@echo "$(BLUE)=== Verifica Connessione Database ===$(NC)"
	@wp db check --path=$(PWD) && \
	echo "$(GREEN)✓ Database OK$(NC)" || \
	echo "$(RED)Errore: wp-cli non trovato o problemi con il database$(NC)"

db-info:
	@echo "$(BLUE)=== Informazioni Database ===$(NC)"
	@wp db size --path=$(PWD) --human-readable 2>/dev/null || \
	echo "$(YELLOW)Info database non disponibili$(NC)"
	@echo ""
	@wp db query "SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)' FROM information_schema.TABLES WHERE table_schema = DATABASE() ORDER BY (data_length + index_length) DESC LIMIT 10;" --path=$(PWD) 2>/dev/null || true

db-export:
	@echo "$(BLUE)=== Esportazione Database ===$(NC)"
	@TIMESTAMP=$$(date +%Y%m%d_%H%M%S); \
	BACKUP_DIR="backups/db"; \
	mkdir -p $$BACKUP_DIR; \
	FILE="$$BACKUP_DIR/backup_db_$$TIMESTAMP.sql"; \
	wp db export $$FILE --path=$(PWD) && \
	echo "$(GREEN)✓ Database esportato: $$FILE$(NC)" && \
	ls -lh $$FILE || \
	echo "$(RED)Errore: wp-cli non trovato o WordPress non configurato$(NC)"

db-export-gz:
	@echo "$(BLUE)=== Esportazione Database (compresso) ===$(NC)"
	@TIMESTAMP=$$(date +%Y%m%d_%H%M%S); \
	BACKUP_DIR="backups/db"; \
	mkdir -p $$BACKUP_DIR; \
	FILE="$$BACKUP_DIR/backup_db_$$TIMESTAMP.sql.gz"; \
	wp db export - --path=$(PWD) | gzip > $$FILE && \
	echo "$(GREEN)✓ Database esportato (compresso): $$FILE$(NC)" && \
	ls -lh $$FILE || \
	echo "$(RED)Errore: wp-cli non trovato o WordPress non configurato$(NC)"

db-import:
	@if [ -z "$(FILE)" ]; then \
		echo "$(RED)Errore: Specifica il file con FILE=nomefile.sql$(NC)"; \
		echo "Esempio: make db-import FILE=backup.sql"; \
		exit 1; \
	fi
	@if [ ! -f "$(FILE)" ]; then \
		echo "$(RED)File non trovato: $(FILE)$(NC)"; \
		exit 1; \
	fi
	@echo "$(BLUE)=== Importazione Database: $(FILE) ===$(NC)"
	@echo "$(YELLOW)ATTENZIONE: Questo sovrascriverà il database corrente!$(NC)"
	@echo "$(YELLOW)Vuoi creare un backup prima? (s/n)$(NC)"
	@read -p "> " backup_confirm; \
	if [ "$$backup_confirm" = "s" ] || [ "$$backup_confirm" = "S" ]; then \
		$(MAKE) db-export; \
	fi
	@read -p "Confermi importazione? (s/n): " confirm && [ "$$confirm" = "s" ] || exit 1
	@if echo "$(FILE)" | grep -q "\.gz$$"; then \
		echo "$(YELLOW)Decompressione file...$(NC)"; \
		gunzip -c $(FILE) | wp db import - --path=$(PWD) && \
		echo "$(GREEN)✓ Database importato$(NC)" || \
		echo "$(RED)Errore nell'importazione$(NC)"; \
	else \
		wp db import $(FILE) --path=$(PWD) && \
		echo "$(GREEN)✓ Database importato$(NC)" || \
		echo "$(RED)Errore nell'importazione$(NC)"; \
	fi

db-replace:
	@echo "$(BLUE)=== Sostituzione URL nel Database ===$(NC)"
	@if [ -f sync-wp-ftp.sh ]; then \
		REMOTE_URL=$$(grep "^REMOTE_URL=" sync-wp-ftp.sh | cut -d'"' -f2 | sed 's|/$$||'); \
		LOCAL_URL=$$(grep "^LOCAL_URL=" sync-wp-ftp.sh | cut -d'"' -f2 | sed 's|/$$||'); \
		if [ -n "$$REMOTE_URL" ] && [ -n "$$LOCAL_URL" ]; then \
			echo "$(YELLOW)Sostituisco: $$REMOTE_URL → $$LOCAL_URL$(NC)"; \
			wp search-replace $$REMOTE_URL $$LOCAL_URL --path=$(PWD) --all-tables --skip-columns=guid --dry-run && \
			echo "" && \
			read -p "Confermi la sostituzione? (s/n): " confirm && [ "$$confirm" = "s" ] && \
			wp search-replace $$REMOTE_URL $$LOCAL_URL --path=$(PWD) --all-tables --skip-columns=guid && \
			echo "$(GREEN)✓ URL sostituiti$(NC)" || \
			echo "$(YELLOW)Operazione annullata$(NC)"; \
		else \
			echo "$(RED)Configura REMOTE_URL e LOCAL_URL in sync-wp-ftp.sh$(NC)"; \
		fi \
	else \
		echo "$(RED)File sync-wp-ftp.sh non trovato$(NC)"; \
	fi

db-replace-reverse:
	@echo "$(BLUE)=== Sostituzione URL nel Database (inverso) ===$(NC)"
	@if [ -f sync-wp-ftp.sh ]; then \
		REMOTE_URL=$$(grep "^REMOTE_URL=" sync-wp-ftp.sh | cut -d'"' -f2 | sed 's|/$$||'); \
		LOCAL_URL=$$(grep "^LOCAL_URL=" sync-wp-ftp.sh | cut -d'"' -f2 | sed 's|/$$||'); \
		if [ -n "$$REMOTE_URL" ] && [ -n "$$LOCAL_URL" ]; then \
			echo "$(YELLOW)Sostituisco: $$LOCAL_URL → $$REMOTE_URL$(NC)"; \
			wp search-replace $$LOCAL_URL $$REMOTE_URL --path=$(PWD) --all-tables --skip-columns=guid --dry-run && \
			echo "" && \
			read -p "Confermi la sostituzione? (s/n): " confirm && [ "$$confirm" = "s" ] && \
			wp search-replace $$LOCAL_URL $$REMOTE_URL --path=$(PWD) --all-tables --skip-columns=guid && \
			echo "$(GREEN)✓ URL sostituiti$(NC)" || \
			echo "$(YELLOW)Operazione annullata$(NC)"; \
		else \
			echo "$(RED)Configura REMOTE_URL e LOCAL_URL in sync-wp-ftp.sh$(NC)"; \
		fi \
	else \
		echo "$(RED)File sync-wp-ftp.sh non trovato$(NC)"; \
	fi

db-reset:
	@echo "$(RED)=== RESET DATABASE ===$(NC)"
	@echo "$(RED)ATTENZIONE: Questo eliminerà TUTTI i dati dal database!$(NC)"
	@read -p "Sei sicuro? Scrivi 'RESET' per confermare: " confirm && [ "$$confirm" = "RESET" ] || exit 1
	@echo "$(YELLOW)Creo backup prima del reset...$(NC)"
	@$(MAKE) db-export > /dev/null 2>&1 || true
	@wp db reset --yes --path=$(PWD) && \
	echo "$(GREEN)✓ Database resettato$(NC)" || \
	echo "$(RED)Errore nel reset$(NC)"

db-optimize:
	@echo "$(BLUE)=== Ottimizzazione Database ===$(NC)"
	@wp db optimize --path=$(PWD) && \
	echo "$(GREEN)✓ Database ottimizzato$(NC)" || \
	echo "$(RED)Errore nell'ottimizzazione$(NC)"

db-repair:
	@echo "$(BLUE)=== Riparazione Database ===$(NC)"
	@wp db repair --path=$(PWD) && \
	echo "$(GREEN)✓ Database riparato$(NC)" || \
	echo "$(RED)Errore nella riparazione$(NC)"

db-query:
	@if [ -z "$(SQL)" ]; then \
		echo "$(RED)Errore: Specifica la query con SQL=\"SELECT ...\"$(NC)"; \
		echo "Esempio: make db-query SQL=\"SELECT COUNT(*) FROM wp_posts\"$(NC)"; \
		exit 1; \
	fi
	@echo "$(BLUE)=== Esecuzione Query ===$(NC)"
	@wp db query "$(SQL)" --path=$(PWD) || \
	echo "$(RED)Errore nell'esecuzione della query$(NC)"

# ============================================
# BACKUP E PULIZIA
# ============================================
backup:
	@echo "$(BLUE)=== Backup Completo ===$(NC)"
	@TIMESTAMP=$$(date +%Y%m%d_%H%M%S); \
	BACKUP_DIR="backups/backup_$$TIMESTAMP"; \
	mkdir -p $$BACKUP_DIR; \
	echo "$(YELLOW)1. Esportazione database...$(NC)"; \
	wp db export $$BACKUP_DIR/database.sql --path=$(PWD) 2>/dev/null || echo "$(YELLOW)   Database non esportato (wp-cli non disponibile)$(NC)"; \
	echo "$(YELLOW)2. Backup file wp-content...$(NC)"; \
	tar -czf $$BACKUP_DIR/wp-content.tar.gz wp-content/ 2>/dev/null || echo "$(YELLOW)   File non copiati$(NC)"; \
	echo "$(GREEN)✓ Backup completato: $$BACKUP_DIR$(NC)"

clean:
	@echo "$(BLUE)=== Pulizia File Temporanei ===$(NC)"
	@find . -name ".DS_Store" -delete 2>/dev/null || true
	@find . -name "*.log" -delete 2>/dev/null || true
	@find . -name "*.tmp" -delete 2>/dev/null || true
	@find wp-content/uploads -name "*.tmp" -delete 2>/dev/null || true
	@echo "$(GREEN)✓ Pulizia completata$(NC)"

# ============================================
# ALIAS RAPIDI
# ============================================
pull: pull-ftp
	@echo "$(YELLOW)NOTA: Usato FTP. Per SSH usa: make pull-ssh$(NC)"

push: push-ftp
	@echo "$(YELLOW)NOTA: Usato FTP. Per SSH usa: make push-ssh$(NC)"

