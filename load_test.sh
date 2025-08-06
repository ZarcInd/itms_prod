#!/bin/bash

HOST="172.21.208.1"  # Your Windows host IP
PORT="8080"
MESSAGE="Hello Server"
CONNECTIONS=4000

for i in $(seq 1 $CONNECTIONS); do
  echo "$MESSAGE-$i" | nc "$HOST" "$PORT" > /dev/null &
done

wait
echo "Sent $CONNECTIONS requests."
