 <html>
     <head>
         <title>View</title>
     </head>
     <body>
       <b>Message Data</b>  
       <br> <br>
       <?php
 
            $servername = "localhost";
            $username = "thenorth_test_user";
            $password = "G3E[TR65@-77";
            $dbname = "thenorth_test_db";
            
             $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
         $stmt = $conn->prepare("SELECT sl_no, u_id_from, u_id_to, msg
                                                        FROM msg_details order by sl_no desc limit 10");
                                                        $stmt->execute();
                                                        $count = $stmt->rowCount();
                                                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($result as $val) {

                                                            echo $val['sl_no'].' --- '.$val['u_id_from'].' --- '.$val['u_id_to'].' --- '.$val['msg'].'<br>';
                                                        }
  
    

                                                        
                                                        ?>
     </body>
 </html>
 
 