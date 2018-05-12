
<?php
if(session_status() !== PHP_SESSION_ACTIVE){
	session_start();
}


class Database {

	private $db;

	private $TABLE_USER = "user";
	private $U_ID = "user.uid";
	private $U_NAME = "user.name";
	private $U_MAIL = "user.mail";
	private $U_PASSWORD = "user.password";
	private $U_TIMEADDED = "user.timeadded";
	private $U_TIMEMODIFIED = "user.timemodified";

	private $TABLE_CHAT = "chat";
	private $C_ID = "chat.cid";
	private $C_NAME = "chat.name";
	private $C_TIMEADDED = "chat.timeadded";
	private $C_TIMEMODIFIED = "chat.timemodified";

	private $TABLE_USER_IS_IN_CHAT = "user_is_in_chat";
	private $UIIC_ID = "user_is_in_chat.uiicid";
	private $UIIC_CID = "user_is_in_chat.cid";
	private $UIIC_UID = "user_is_in_chat.uid";
	private $UIIC_TIMEADDED = "user_is_in_chat.timeadded";
	private $UIIC_TIMEMODIFIED = "user_is_in_chat.timemodified";

	private $TABLE_MESSAGE = "message";
	private $M_ID = "message.mid";
    private $M_UIICID = "message.uiicid";
	private $M_MESSAGE = "message.message";
	private $M_TIMEADDED = "message.timeadded";
	private $M_TIMEMODIFIED = "message.timemodified";

	function __construct() {
		include("config.php");

		try {
			$this->db = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB . ';charset=utf8', MYSQL_USER, MYSQL_PASSWORD);
		}
		catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
	
	public function getAllUsers() {
		try {
			$stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_USER}");
			
			if($stmt->execute()) {
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			return "Error: " . $e->getMessage();
		}
	}
	
	public function userExists($name, $email) {
		try {
			$stmt = $this->db->prepare("SELECT {$this->U_ID}
					FROM {$this->TABLE_USER}
					WHERE {$this->U_NAME} LIKE :name
					OR {$this->U_MAIL} = :email");
			
			if($stmt->execute(array(':name' => $name, ':email' => $email))) {
				return $stmt->rowCount() > 0;
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			return "Error: " . $e->getMessage();
		}
	}
	
	public function proofPassword($name, $password) {
		try {
			$stmt = $this->db->prepare("SELECT {$this->U_PASSWORD}
					FROM {$this->TABLE_USER}
					WHERE {$this->U_NAME} LIKE :name");
			
			if($stmt->execute(array(':name' => $name))) {
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				if(sizeof($result) <= 0) {
					return 0;
				}
				
				return password_verify($password, $result[0]["password"]);
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			return "Error: " . $e->getMessage();
		}
	}
	
	public function isLoggedIn() {
		return isset($_SESSION['name']) === true;
	}
	
	public function login($name, $password) {
		if($this->proofPassword($name, $password)) {
			$_SESSION['name'] = $name;
			return true;
		}
		else {
			return false;
		}
	}
	
	public function logout() {
		session_destroy();
		unset($_SESSION['name']);
		return true;
	}
	
	public function register($name, $email, $password) {
		try {
			$pw = password_hash($password, PASSWORD_DEFAULT);
			
			$stmt = $this->db->prepare("INSERT INTO {$this->TABLE_USER}
					({$this->U_NAME}, {$this->U_MAIL}, {$this->U_PASSWORD})
					VALUES (:name, :email, :password)");
			
			if($stmt->execute(array(':name' => $name, ':email' => $email, ':password' => $pw))) {
				return $this->db->lastInsertId();
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			return "Error: " . $e->getMessage();
		}
	}
	
	public function getAllChatsFromUser($userId) {
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
			
			if($stmt->execute(array(':userId' => $userId))) {
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			return "Error: " . $e->getMessage();
		}
	}

	public function getAllChatsFromCurrentUser() {
		try {
			$currentUser = $this->getCurrentUser();
			$stmt = $this->db->prepare("SELECT {$this->C_ID} AS cid, {$this->C_NAME} AS chatname, GROUP_CONCAT({$this->U_NAME}) AS members
					FROM {$this->TABLE_CHAT}, {$this->TABLE_USER_IS_IN_CHAT}, {$this->TABLE_USER}
					WHERE {$this->UIIC_CID} = {$this->C_ID}
					AND {$this->UIIC_UID} = {$this->U_ID}
					GROUP BY {$this->C_ID}
					HAVING members LIKE '%$currentUser%'");

			if($stmt->execute()) {
				//return $stmt;
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
				//return $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			return "Error: " . $e->getMessage();
		}
	}

	public function getCurrentUser() {
		return $_SESSION['name'];
	}

	public function getAllMessagesFromChat($chatId) {
		try {
			/*
			SELECT m.message, m.timeadded, u.name
			FROM message AS m
			JOIN user_is_in_chat AS uiic on (m.uiicid = uiic.uiicid)
			JOIN user AS u on (uiic.uid = u.uid)
			JOIN chat AS c on (uiic.cid = c.cid)
			WHERE uiic.cid = 2
			*/
			$stmt = $this->db->prepare("SELECT {$this->M_UIICID}, {$this->UIIC_UID}, {$this->M_MESSAGE}, {$this->M_TIMEADDED}, {$this->U_ID}, {$this->U_NAME}
					FROM {$this->TABLE_MESSAGE}
					JOIN {$this->TABLE_USER_IS_IN_CHAT} ON ({$this->M_UIICID} = {$this->UIIC_ID})
					JOIN {$this->TABLE_USER} ON ({$this->UIIC_UID} = {$this->U_ID})
					WHERE {$this->UIIC_CID} = :chatid");
			
			if($stmt->execute(array(':chatid' => $chatId))) {
				return $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			else {
				return false;
			}
		}
		catch(PDOException $e) {
			return "Error: " . $e->getMessage();
		}

	}

    public function getUserID(){
        try{
			//nutze hier extra "as '0'" da ich sonst das Array der Ergebnissmenge so ansprechen müsste  $userid[0]['uid']; //print_r($userid); zur ausgabe
			
            $userName = $this->getCurrentUser();
            $stmt = $this->db->prepare("SELECT user.uid as '0' FROM user WHERE user.name = :username");
            if($stmt->execute(array(':username' => $userName))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                return false;
            }
        }
        catch (PDOException $e){
            return "Error: " . $e->getMessage();
        }
    }

    public function getEmail($userId){
        try{
            $stmt = $this->db->prepare("SELECT user.mail as '0' FROM user WHERE user.uid = :userID");
            if($stmt->execute(array(':userID' => $userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                return false;
            }
        }
        catch (PDOException $e){
            return "Error: " . $e->getMessage();
        }
    }

    public function getUsername($userId){
        try{
            $stmt = $this->db->prepare("SELECT user.name as '0' FROM user WHERE user.uid = :userID");
            if($stmt->execute(array(':userID' => $userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                return false;
            }
        }
        catch (PDOException $e){
            return "Error: " . $e->getMessage();
        }
    }
	
	public function writeMessage($chatId, $userId, $message) {
		try{
			/*
			INSERT INTO message
			(uiicid, message)
			SELECT user_is_in_chat.uiicid, 'testmessage'
			FROM user_is_in_chat
			WHERE user_is_in_chat.cid = 2
			AND user_is_in_chat.uid = 3
			*/
			$stmt = $this->db->prepare("INSERT INTO user_is_in_chat (cid,uid) VALUES (?,?)");
            $ret = $stmt->execute(array($chatId,$userId));
            if($ret == 0) throw new Exception('Nachricht kann nicht vom Server gespeicher werden.');
            else {
                $uiicid = $this->db->lastInsertId('uiicid');
                $stmt_2 = $this->db->prepare("INSERT INTO message (uiicid,message)VALUES (?,?)");
                $ret_2 = $stmt_2->execute(array($uiicid,$message));
                if($ret_2==0) throw new Exception('Nachricht kann nicht vom Server gespeicher werden.');
                else return $this->db->lastInsertId();
            }
            /*$stmt = $this->db->prepare("INSERT INTO {$this->TABLE_MESSAGE}
					({$this->M_UIICID}, {$this->M_MESSAGE})
					SELECT {$this->UIIC_ID}, :message
					FROM {$this->TABLE_USER_IS_IN_CHAT}
					WHERE {$this->UIIC_CID} = :chatId
					AND {$this->UIIC_UID} = :userId");

            /*$stmt = $this->db->prepare("INSERT INTO {$this->TABLE_MESSAGE}
					({$this->M_UIICID}, {$this->M_MESSAGE})
					SELECT {$this->UIIC_ID}, '%$message%'
					FROM {$this->TABLE_USER_IS_IN_CHAT}
					WHERE {$this->UIIC_CID} = '%$chatId%'
					AND {$this->UIIC_UID} = '%$userId%'");*/

			/*if($stmt->execute(array(':message' => $message, ':chatId' => $chatId, ':userId' => $userId))) {
                    return $this->db->lastInsertId();
            }
            else {
                return false;
            }*/
        }
        catch (PDOException $e){
            return "Error: " . $e->getMessage();
        }
	}
}
?>
