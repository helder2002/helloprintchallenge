<?php
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();

$channel->queue_declare('request_password', false, false, false, false);


echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "test";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user = $msg->body;

    $sql = "SELECT password,email FROM users where username = '$user'";

    echo "Query:  ".$sql;

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            echo "password: " . $row["password"]. " Email: " . $row["email"]. " <br>";
            echo "Email with password sent to  ".$row["email"];
        }
    } else {
        echo "No result returned";
    }
    $conn->close();

    echo ' [x] Received ', $msg->body, "\n";



};

$channel->basic_consume('request_password', '', false, true, false, false, $callback);

while (count($channel->callbacks)) {
    $channel->wait();
}