
<?php
if(session_status() !== PHP_SESSION_ACTIVE){
	session_start();
}


class Database
{

    private $db;

    private $TABLE_USER = "user";
    private $U_ID = "user.uid";
    private $U_NAME = "user.name";
    private $U_MAIL = "user.mail";
    private $U_PASSWORD = "user.password";
    private $U_TIMEADDED = "user.timeadded";
    private $U_TIMEMODIFIED = "user.timemodified";
    private $U_ISADMIN = "user.is_admin";

    private $TABLE_CHAT = "chat";
    private $C_ID = "chat.cid";
    private $C_NAME = "chat.name";
    private $C_TIMEADDED = "chat.timeadded";
    private $C_TIMEMODIFIED = "chat.timemodified";

    private $TABLE_USER_IS_IN_CHAT = "user_is_in_chat";
    private $UIIC_ID = "user_is_in_chat.uiicid";
    private $UIIC_CID = "user_is_in_chat.cid";
    private $UIIC_UID = "user_is_in_chat.uid";
    private $UIIC_LINK = "user_is_in_chat.link";
    private $UIIC_TIMEADDED = "user_is_in_chat.timeadded";
    private $UIIC_TIMEMODIFIED = "user_is_in_chat.timemodified";
    private $UIIC_DELETED = "user_is_in_chat.deleted";

    private $TABLE_MESSAGE = "message";
    private $M_ID = "message.mid";
    private $M_UIICID = "message.uiicid";
    private $M_MESSAGE = "message.message";
    private $M_TIMEADDED = "message.timeadded";
    private $M_TIMEMODIFIED = "message.timemodified";

    private $TABLE_IMAGES = "images";
    private $I_UID = "images.uid";
    private $I_CID = "images.cid";
    private $I_IMGDATA ="images.imgdata";
    private $I_IMGNAME = "images.imgname";

    function __construct()
    {
        include("config.php");

        try {
            $this->db = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB . ';charset=utf8', MYSQL_USER, MYSQL_PASSWORD);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function getAllUsers()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_USER}");

            if ($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function userExists($name, $email)
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->U_ID}
					FROM {$this->TABLE_USER}
					WHERE {$this->U_NAME} LIKE :name
					OR {$this->U_MAIL} = :email");

            if ($stmt->execute(array(':name' => $name, ':email' => $email))) {
                return $stmt->rowCount() > 0;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function proofPassword($name, $password)
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->U_PASSWORD}
					FROM {$this->TABLE_USER}
					WHERE {$this->U_NAME} LIKE :name");

            if ($stmt->execute(array(':name' => $name))) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (sizeof($result) <= 0) {
                    return 0;
                }

                return password_verify($password, $result[0]["password"]);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['name']) === true;
    }

    public function login($name, $password)
    {
        if ($this->proofPassword($name, $password)) {
            $_SESSION['name'] = $name;
            return true;
        } else {
            return false;
        }
    }

    public function logout()
    {
        session_destroy();
        unset($_SESSION['name']);
        return true;
    }

    public function register($name, $email, $password)
    {
        try {
            $pw = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_USER}
					({$this->U_NAME}, {$this->U_MAIL}, {$this->U_PASSWORD}, {$this->U_ISADMIN})
					VALUES (:name, :email, :password, :isAdmin)");

            if ($stmt->execute(array(':name' => $name, ':email' => $email, ':password' => $pw, ':isAdmin' => 0))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function changePasswordByEmail($email, $password)
    {
     try {
         $pw = password_hash($password, PASSWORD_DEFAULT);
         $stmt = $this->db->prepare("UPDATE {$this->TABLE_USER} SET {$this->U_PASSWORD} = :password 
         WHERE {$this->U_MAIL} = :email");

         if ($stmt->execute(array(':password' => $pw, ':email' => $email))) {
             return true;
         } else {
             return false;
         }
     } catch (PDOException $e) {
         return "Error: " . $e->getMessage();
     }
    }

    public function getAllChatsFromUser($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT {$this->UIIC_CID} AS cid, {$this->C_NAME} AS chatname, GROUP_CONCAT({$this->U_NAME}) AS members
					FROM {$this->TABLE_USER_IS_IN_CHAT}
					JOIN user ON ({$this->UIIC_UID} = {$this->U_ID})
					JOIN chat ON ({$this->UIIC_CID} = {$this->C_ID})
					WHERE {$this->UIIC_CID} IN (
					SELECT {$this->C_ID} FROM {$this->TABLE_CHAT}, {$this->TABLE_USER_IS_IN_CHAT}
					JOIN {$this->TABLE_USER} ON ({$this->UIIC_UID} = {$this->U_ID})
					WHERE {$this->U_ID} = :userId
					GROUP BY {$this->C_ID})
					GROUP BY {$this->UIIC_CID}
					ORDER BY {$this->UIIC_ID}");

            if ($stmt->execute(array(':userId' => $userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getAllChatsFromCurrentUser()
    {
        try {
            $currentUser = $this->getCurrentUser();
            $stmt = $this->db->prepare("SELECT {$this->C_ID} AS cid, {$this->C_NAME} AS chatname, GROUP_CONCAT(distinct {$this->U_NAME}) AS members
					FROM {$this->TABLE_CHAT}, {$this->TABLE_USER_IS_IN_CHAT}, {$this->TABLE_USER}
					WHERE {$this->UIIC_CID} = {$this->C_ID}
					AND {$this->UIIC_UID} = {$this->U_ID}
					GROUP BY {$this->C_ID}
					HAVING members LIKE '%$currentUser%'");

            if ($stmt->execute()) {
                //return $stmt;
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
                //return $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getCurrentUser()
    {
        return $_SESSION['name'];
    }

    public function getAllMessagesFromChat($chatId)
    {
        try {
            /*
            SELECT m.message, u.name, m.timeadded,
            DATEDIFF(NOW(), m.timeadded) AS date
            FROM message AS m
            JOIN user_is_in_chat AS uiic on (m.uiicid = uiic.uiicid)
            JOIN user AS u on (uiic.uid = u.uid)
            JOIN chat AS c on (uiic.cid = c.cid)
            WHERE uiic.cid = 2
            ORDER BY m.timeadded
            */
            $stmt = $this->db->prepare("SELECT {$this->M_UIICID}, {$this->UIIC_UID}, {$this->M_MESSAGE}, {$this->M_TIMEADDED}, DATEDIFF(NOW(), {$this->M_TIMEADDED}) AS datediff, DATE_FORMAT({$this->M_TIMEADDED}, '%d.%m.%Y') AS date, DATE_FORMAT({$this->M_TIMEADDED}, '%h:%i') AS time, {$this->U_ID}, {$this->U_NAME}
					FROM {$this->TABLE_MESSAGE}
					JOIN {$this->TABLE_USER_IS_IN_CHAT} ON ({$this->M_UIICID} = {$this->UIIC_ID})
					JOIN {$this->TABLE_USER} ON ({$this->UIIC_UID} = {$this->U_ID})
					WHERE {$this->UIIC_CID} = :chatid
					ORDER BY datediff DESC");

            if ($stmt->execute(array(':chatid' => $chatId))) {
                $res = array();

                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $message) {
                    if ($message['datediff'] == 0) {
                        // Nachricht heute
                        if (!array_key_exists('Heute', $res)) {
                            $res['Heute'] = array();
                        }
                        array_push($res['Heute'], $message);
                    } else if ($message['datediff'] == 1) {
                        // Nachricht gestern
                        if (!array_key_exists('Gestern', $res)) {
                            $res['Gestern'] = array();
                        }
                        array_push($res['Gestern'], $message);
                    } else {
                        // sonstige Nachrichten
                        if (!array_key_exists($message['date'], $res)) {
                            $res[$message['date']] = array();
                        }
                        array_push($res[$message['date']], $message);
                    }
                }

                return $res;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getUserID()
    {
        try {
            //nutze hier extra "as '0'" da ich sonst das Array der Ergebnissmenge so ansprechen müsste  $userid[0]['uid']; //print_r($userid); zur ausgabe

            $userName = $this->getCurrentUser();
            $stmt = $this->db->prepare("SELECT user.uid as '0' FROM user WHERE user.name = :username");
            if ($stmt->execute(array(':username' => $userName))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getUserID_v2()
    {
        try {
            $userName = $this->getCurrentUser();
            $stmt = $this->db->prepare("SELECT user.uid FROM user WHERE user.name = :username");
            if ($stmt->execute(array(':username' => $userName))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['uid'];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getEmail($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT user.mail as '0' FROM user WHERE user.uid = :userID");
            if ($stmt->execute(array(':userID' => $userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getUsername($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT user.name as '0' FROM user WHERE user.uid = :userID");
            if ($stmt->execute(array(':userID' => $userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function writeMessage($chatId, $userId, $message)
    {
        try {
            /*
            INSERT INTO message
            (uiicid, message)
            SELECT user_is_in_chat.uiicid, 'testmessage'
            FROM user_is_in_chat
            WHERE user_is_in_chat.cid = 2
            AND user_is_in_chat.uid = 3
            */
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_MESSAGE}
					({$this->M_UIICID}, {$this->M_MESSAGE})
					SELECT {$this->UIIC_ID}, :message
					FROM {$this->TABLE_USER_IS_IN_CHAT}
					WHERE {$this->UIIC_CID} = :chatId
					AND {$this->UIIC_UID} = :userId");

            if ($stmt->execute(array(':message' => $message, ':chatId' => $chatId, ':userId' => $userId))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getInvitationLink($chatId) {
        try {
            $stmt = $this->db->prepare("SELECT {$this->UIIC_LINK} AS link
                FROM {$this->TABLE_USER_IS_IN_CHAT}
                WHERE {$this->UIIC_CID} = :chatId
                AND {$this->UIIC_UID} = :userId");

            $userId = $this->getUserID_v2();

            if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getLinkData($link) {
        try {
            $stmt = $this->db->prepare("SELECT {$this->UIIC_UID} AS uid, {$this->UIIC_CID} AS cid
                FROM {$this->TABLE_USER_IS_IN_CHAT}
                WHERE {$this->UIIC_LINK} LIKE :link");

            if ($stmt->execute(array(':link' => $link))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function joinChat($chatId) {
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_USER_IS_IN_CHAT}
                    ({$this->UIIC_UID}, {$this->UIIC_CID}, {$this->UIIC_LINK})
                    VALUES(:userId, :chatId, :link)
                    ON DUPLICATE KEY UPDATE deleted = 0");

            $link = md5(rand(0,1000));

            if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $this->getUserID_v2(), ':link' => $link))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function createChat($chatName) {
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_CHAT}
                    ({$this->C_NAME})
                    VALUES (:chatName)");

            if ($stmt->execute(array(':chatName' => $chatName))) {
                $chatId = $this->db->lastInsertId();

                $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_USER_IS_IN_CHAT}
                    ({$this->UIIC_CID}, {$this->UIIC_UID}, {$this->UIIC_LINK})
                    VALUES (:chatId, :userId, :link)");

                $link = md5(rand(0,1000));
                $userId = $this->getUserID_v2();
                
                if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $userId, ':link' => $link))) {
                    return $this->db->lastInsertId();
                }
                else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function createRandomChat() {
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_CHAT}
                    ({$this->C_NAME})
                    VALUES ('Random-Chat')");

            if ($stmt->execute()) {
                $chatId = $this->db->lastInsertId();

                $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_USER_IS_IN_CHAT}
                    ({$this->UIIC_CID}, {$this->UIIC_UID}, {$this->UIIC_LINK}) VALUES (:chatId, :userId1, :link1);

                    INSERT INTO {$this->TABLE_USER_IS_IN_CHAT}
                    ({$this->UIIC_CID}, {$this->UIIC_UID}, {$this->UIIC_LINK})
                    SELECT :chatId, {$this->UIIC_UID}, :link1 FROM {$this->TABLE_USER_IS_IN_CHAT}
                    WHERE {$this->UIIC_UID} != :userId1
                    ORDER BY RAND() LIMIT 1;");

                $link1 = md5(rand(0,1000));
                $link2 = md5(rand(0,1000));
                $userId = $this->getUserID_v2();
                
                if ($stmt->execute(array(':chatId' => $chatId, ':userId1' => $userId, ':link1' => $link1, ':link2' => $link2))) {
                    $stmt->closeCursor();
                    return $this->getUserNameFromUserIsInChatId($this->db->lastInsertId());
                }
                else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function deleteChat($chatId)
    {
        try {
            /*
             * UPDATE `user_is_in_chat` SET `deleted`=1 WHERE `cid`= 1 AND `uid` 5
            */
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_USER_IS_IN_CHAT}
		            SET {$this->UIIC_DELETED} = 1
					WHERE {$this->UIIC_CID} = :chatId
					AND {$this->UIIC_UID} = :userId");
            $userId = $this->getUserID_v2();
            if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $userId))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function isChatDeletedForUser($chatId)
    {
        try {
            /*
             * SELECT `deleted` FROM `user_is_in_chat` WHERE `cid` = 1 AND `uid` = 1
            */
            $stmt = $this->db->prepare("SELECT {$this->UIIC_DELETED} 
            		FROM {$this->TABLE_USER_IS_IN_CHAT}
					WHERE {$this->UIIC_CID} = :chatId
					AND {$this->UIIC_UID} = :userId");
            $userId = $this->getUserID();
            $userId = $userId[0][0];
            if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $userId))) {
                $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $deleted = $deleted[0]['deleted'];
                return $deleted;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getMembersOfChat($chatId)
    {
        try {
            /*
             * SELECT user.name, user_is_in_chat.uid
             * FROM user_is_in_chat
             * JOIN USER on user_is_in_chat.uid = USER.uid
             * WHERE user_is_in_chat.cid=3 AND NOT(user_is_in_chat.deleted)
            */
            $stmt = $this->db->prepare("SELECT {$this->U_NAME} 
            		FROM {$this->TABLE_USER_IS_IN_CHAT}
            		JOIN {$this->TABLE_USER} ON {$this->UIIC_UID}={$this->U_ID}
					WHERE {$this->UIIC_CID} = :chatId
					AND NOT ({$this->UIIC_DELETED})");
            if ($stmt->execute(array(':chatId' => $chatId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    function userCount(){
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_ID} FROM {$this->TABLE_USER}");
            if($stmt->execute()){
                return $stmt->rowCount();
            }else return false;
        }catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function msgCountPerTime($hours){
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_MESSAGE} WHERE {$this->M_TIMEMODIFIED} >= DATE_SUB(NOW(), INTERVAL ? HOUR)");
            if($stmt->execute((array($hours)))){
                return $stmt->rowCount();
            }else {
                return false;
            }
        }catch(PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function totalChatMessages(){
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_MESSAGE}");
            if($stmt->execute()){
                return $stmt->rowCount();
            }else {
                return false;
            }
        }catch(PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function recentlyActive(){
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_MESSAGE} WHERE {$this->M_TIMEMODIFIED} >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) GROUP BY {$this->M_UIICID}");
            if($stmt->execute()){
                return $stmt->rowCount();
            }else {
                return false;
            }
        }catch(PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function onlineUsers(){
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_USER} WHERE {$this->U_TIMEMODIFIED} >= DATE_SUB(NOW(), INTERVAL 10 SECOND)");
            if($stmt->execute()){
                return $stmt->rowCount();
            }else {
                return false;
            }
        }catch(PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function isUserAdmin (){
        /*
         * SELECT USER.is_admin FROM `user` WHERE user.uid = 1
         */
        $userid = $this->getUserID();
        $userid = $userid[0][0];
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_ISADMIN} AS '0' FROM {$this->TABLE_USER} WHERE {$this->U_ID}= :userId");
            if($stmt->execute(array(':userId' => $userid))){
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result[0][0];
            }
        }catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function ping(){
        try {
            /*
             * UPDATE `user` SET USER.timemodified=CURRENT_TIMESTAMP   WHERE USER.uid = 1
            */
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_USER}
		            SET {$this->U_TIMEMODIFIED} = CURRENT_TIMESTAMP 
					WHERE {$this->U_ID} = :userId");
            $userId = $this->getUserID();
            $userId = $userId[0][0];
            if ($stmt->execute(array(':userId' => $userId))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    function getCurrentUser_v2(){
        /*
         * SELECT USER.name FROM `user` WHERE user.uid = 1
         */
        $userid = $this->getUserID();
        $userid = $userid[0][0];
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_NAME} AS '0' FROM {$this->TABLE_USER} WHERE {$this->U_ID}= :userId");
            if($stmt->execute(array(':userId' => $userid))){
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result[0][0];
            }
        }catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function createPictureFromUser($name,$image){
        try {
            /*
             * INSERT INTO images (uid,imdata) VALUES (:uid,:imdata)
            */
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_IMAGES}
		            ({$this->I_UID},{$this->I_IMGNAME},{$this->I_IMGDATA}) VALUES (:userId,:imgname,:imgdata)");
            $userId = $this->getUserID();
            $userId = $userId[0][0];
            if ($stmt->execute(array(':userId' => $userId,':imgname' => $name,':imgdata' => $image))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    function showPictureFromCurrentUser(){
        try{
            $userId = $this->getUserID();
            $userId = $userId[0][0];
            $stmt = $this->db->prepare("SELECT {$this->I_IMGNAME},{$this->I_IMGDATA} FROM {$this->TABLE_IMAGES} WHERE {$this->I_UID}=:userId");
            if($stmt->execute(array(':userId' => $userId))){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }else return false;
        }catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function showPictureByUserId($userId){
        try{
            $stmt = $this->db->prepare("SELECT {$this->I_IMGNAME},{$this->I_IMGDATA} FROM {$this->TABLE_IMAGES} WHERE {$this->I_UID}=:userId");
            if($stmt->execute(array(':userId' => $userId))){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }else return false;
        }catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function getUserIdByName($username){
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_ID} AS '0' FROM {$this->TABLE_USER} WHERE {$this->U_NAME}=:username");
            if($stmt->execute(array(':username' => $username))){
                $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $return[0][0];
            }else return false;
        }catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function getUserNameById($userId) {
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_NAME} AS name
                    FROM {$this->TABLE_USER}
                    WHERE {$this->U_ID} = :userId");
            if($stmt->execute(array(':userId' => $userId))){
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['name'];
            }
            else {
                return false;
            }
        } catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    public function getUserNameFromUserIsInChatId($userIsInChatId)
    {
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_NAME} AS name
                    FROM {$this->TABLE_USER}, {$this->TABLE_USER_IS_IN_CHAT}
                    WHERE {$this->UIIC_ID} = :userIsInChatId AND {$this->UIIC_UID} = {$this->U_ID}");
            
            if($stmt->execute(array(':userIsInChatId' => $userIsInChatId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['name'];
            }
            else {
                return false;
            }
        } catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function getChatnameById($chatId) {
        try{
            $stmt = $this->db->prepare("SELECT {$this->C_NAME} AS name
                    FROM {$this->TABLE_CHAT} WHERE
                    {$this->C_ID} = :chatId");
            if($stmt->execute(array(':chatId' => $chatId))){
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['name'];
            }
            else {
                return false;
            }
        } catch (PDOException $e){
            return "Error: ".$e->getMessage();
        }
    }

    function updatePictureFromCurrentUser($name,$image){
        try {
            /*
             * UPDATE images SET imgdata=:imgdata,imgname=:imgname WEHRE uid=:userId
            */
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_IMAGES}
		            SET {$this->I_IMGNAME}=:imgname,{$this->I_IMGDATA}=:imgdata WHERE {$this->I_UID}=:userId");
            $userId = $this->getUserID();
            $userId = $userId[0][0];
            if ($stmt->execute(array(':userId' => $userId,':imgname' => $name,':imgdata' => $image))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            return "Error: " . $e->getMessage();
        }
    }

    function haveUserPicture(){
        $picture = $this->showPictureFromCurrentUser();
        $count = count($picture);
        if($count>0){
            return true;
        }else return false;
    }
}
