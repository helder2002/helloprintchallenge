<?php
include_once 'debug.php';
require_once __DIR__ . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$username = "";
$password = "";
if(isset($_REQUEST['operation']))
{
    if($_REQUEST['operation'] == 'rpassword') //Request Password
    {
        $user = $_REQUEST['username'];

        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('request_password', false, false, false, false);

        echo "Username:  ".$user." asks for password";

        $msg = new AMQPMessage($user);
        $channel->basic_publish($msg, '', 'request_password');


        echo " [x] Sent 'Hello World!'\n";
        $channel->close();
        $connection->close();
    }
    else if($_REQUEST['operation'] == 'login')
    {

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

        $user = $_REQUEST['username'];
        $pass = $_REQUEST['password'];

        $sql = "SELECT id FROM users where username = '$user' and password = '$pass' ";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                if($row['id'])
                {
                    echo $row['id'];
                }
                else
                {
                    echo false;
                }
            }
        } else {
           echo false;
        }
        $conn->close();
    }

    return;
}
else
{
    echo "Nothing to do";
    return;
}