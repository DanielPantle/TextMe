<?php
include("db.php");
$Database = new Database();
$return_array = array();
function done(){
    global $return_array;

    if(isset($return_array) and isset($return_array['status'])) return 1;
    else return 0;
}
$rawpostdata = file_get_contents("php://input");
$p = json_decode($rawpostdata,true);
try {
    if(is_null($p) and !done()) {
        throw new Exception('Fehlerhafte JSON.');
    }

    if(!isset($p['i']) and !done()) {
        throw new Exception('Instruktion fehlt.');
    }

    if (!done()){ switch ($p['i']) {

        //Erwartet die ID des Chats sowie die Nachricht
        case "send-message":
            $user_id = $Database->getUserID();
            $user_id = $user_id[0][0];
            $msg = $p['msg'];
            $chat_id = $p['chat_id'];
            //$return_array = $Database->writeMessage($p['chat_id'],$user_id,$p['msg']);
            $return_array =$Database->writeMessage($chat_id,$user_id,$msg);
            break;

        default:
            throw new Exception('Instruktion nicht gefunden.');

    }}

} catch (Exception $e) {
    //Im Falle eines Fehlers wird im catch Block der Status inklusive der Fehlerbeschreibung als JSON übertragen.
    $return_array['status_log'] = $e->getMessage();
}

echo json_encode($return_array);