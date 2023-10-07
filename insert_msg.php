<?php
require 'vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Http\HttpServer;
use Ratchet\Http\HttpServerInterface;


class Chat implements MessageComponentInterface
{
    protected $clients;
    protected $pdo;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;

        // connect db
         $dsn = 'mysql:host=localhost;dbname=demo_database';
         $username = 'root';
         $password = '';
 
         try {
             $this->pdo = new \PDO($dsn, $username, $password);
         } catch (\PDOException $e) {
            // echo "Database connection error: " . $e->getMessage() . "\n";
            // die();
         }
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
       // echo "New connection! ({$conn->resourceId})\n";

    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        $u_id_to = '9774';
        $sql = "INSERT INTO msg_details (u_id_from, u_id_to, msg) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$from->resourceId, $u_id_to, $msg]);

        // Broadcast the message to all connected clients
        foreach ($this->clients as $client) {
            $client->send($msg);
        }
        
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function insert_to_db(ConnectionInterface $from,$msg){
        
        $from = $from->resourceId;
        $msg = $msg;
        $to =  '9612953928';
        
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "demo_database";
    
        try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $data = array($from,$to,$msg);
        $statement = $conn->prepare('INSERT INTO msg_details (u_id_from, u_id_to, msg) VALUES (?, ?, ?)');
        $statement->execute($data);
        
        } catch(PDOException $e) {
        
        }
        
        $conn = null;
    
    
    }
}



$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    81,
    '192.168.233.55'
);

$server->run();

?>