-- Script per attivare il tema corretto
-- Esegui: mysql -uroot -proot archivio_local < fix-theme.sql

-- Verifica tema attuale
SELECT 'Tema attuale:' as Info, option_name, option_value 
FROM wp_options 
WHERE option_name IN ('template', 'stylesheet');

-- Attiva tema valeska (o valeska-child-server se preferisci)
UPDATE wp_options SET option_value = 'valeska' WHERE option_name = 'template';
UPDATE wp_options SET option_value = 'valeska' WHERE option_name = 'stylesheet';

-- Se preferisci il tema child, usa invece:
-- UPDATE wp_options SET option_value = 'valeska-child-server' WHERE option_name = 'template';
-- UPDATE wp_options SET option_value = 'valeska-child-server' WHERE option_name = 'stylesheet';

-- Verifica tema aggiornato
SELECT 'Tema aggiornato:' as Info, option_name, option_value 
FROM wp_options 
WHERE option_name IN ('template', 'stylesheet');
