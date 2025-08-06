#!/bin/bash

# Check if a filename is provided
if [ $# -ne 3 ]; then
    echo "Usage: $0 <filename> <telnet_host> <telnet_port>"
    exit 1
fi

FILE="$1"
TELNET_HOST="$2"
TELNET_PORT="$3"

# Open a Telnet session and send lines
while IFS= read -r line
do
    echo "Sending: $line"
    echo "$line" | nc $TELNET_HOST $TELNET_PORT
    # sleep 1  # Optional delay between commands
done < "$FILE"
