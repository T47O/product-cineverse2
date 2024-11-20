#!/bin/sh

# Instalar rsync
apk add --no-cache rsync

# Configurar tareas en cron
echo '0 3 * * 0 rsync -avz /data/master/ /backups/master/' >> /etc/crontabs/root
echo '0 3 * * 0 rsync -avz /data/slave/ /backups/slave/' >> /etc/crontabs/root
echo '*/5 * * * * rsync -avz /data/binlogs/ /backups/binlogs/' >> /etc/crontabs/root

# Iniciar el demonio de cron
crond -f
