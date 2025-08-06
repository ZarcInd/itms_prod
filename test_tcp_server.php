<?php
$address = '0.0.0.0';
$port = 9001; // Change if needed

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($sock, $address, $port) or die("Could not bind to $port\n");
socket_listen($sock);
echo "Listening on $port...\n";

while (true) {
    $client = socket_accept($sock);
    if ($client) {
        $input = socket_read($client, 9001);
        echo "Received: $input\n";

        $response = "Hello from server!\n";
        socket_write($client, $response);
        socket_close($client);
    }
}
