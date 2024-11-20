#!/bin/sh

# Rutas locales dentro del contenedor de backups
MASTER_DATA_PATH="/data/master"
SLAVE_DATA_PATH="/data/slave"
LOCAL_MASTER_BACKUP="/backups/master"
LOCAL_SLAVE_BACKUP="/backups/slave"

# Backup Master
echo "Backing up Master database..."
rsync -avz $MASTER_DATA_PATH/ $LOCAL_MASTER_BACKUP/
echo "Master backup completed."

# Backup Slave
echo "Backing up Slave database..."
rsync -avz $SLAVE_DATA_PATH/ $LOCAL_SLAVE_BACKUP/
echo "Slave backup completed."
