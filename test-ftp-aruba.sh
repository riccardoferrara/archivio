#!/bin/bash

# Script per testare la connessione FTP ad Aruba
# Uso: ./test-ftp-aruba.sh

set -e

# Colori
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}=== Test Connessione FTP Aruba ===${NC}\n"

# Leggi configurazione da sync-wp-ftp.sh se esiste
if [ -f "sync-wp-ftp.sh" ]; then
    echo "Leggo configurazione da sync-wp-ftp.sh..."
    REMOTE_FTP_HOST=$(grep "^REMOTE_FTP_HOST=" sync-wp-ftp.sh | cut -d'"' -f2)
    REMOTE_FTP_USER=$(grep "^REMOTE_FTP_USER=" sync-wp-ftp.sh | cut -d'"' -f2)
    REMOTE_FTP_PASS=$(grep "^REMOTE_FTP_PASS=" sync-wp-ftp.sh | cut -d'"' -f2)
    REMOTE_FTP_PORT=$(grep "^REMOTE_FTP_PORT=" sync-wp-ftp.sh | cut -d'"' -f2)
    REMOTE_FTP_PATH=$(grep "^REMOTE_FTP_PATH=" sync-wp-ftp.sh | cut -d'"' -f2)
    
    echo -e "${GREEN}Configurazione trovata:${NC}"
    echo "  Host: $REMOTE_FTP_HOST"
    echo "  User: $REMOTE_FTP_USER"
    echo "  Port: $REMOTE_FTP_PORT"
    echo "  Path: $REMOTE_FTP_PATH"
    echo ""
else
    echo "Inserisci i dati FTP:"
    read -p "Hostname FTP: " REMOTE_FTP_HOST
    read -p "Username: " REMOTE_FTP_USER
    read -sp "Password: " REMOTE_FTP_PASS
    echo ""
    read -p "Porta (default 21): " REMOTE_FTP_PORT
    REMOTE_FTP_PORT=${REMOTE_FTP_PORT:-21}
    read -p "Percorso (default /public_html): " REMOTE_FTP_PATH
    REMOTE_FTP_PATH=${REMOTE_FTP_PATH:-/public_html}
    echo ""
fi

# Test 1: FTP standard (porta 21)
echo -e "${YELLOW}Test 1: FTP standard (porta 21)...${NC}"
if lftp -c "set ftp:list-options -a; set ssl:verify-certificate no; open -p 21 -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS $REMOTE_FTP_HOST; ls" 2>&1 | head -5; then
    echo -e "${GREEN}✓ Connessione FTP (porta 21) funzionante!${NC}\n"
    FTP_WORKS=true
    WORKING_PORT=21
else
    echo -e "${RED}✗ FTP porta 21 fallito${NC}\n"
    FTP_WORKS=false
fi

# Test 2: SFTP (porta 22)
if [ "$FTP_WORKS" != true ]; then
    echo -e "${YELLOW}Test 2: SFTP (porta 22)...${NC}"
    if lftp -c "set ssl:verify-certificate no; open -p 22 -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS sftp://$REMOTE_FTP_HOST; ls" 2>&1 | head -5; then
        echo -e "${GREEN}✓ Connessione SFTP (porta 22) funzionante!${NC}\n"
        FTP_WORKS=true
        WORKING_PORT=22
    else
        echo -e "${RED}✗ SFTP porta 22 fallito${NC}\n"
    fi
fi

# Test 3: Hostname alternativi
if [ "$FTP_WORKS" != true ]; then
    echo -e "${YELLOW}Test 3: Hostname alternativi...${NC}"
    
    # Rimuovi "ftp." dall'hostname se presente
    ALT_HOST=$(echo "$REMOTE_FTP_HOST" | sed 's/^ftp\.//')
    
    if [ "$ALT_HOST" != "$REMOTE_FTP_HOST" ]; then
        echo "  Provo: $ALT_HOST"
        if lftp -c "set ftp:list-options -a; set ssl:verify-certificate no; open -p 21 -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS $ALT_HOST; ls" 2>&1 | head -3 > /dev/null 2>&1; then
            echo -e "${GREEN}✓ Funziona con: $ALT_HOST${NC}\n"
            FTP_WORKS=true
            WORKING_HOST=$ALT_HOST
            WORKING_PORT=21
        fi
    fi
    
    # Prova ftp.aruba.it
    if [ "$FTP_WORKS" != true ]; then
        echo "  Provo: ftp.aruba.it"
        if lftp -c "set ftp:list-options -a; set ssl:verify-certificate no; open -p 21 -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS ftp.aruba.it; ls" 2>&1 | head -3 > /dev/null 2>&1; then
            echo -e "${GREEN}✓ Funziona con: ftp.aruba.it${NC}\n"
            FTP_WORKS=true
            WORKING_HOST="ftp.aruba.it"
            WORKING_PORT=21
        fi
    fi
fi

# Risultato finale
if [ "$FTP_WORKS" = true ]; then
    echo -e "${GREEN}=== Test completato con successo! ===${NC}\n"
    echo -e "${BLUE}Configurazione consigliata per sync-wp-ftp.sh:${NC}"
    if [ -n "$WORKING_HOST" ]; then
        echo "  REMOTE_FTP_HOST=\"$WORKING_HOST\""
    else
        echo "  REMOTE_FTP_HOST=\"$REMOTE_FTP_HOST\""
    fi
    echo "  REMOTE_FTP_PORT=\"$WORKING_PORT\""
    echo ""
    
    # Test percorso WordPress
    echo -e "${YELLOW}Verifica percorso WordPress...${NC}"
    TEST_PATH=${REMOTE_FTP_PATH:-/public_html}
    if [ "$WORKING_PORT" = "22" ]; then
        PROTO="sftp://"
    else
        PROTO=""
    fi
    
    if [ -n "$WORKING_HOST" ]; then
        TEST_HOST=$WORKING_HOST
    else
        TEST_HOST=$REMOTE_FTP_HOST
    fi
    
    if lftp -c "set ssl:verify-certificate no; open -p $WORKING_PORT -u $REMOTE_FTP_USER,$REMOTE_FTP_PASS ${PROTO}${TEST_HOST}; cd $TEST_PATH; ls wp-config.php" 2>&1 | grep -q "wp-config.php"; then
        echo -e "${GREEN}✓ WordPress trovato in: $TEST_PATH${NC}"
        echo "  REMOTE_FTP_PATH=\"$TEST_PATH\""
    else
        echo -e "${YELLOW}⚠ wp-config.php non trovato in $TEST_PATH${NC}"
        echo "  Prova a verificare il percorso manualmente"
    fi
else
    echo -e "${RED}=== Test fallito ===${NC}\n"
    echo -e "${YELLOW}Possibili cause:${NC}"
    echo "  1. Credenziali errate"
    echo "  2. Filtro accessi FTP attivo nel pannello Aruba"
    echo "  3. Firewall che blocca la connessione"
    echo "  4. Hostname errato"
    echo ""
    echo -e "${YELLOW}Azioni consigliate:${NC}"
    echo "  1. Verifica credenziali nel pannello Aruba (https://admin.aruba.it)"
    echo "  2. Controlla il filtro accessi FTP nel pannello"
    echo "  3. Prova a connetterti con FileZilla o Cyberduck"
    echo "  4. Contatta il supporto Aruba"
fi

