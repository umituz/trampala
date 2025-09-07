#!/bin/bash
# docker/init-database.sh

set -e
set -u

echo "Trampala database initialization started"

# Create database for trampala
if [ -n "$POSTGRES_USER" ]; then
    echo "Creating trampala_api database..."
    
    psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
        CREATE DATABASE trampala_api;
        GRANT ALL PRIVILEGES ON DATABASE trampala_api TO "$POSTGRES_USER";
EOSQL
    
    echo "Trampala database created successfully!"
else
    echo "POSTGRES_USER is not set"
fi

echo "Database initialization completed."