#!/bin/bash

if [[ "$RUN_WS" == "1" ]]; then
    echo "Iniciando WebSocket Server..."
    exec php /var/www/html/server.php
else
    echo "Iniciando Apache..."
    exec apache2-foreground
fi