#!/bin/bash

# Script per testare la connessione SSH ad Aruba
# Uso: ./test-ssh-aruba.sh [username] [hostname]

set -e

# Colori
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== Test Connessione SSH Aruba ===${NC}\n"

# Se vengono passati parametri, usali
if [ $# -ge 2 ]; then
    USERNAME=$1
    HOSTNAME=$2
    PORT=${3:-22}
else
    echo "Inserisci i dati per la connessione SSH ad Aruba:"
    echo ""
    read -p "Username Aruba: " USERNAME
    read -p "Hostname/Server (es. ftp.tuo-sito.com o server.aruba.it): " HOSTNAME
    read -p "Porta SSH (default 22): " PORT
    PORT=${PORT:-22}
    echo ""
fi

echo -e "${YELLOW}Test 1: Verifica connessione di base...${NC}"
if timeout 5 ssh -o ConnectTimeout=5 -o BatchMode=yes -p "$PORT" "$USERNAME@$HOSTNAME" "echo 'Connessione OK'" 2>/dev/null; then
    echo -e "${GREEN}✓ Connessione SSH funzionante!${NC}\n"
    SSH_WORKS=true
else
    echo -e "${RED}✗ Impossibile connettersi via SSH${NC}\n"
    SSH_WORKS=false
    
    echo -e "${YELLOW}Possibili cause:${NC}"
    echo "  1. SSH non è abilitato sul tuo piano Aruba"
    echo "  2. Credenziali errate (username o hostname)"
    echo "  3. Porta SSH errata (prova 22, 2222, o altre)"
    echo "  4. Firewall che blocca la connessione"
    echo ""
    echo -e "${YELLOW}Prova manualmente:${NC}"
    echo "  ssh -p $PORT $USERNAME@$HOSTNAME"
    echo ""
    exit 1
fi

if [ "$SSH_WORKS" = true ]; then
    echo -e "${YELLOW}Test 2: Verifica ambiente...${NC}"
    
    # Test informazioni sistema
    echo "Informazioni server:"
    ssh -p "$PORT" "$USERNAME@$HOSTNAME" "uname -a; pwd; whoami" 2>/dev/null || true
    echo ""
    
    echo -e "${YELLOW}Test 3: Verifica wp-cli...${NC}"
    if ssh -p "$PORT" "$USERNAME@$HOSTNAME" "command -v wp >/dev/null 2>&1 || [ -f ~/bin/wp ] || [ -f ~/wp-cli.phar ]" 2>/dev/null; then
        echo -e "${GREEN}✓ wp-cli trovato!${NC}"
        ssh -p "$PORT" "$USERNAME@$HOSTNAME" "wp --version 2>/dev/null || ~/bin/wp --version 2>/dev/null || php ~/wp-cli.phar --version 2>/dev/null" || true
    else
        echo -e "${YELLOW}⚠ wp-cli non trovato${NC}"
        echo "  Dovrai installarlo seguendo le istruzioni in SYNC-ARUBA.md"
    fi
    echo ""
    
    echo -e "${YELLOW}Test 4: Verifica percorsi comuni WordPress...${NC}"
    COMMON_PATHS=(
        "/home/$USERNAME/public_html"
        "/home/$USERNAME/www"
        "/home/$USERNAME/domains"
        "~/public_html"
        "~/www"
    )
    
    for path in "${COMMON_PATHS[@]}"; do
        if ssh -p "$PORT" "$USERNAME@$HOSTNAME" "[ -d $path ] && [ -f $path/wp-config.php ]" 2>/dev/null; then
            echo -e "${GREEN}✓ WordPress trovato in: $path${NC}"
            FULL_PATH=$(ssh -p "$PORT" "$USERNAME@$HOSTNAME" "cd $path && pwd" 2>/dev/null)
            echo "  Percorso completo: $FULL_PATH"
        fi
    done
    echo ""
    
    echo -e "${GREEN}=== Test completato con successo! ===${NC}"
    echo ""
    echo -e "${BLUE}Configura sync-wp.sh con:${NC}"
    echo "  REMOTE_HOST=\"$HOSTNAME\""
    echo "  REMOTE_USER=\"$USERNAME\""
    echo "  REMOTE_SSH_PORT=\"$PORT\""
    echo ""
    
    # Prova a trovare il percorso WordPress
    WP_PATH=$(ssh -p "$PORT" "$USERNAME@$HOSTNAME" "find ~ -maxdepth 3 -name 'wp-config.php' -type f 2>/dev/null | head -1 | xargs dirname" 2>/dev/null || echo "")
    if [ -n "$WP_PATH" ]; then
        echo "  REMOTE_WP_PATH=\"$WP_PATH\""
    else
        echo "  REMOTE_WP_PATH=\"/home/$USERNAME/public_html\"  # Verifica questo percorso"
    fi
fi

