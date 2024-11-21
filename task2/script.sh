#!/bin/bash

ENV_FILE=".env"

echo "=== Проверка секретов в .env... ==="
if [ -f "$ENV_FILE" ]; then
    SUSPICIOUS_ENTRIES=$(grep -Ei 'password|secret|key|token' "$ENV_FILE")
    if [ -z "$SUSPICIOUS_ENTRIES" ]; then
        echo "  Секреты в .env не найдены."
    else
        echo " Найдены возможные секреты в $ENV_FILE:"
        echo "$SUSPICIOUS_ENTRIES"
    fi
else
    echo "  Файл .env не найден."
fi
