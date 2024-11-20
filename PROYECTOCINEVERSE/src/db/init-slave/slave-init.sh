#!/bin/bash

# Variables de entorno
MASTER_HOST=${MASTER_HOST:-database_master}
MASTER_USER=${MASTER_USER:-replicator}
MASTER_PASSWORD=${MASTER_PASSWORD:-replicator_password}

# Esperar a que el maestro esté listo
until mysql -h"$MASTER_HOST" -uroot -p"$MYSQL_ROOT_PASSWORD" -e "SHOW MASTER STATUS;" > /dev/null 2>&1; do
  echo "Esperando al maestro..."
  sleep 5
done

# Obtener estado del maestro
MASTER_STATUS=$(mysql -h"$MASTER_HOST" -uroot -p"$MYSQL_ROOT_PASSWORD" -e "SHOW MASTER STATUS\G")
MASTER_LOG_FILE=$(echo "$MASTER_STATUS" | grep "File:" | awk '{print $2}')
MASTER_LOG_POS=$(echo "$MASTER_STATUS" | grep "Position:" | awk '{print $2}')

# Configurar esclavo
mysql -uroot -p"$MYSQL_ROOT_PASSWORD" <<-EOSQL
  CHANGE MASTER TO
    MASTER_HOST='$MASTER_HOST',
    MASTER_USER='$MASTER_USER',
    MASTER_PASSWORD='$MASTER_PASSWORD',
    MASTER_LOG_FILE='$MASTER_LOG_FILE',
    MASTER_LOG_POS=$MASTER_LOG_POS;
  START SLAVE;
EOSQL

echo "Esclavo configurado correctamente."
