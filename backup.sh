#!/bin/bash

set -e

# WordPress configuration
WP_CONFIG="./wp-config.php"
BACKUP_FILE="./local_backup.sql"

# Read database credentials from wp-config.php
DB_NAME=$(php -r "include '$WP_CONFIG'; echo DB_NAME;")
DB_USER=$(php -r "include '$WP_CONFIG'; echo DB_USER;")
DB_PASSWORD=$(php -r "include '$WP_CONFIG'; echo DB_PASSWORD;")
DB_HOST=$(php -r "include '$WP_CONFIG'; echo DB_HOST;")

echo "Backing up database: $DB_NAME"

export MYSQL_PWD="$DB_PASSWORD"

if mysqldump \
    --host="$DB_HOST" \
    --user="$DB_USER" \
    --single-transaction \
    --quick \
    --lock-tables=false \
    "$DB_NAME" > "$BACKUP_FILE"; then

    echo "Backup completed successfully!"
    echo "File: $(pwd)/local_backup.sql"
    ls -lh "$BACKUP_FILE"

else
    echo "ERROR: Database backup failed!" >&2
    rm -f "$BACKUP_FILE"
    exit 1
fi

unset MYSQL_PWD