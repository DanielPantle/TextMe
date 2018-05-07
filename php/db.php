
<?php
session_start();

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
	private $UINC_ID = "user_is_in_chat.uiicid";
	private $UINC_CID = "user_is_in_chat.uid";
	private $UINC_UID = "user_is_in_chat.cid";
	private $UINC_TIMEADDED = "user_is_in_chat.timeadded";
	private $UINC_TIMEMODIFIED = "user_is_in_chat.timemodified";
	
	private $TABLE_MESSAGE = "message";
	private $M_ID = "message.mid";
	private $M_CID = "message.cid";
	private $M_UID = "message.uid";
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
	
	public function userExists($email) {
		try {
			$stmt = $this->db->prepare("SELECT {$this->U_ID}
					FROM {$this->TABLE_USER}
					WHERE {$this->U_MAIL} = :email");
			
			if($stmt->execute(array(':email' => $email))) {
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
			/*$sql = "SELECT chat_user.chat_id, GROUP_CONCAT(user.name) FROM chat_user
					JOIN user ON (chat_user.user_id = user.id)
					WHERE chat_user.chat_id IN (
					SELECT chat.id FROM chat, chat_user
					JOIN user ON (chat_user.user_id = user.id)
					WHERE user.id = $userId
					GROUP BY chat.id)
					GROUP BY chat_user.chat_id
					ORDER BY chat_user.chat_id";*/
			$stmt = $this->db->prepare("SELECT {$this->UINC_CID}, GROUP_CONCAT({$this->U_NAME})
					FROM {$this->TABLE_USER_IS_IN_CHAT}
					JOIN user ON ({$this->UINC_UID} = {$this->U_ID})
					WHERE {$this->UINC_CID} IN (
					SELECT {$this->C_ID} FROM {$this->TABLE_CHAT}, {$this->TABLE_USER_IS_IN_CHAT}
					JOIN {$this->TABLE_USER} ON ({$this->UINC_UID} = {$this->U_ID})
					WHERE {$this->U_ID} = :userId
					GROUP BY {$this->C_ID})
					GROUP BY {$this->UINC_CID}
					ORDER BY {$this->UINC_ID}");
			
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
	
	public function getAllMessagesFromChat($chatId) {
		try {
			$stmt = $this->db->prepare("SELECT user.name, nachricht.text, nachricht.timestamp FROM nachricht
					JOIN user ON (nachricht.sender_id = user.id)
					WHERE nachricht.chat_id = :chatId");
			
			if($stmt->execute(array(':chatId' => $chatId))) {
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
}
?>
