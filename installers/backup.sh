#!/bin/bash

BACKUP_FILE="/tmp/backup.tar.gz"
tar -czf "$BACKUP_FILE" -T /etc/backup.list -T /etc/checkbox_backup.list
