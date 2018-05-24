<?php
include("./db.php");
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
        case "sendmessage":
            $user_id = $Database->getUserID();
            $user_id = $user_id[0][0];
            $return_array =$Database->writeMessage($p['chat_id'],$user_id,$p['msg']);
            break;
        case "getAllChatsFromCurrentUser":
            $return_array = $Database->getAllChatsFromCurrentUser();
            break;
        case "getCurrentUser":
            $return_array = $Database->getCurrentUser();
            break;
        case "logout":
            $return_array = $Database->logout();
            break;
        case "getAllMessagesFromChat":
            $return_array = $Database->getAllMessagesFromChat($p['chat_id']);
            break;
        case "chatloeschen":
            $return_array = $Database->deleteChat($p['chat_id']);
            break;
        case "ping":
            $return_array = $Database->ping();
            break;

        case "getinvitationlink":
            $return_array = $Database->getInvitationLink($p['chat_id']);
            break;
        case "createchat":
            $return_array = $Database->createChat($p['chat_name']);
            break;

        default:
            throw new Exception('Instruktion nicht gefunden.');

    }}

} catch (Exception $e) {
    //Im Falle eines Fehlers wird im catch Block der Status inklusive der Fehlerbeschreibung als JSON übertragen.
    $return_array['status_log'] = $e->getMessage();
}

echo json_encode($return_array);