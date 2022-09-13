<?php ('core/initialize.php'); ?>


<h4>Mikrotik Devices</h4>
<?php 

    $user = $_SESSION['id'];
    $sql = "SELECT * FROM devices WHERE userid = ?";
    $stmt = $db->prepare($sql);

    $result = $stmt->execute([$user]);

    
    if($result){

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(!empty($data)){
            
            $tbl = '<table class="table table-bordered">';
            $tbl .= '<thead><tr><td>Server Ip</td><td>Username</td><td>Port</td><td>Connect</td></tr>';
            $tbl .= '<tbody>';
            foreach($data as $d){
                
                $tbl .= '<tr>';
                $tbl .= '<td>' . ' ' . $d['serverip'] . ' ' .  '</td>';
                $tbl .= '<td>' . ' ' . $d['musername'] . ' ' .  '</td>';
                $tbl .= '<td>' . ' ' . $d['mport'] . ' ' .  '</td>';
                $tbl .= '<td><a href="index.php?connect='. $d['id'] . '" class="btn btn-block btn-outline-success">Connect</a></td>';
                $tbl .= '</tr>';
            }
            $tbl .= '</tbody>';
            $tbl .= '</table>';

            echo $tbl;
        }else{
            die('No data');
        }
    }else{
        die('There were errors while getting devices.');
    }

?>



