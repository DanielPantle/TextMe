<?php
/**
 *  Session wird gestartet insofern sie noch nicht gestartet ist
 */
if(session_status() !== PHP_SESSION_ACTIVE){
    session_start();
}


/**
 * Class Database
 *    stellt Funktionen zur Verfügung
 *    um mit der Daten aus der Datenbank zu erhalten
 */
class Database
{

    /**
     * @var PDO
     */
    private $db;

    /**
     * @var string
     * Private Variabeln
     * Vereinfachen das Nutzen der Tabelle USER
     * in den einzelnen Funktionen
     * Vereinfacht das Ändern der Datenbank
     */
    private $TABLE_USER = "user";
    private $U_ID = "user.uid";
    private $U_NAME = "user.name";
    private $U_MAIL = "user.mail";
    private $U_PASSWORD = "user.password";
    private $U_TIMEADDED = "user.timeadded";
    private $U_TIMEMODIFIED = "user.timemodified";
    private $U_ISADMIN = "user.is_admin";

    /**
     * @var string
     * Private Variabeln
     * Vereinfachen das Nutzen der Tabelle Chat
     * in den einzelnen Funktionen
     * Vereinfacht das Ändern der Datenbank
     */
    private $TABLE_CHAT = "chat";
    private $C_ID = "chat.cid";
    private $C_NAME = "chat.name";
    private $C_TIMEADDED = "chat.timeadded";
    private $C_TIMEMODIFIED = "chat.timemodified";

    /**
     * @var string
     * Private Variabeln
     * Vereinfachen das Nutzen der Tabelle user_is_in_chat
     * in den einzelnen Funktionen
     * Vereinfacht das Ändern der Datenbank
     */
    private $TABLE_USER_IS_IN_CHAT = "user_is_in_chat";
    private $UIIC_ID = "user_is_in_chat.uiicid";
    private $UIIC_CID = "user_is_in_chat.cid";
    private $UIIC_UID = "user_is_in_chat.uid";
    private $UIIC_LINK = "user_is_in_chat.link";
    private $UIIC_TIMEADDED = "user_is_in_chat.timeadded";
    private $UIIC_TIMEMODIFIED = "user_is_in_chat.timemodified";
    private $UIIC_DELETED = "user_is_in_chat.deleted";
    private $UIIC_UNREADMESSAGE = "user_is_in_chat.unreadMessage";

    /**
     * @var string
     * Private Variabeln
     * Vereinfachen das Nutzen der Tabelle messsage
     * in den einzelnen Funktionen
     * Vereinfacht das Ändern der Datenbank
     */
    private $TABLE_MESSAGE = "message";
    private $M_ID = "message.mid";
    private $M_UIICID = "message.uiicid";
    private $M_MESSAGE = "message.message";
    private $M_TIMEADDED = "message.timeadded";
    private $M_TIMEMODIFIED = "message.timemodified";

    /**
     * @var string
     * Private Variabeln
     * Vereinfachen das Nutzen der Tabelle Images
     * in den einzelnen Funktionen
     * Vereinfacht das Ändern der Datenbank
     */
    private $TABLE_IMAGES = "images";
    private $I_UID = "images.uid";
    private $I_CID = "images.cid";
    private $I_IMGDATA ="images.imgdata";
    private $I_IMGNAME = "images.imgname";

    /**
     * Database constructor.
     */
    function __construct()
    {
        //config.php einbinden
        include("config.php");

        //try Catch block zum abfangen einer Möglichen Exception
        try {
            //Stellt Datenbankverbindung her
            //durch PDO (PHP Data Objects)
            //und den Definierten Host,Datenbankname,Username,Password aus "config.php"
            $this->db = new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB . ';charset=utf8', MYSQL_USER, MYSQL_PASSWORD);
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Prüft ob User Exisitert anhand name oder Email Adresse
     * @param $name
     * @param $email
     * @return bool|string
     */
    public function userExists($name, $email)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $stmt = $this->db->prepare("SELECT {$this->U_ID}
                    FROM {$this->TABLE_USER}
                    WHERE {$this->U_NAME} LIKE :name
                    OR {$this->U_MAIL} = :email");
            if ($stmt->execute(array(':name' => $name, ':email' => $email))) {
                //Git die Anzahl der Zeilen zurück solange Größer als 0
                return $stmt->rowCount() > 0;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Vergleicht eingegebene Passwort mit dem aus der Datenbank
     * @param $name
     * @param $password
     * @return bool|int
     */
    public function proofPassword($name, $password)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $stmt = $this->db->prepare("SELECT {$this->U_PASSWORD}
                    FROM {$this->TABLE_USER}
                    WHERE {$this->U_NAME} LIKE :name");
            if ($stmt->execute(array(':name' => $name))) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // wenn das Zurückgegebende Ergebnis kleiner oder gleich 0 ist gibt ein false zurück
                if (sizeof($result) <= 0) {
                    return 0;
                }
                //Überprüft das eingegebende Password und das Password in der Datenbank auf gleichheit
                return password_verify($password, $result[0]["password"]);
            } else {
                //gibt false zurpck falls SQL ausführen nicht geklappt hat
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Überprüft ob User eingeloggt ist
     * @return bool
     */
    public function isLoggedIn()
    {
        //wenn Session Variable gesetzt ist, ist User bereits eingeloggt --> gibt true zurück anderfalls false
        return isset($_SESSION['name']) === true;
    }

    /**
     * User wird "eingeloggt"
     * @param $name
     * @param $password
     * @return bool
     */
    public function login($name, $password)
    {
        //wenn eingegebende Password mit dem Password aus der Datenbank übereinstimmt
        if ($this->proofPassword($name, $password)) {
            //dann setzt er die Session variable name mit den aktuellen Benutzernan
            $_SESSION['name'] = $name;
            return true;
        } else {
            //wenn nicht gibt ein false zurück
            return false;
        }
    }

    /**
     * User wird "ausgeloggt"
     * @return bool
     */
    public function logout()
    {
        //zerstört die aktuelle Session
        session_destroy();
        //setzt die gesetze Session variable name zurück
        unset($_SESSION['name']);
        //gibt true zurück sobald er fertig ist
        return true;
    }


    /**
     * * User Registrierungs Funktion
     * @param $name
     * @param $email
     * @param $password
     * @return bool|string
     */
    public function register($name, $email, $password)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            //Damit das Password nicht mit klartext in der Datenbanks steht wird das eingegebene Passowrd gehasht
            $pw = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_USER}
                    ({$this->U_NAME}, {$this->U_MAIL}, {$this->U_PASSWORD}, {$this->U_ISADMIN})
                    VALUES (:name, :email, :password, :isAdmin)");
            if ($stmt->execute(array(':name' => $name, ':email' => $email, ':password' => $pw, ':isAdmin' => 0))) {
                //gibt zuletzt eingefügte ID zurück
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion zum Password ändern anhand der verwendeten Email Adresse
     * @param $email
     * @param $password
     * @return bool
     */
    public function changePasswordByEmail($email, $password)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            //Damit das Password nicht mit klartext in der Datenbanks steht wird das eingegebene Passowrd gehasht
            $pw = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_USER} SET {$this->U_PASSWORD} = :password 
            WHERE {$this->U_MAIL} = :email");
            if ($stmt->execute(array(':password' => $pw, ':email' => $email))) {
             return true;
            } else {
             return false;
            }
         } catch (PDOException $e) {
             //Gibt Error meldung bei Execption aus
             echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
         }
    }


    /**
     * Gibt alle Chats des aktuellen Users zurück
     * @return array|bool|PDOStatement
     */
    public function getAllChatsFromCurrentUser()
    {
        /*
            SELECT chat.cid AS cid,
            chat.cid AS _cid,
            chat.name AS chatname,
            GROUP_CONCAT(distinct user.name) AS members,
            (SELECT message.timeadded
             FROM message
             JOIN user_is_in_chat AS _uiic ON message.uiicid = _uiic.uiicid
             JOIN chat AS _c ON _uiic.cid = _c.cid
             WHERE _c.cid = _cid
             ORDER BY message.timeadded DESC
             LIMIT 1)
             AS ord
            FROM chat, user, user_is_in_chat
            WHERE user_is_in_chat.cid = chat.cid
            AND user_is_in_chat.uid = user.uid
            GROUP BY chat.cid
            HAVING members LIKE '%daniel%'
            ORDER BY ord
        */
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            //aktuellen User Namen in Variabel speichern
            $currentUser = $this->getCurrentUser();
            $stmt = $this->db->prepare("SELECT {$this->C_ID} AS cid, {$this->C_ID} AS _cid, {$this->C_NAME} AS chatname, GROUP_CONCAT(distinct {$this->U_NAME}) AS members,
                    (SELECT MAX({$this->M_TIMEADDED}) FROM message
                        JOIN {$this->TABLE_USER_IS_IN_CHAT} AS _uiic ON {$this->M_UIICID} = _uiic.uiicid
                        JOIN {$this->TABLE_CHAT} AS _c ON _uiic.cid = _c.cid
                        WHERE _c.cid = _cid
                        ORDER BY {$this->M_TIMEADDED} DESC
                        LIMIT 1) AS ord
                    FROM {$this->TABLE_CHAT}, {$this->TABLE_USER_IS_IN_CHAT}, {$this->TABLE_USER}
                    WHERE {$this->UIIC_CID} = {$this->C_ID}
                    AND {$this->UIIC_UID} = {$this->U_ID}
                    GROUP BY {$this->C_ID}
                    HAVING members LIKE '%$currentUser%'
                    ORDER BY ord DESC");

            if ($stmt->execute()) {
                //Gibt ein PDOStatement zurück - die Ergebnismenge
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion gibt den aktuellen Usernamen zurück
     * Funktion exisitert damit der Username immer so angezeigt und verwendet wird
     * wie er sich registert hat und nicht wie er sich gerade angemeldet hat
     * z.B. Registiert mit Christian - angemeldet mit christian --> soll Christian anzeigen
     * @return mixed
     */
    public function getCurrentUser(){
        /*
         * SELECT USER.name FROM `user` WHERE user.name = "christian"
         */
        $username = $_SESSION['name'];
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_NAME} AS '0' FROM {$this->TABLE_USER} WHERE {$this->U_NAME}= :username");
            if($stmt->execute(array(':username' => $username))){
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0][0];
            }
        }catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt alle nachrichten eines Chats zurücks
     * @param $chatId
     * @return array|bool
     */
    public function getAllMessagesFromChat($chatId)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            /*
            SELECT m.mid m.message, u.name, m.timeadded,
            DATEDIFF(NOW(), m.timadded) AS date
            FROM message AS m
            JOIN user_is_in_chat AS uiic on (m.uiicid = uiic.uiicid)
            JOIN user AS u on (uiic.uid = u.uid)
            JOIN chat AS c on (uiic.cid = c.cid)
            WHERE uiic.cid = 2
            ORDER BY m.timeadded
            */
            $stmt = $this->db->prepare("SELECT {$this->M_ID},{$this->M_UIICID}, {$this->UIIC_UID}, {$this->M_MESSAGE}, {$this->M_TIMEADDED}, DATEDIFF(NOW(), {$this->M_TIMEADDED}) AS datediff, DATE_FORMAT({$this->M_TIMEADDED}, '%d.%m.%Y') AS date, DATE_FORMAT({$this->M_TIMEADDED}, '%H:%i') AS time, {$this->U_ID}, {$this->U_NAME}
                    FROM {$this->TABLE_MESSAGE}
                    JOIN {$this->TABLE_USER_IS_IN_CHAT} ON ({$this->M_UIICID} = {$this->UIIC_ID})
                    JOIN {$this->TABLE_USER} ON ({$this->UIIC_UID} = {$this->U_ID})
                    WHERE {$this->UIIC_CID} = :chatid
                    ORDER BY {$this->M_TIMEADDED} ASC, {$this->M_ID} ASC");

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
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Funktion gibt UserID des aktuellen Users zurück
     * @return bool|string
     */
    public function getUserID()
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $userName = $this->getCurrentUser();
            $stmt = $this->db->prepare("SELECT user.uid FROM user WHERE user.name = :username");
            if ($stmt->execute(array(':username' => $userName))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['uid'];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt die Email Adresse des users aus desser userId übergeben wird
     * @param $userId
     * @return bool
     */
    public function getEmail($userId)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $stmt = $this->db->prepare("SELECT user.mail FROM user WHERE user.uid = :userID");
            if ($stmt->execute(array(':userID' => $userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['mail'];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * @param $chatId
     * @param $message
     * @return bool|string
     */
    public function writeMessage($chatId, $message)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $message = htmlentities($message, ENT_QUOTES);
            $userId = $this->getUserID();
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

            if ($stmt->execute(array(':message' => $message, ':chatId' => $chatId, ':userId' => $userId)) && $this->db->lastInsertId() > 0) {
                // Nachricht zurückgeben, damit Datum ausgegeben werden kann
                $stmt2 = $this->db->prepare("SELECT DATE_FORMAT({$this->M_TIMEADDED}, '%H:%i') AS time FROM {$this->TABLE_MESSAGE} ORDER BY {$this->M_ID} DESC LIMIT 1");
                if($stmt2->execute()) {
                    return $stmt2->fetchAll(PDO::FETCH_ASSOC)[0]['time'];
                }
                else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion gibt Einladungslink zurück für den übergebenden ChatId
     * @param $chatId
     * @return bool
     */
    public function getInvitationLink($chatId) {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $stmt = $this->db->prepare("SELECT {$this->UIIC_LINK} AS link
                FROM {$this->TABLE_USER_IS_IN_CHAT}
                WHERE {$this->UIIC_CID} = :chatId
                AND {$this->UIIC_UID} = :userId");

            $userId = $this->getUserID();

            if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt ChatID sowie die UserId des einladendem Users zurück
     * @param $link
     * @return bool
     */
    public function getLinkData($link) {
        //try Catch block zum abfangen einer Möglichen Exception
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
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion fügt  aktuellen user in Chat hinzu anhand der Chatid
     * @param $chatId
     * @return bool|string
     */
    public function joinChat($chatId) {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_USER_IS_IN_CHAT}
                    ({$this->UIIC_UID}, {$this->UIIC_CID}, {$this->UIIC_LINK})
                    VALUES(:userId, :chatId, :link)
                    ON DUPLICATE KEY UPDATE deleted = 0");
            //erzeug einen random string der als link genutzt wird
            $link = md5(rand(0,1000));

            if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $this->getUserID(), ':link' => $link))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Funktion erstellt Chat und fügt aktuellen user hinzu
     * @param $chatName
     * @return bool|string
     */
    public function createChat($chatName) {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_CHAT}
                    ({$this->C_NAME})
                    VALUES (:chatName)");

            if ($stmt->execute(array(':chatName' => $chatName))) {
                $chatId = $this->db->lastInsertId();

                $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_USER_IS_IN_CHAT}
                    ({$this->UIIC_CID}, {$this->UIIC_UID}, {$this->UIIC_DELETED}, {$this->UIIC_LINK})
                    VALUES (:chatId, :userId, :deleted , :link)");
                //erzeugt einen random string der als link genutzt wird
                $link = md5(rand(0,1000));
                $userId = $this->getUserID();
                
                if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $userId, ':deleted'=> 0, ':link' => $link))) {
                    return $this->db->lastInsertId();
                }
                else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Erstellt einen ZufallsChat mit dem Namen Random-Chat
     * Fügt zufällig eine Person hinzu und den aktuellen user
     * @return bool|string
     */
    public function createRandomChat() {
        //try Catch block zum abfangen einer Möglichen Exception
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
                //erzeugt einen random string der als link genutzt wird
                $link1 = md5(rand(0,1000));
                $link2 = md5(rand(0,1000));
                $userId = $this->getUserID();
                
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
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Funktion Löscht Chat(chatId) für aktuellen User
     * setzt Flag in Datenbank das User den Chat gelöscht hat
     * @param $chatId
     * @return bool|string
     */
    public function deleteChat($chatId)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            /*
             * UPDATE `user_is_in_chat` SET `deleted`=1 WHERE `cid`= 1 AND `uid` 5
            */
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_USER_IS_IN_CHAT}
                    SET {$this->UIIC_DELETED} = 1
                    WHERE {$this->UIIC_CID} = :chatId
                    AND {$this->UIIC_UID} = :userId");
            $userId = $this->getUserID();
            if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $userId))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Funktion prüft ob Chat für den User gelöscht ist
     * Überprüft ob das Flag deleted gesetzt ist oder nicht
     * @param $chatId
     * @return array|bool|string
     */
    public function isChatDeletedForUser($chatId)
    {
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            /*
             * SELECT `deleted` FROM `user_is_in_chat` WHERE `cid` = 1 AND `uid` = 1
            */
            $stmt = $this->db->prepare("SELECT {$this->UIIC_DELETED} 
                    FROM {$this->TABLE_USER_IS_IN_CHAT}
                    WHERE {$this->UIIC_CID} = :chatId
                    AND {$this->UIIC_UID} = :userId");
            $userId = $this->getUserID();
            if ($stmt->execute(array(':chatId' => $chatId, ':userId' => $userId))) {
                $deleted = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $deleted = $deleted[0]['deleted'];
                return $deleted;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion gibt die Mitglieder des Chats zurück
     * @param $chatId
     * @return array|bool
     */
    public function getMembersOfChat($chatId)
    {
        //try Catch block zum abfangen einer Möglichen Exception
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
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt die Anzahl der Registierten user zurück
     * Statistik Funktion
     * @return bool|int
     */
    public function userCount(){

        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_ID} FROM {$this->TABLE_USER}");
            if($stmt->execute()){
                return $stmt->rowCount();
            }else return false;
        }catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt die Anzahl der Geschriebenden Nachrichten zurück
     * in dem Intervall der übergeben wurde
     * Statistik Funktion
     * @param $hours
     * @return bool|int
     */
    public function msgCountPerTime($hours){
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_MESSAGE} WHERE {$this->M_TIMEMODIFIED} >= DATE_SUB(NOW(), INTERVAL ? HOUR)");
            if($stmt->execute((array($hours)))){
                return $stmt->rowCount();
            }else {
                return false;
            }
        }catch(PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt die Anzahl der gesamt geschriebenden Nachrichten zurück
     * Statistik Funktion
     * @return bool|int
     */
    public function totalChatMessages(){
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_MESSAGE}");
            if($stmt->execute()){
                return $stmt->rowCount();
            }else {
                return false;
            }
        }catch(PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt die Anzahl der User zurück die innerhalb der letzen 5 Minuten online waren
     * Statistik Funktion
     * @return bool|int
     */
    public function recentlyActive(){
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_MESSAGE} WHERE {$this->M_TIMEMODIFIED} >= DATE_SUB(NOW(), INTERVAL 5 MINUTE) GROUP BY {$this->M_UIICID}");
            if($stmt->execute()){
                return $stmt->rowCount();
            }else {
                return false;
            }
        }catch(PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * * Gibt die Anzahl der gerade aktiven User zurück
     * @return bool|int
     */
    public function onlineUsers(){
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT * FROM {$this->TABLE_USER} WHERE {$this->U_TIMEMODIFIED} >= DATE_SUB(NOW(), INTERVAL 10 SECOND)");
            if($stmt->execute()){
                return $stmt->rowCount();
            }else {
                return false;
            }
        }catch(PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt zurück ob user admin ist oder nicht
     * wenn ja --> Zugriff auf Statistik Funktion
     * @return mixed
     */
    public function isUserAdmin (){
        /*
         * SELECT USER.is_admin FROM `user` WHERE user.uid = 1
         */
        $userid = $this->getUserID();
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_ISADMIN} AS '0' FROM {$this->TABLE_USER} WHERE {$this->U_ID}= :userId");
            if($stmt->execute(array(':userId' => $userid))){
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0][0];
            }
        }catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion updatet die Spalte TimeModified der tabelle users des aktuellen user
     * Dient dazu um herauszufinden wer gerade online ist  oder wer in den letzen x minuten online war
     * @return bool|string
     */
    public function ping(){
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            /*
             * UPDATE `user` SET USER.timemodified=CURRENT_TIMESTAMP   WHERE USER.uid = 1
            */
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_USER}
                    SET {$this->U_TIMEMODIFIED} = CURRENT_TIMESTAMP 
                    WHERE {$this->U_ID} = :userId");
            $userId = $this->getUserID();
            if ($stmt->execute(array(':userId' => $userId))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion erstellt Profilbild für aktuellen User
     * @param $name
     * @param $image
     * @return bool|string
     */
    public function createPictureFromUser($name, $image){
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            /*
             * INSERT INTO images (uid,imdata) VALUES (:uid,:imdata)
            */
            $stmt = $this->db->prepare("INSERT INTO {$this->TABLE_IMAGES}
                    ({$this->I_UID},{$this->I_IMGNAME},{$this->I_IMGDATA}) VALUES (:userId,:imgname,:imgdata)");
            $userId = $this->getUserID();
            if ($stmt->execute(array(':userId' => $userId,':imgname' => $name,':imgdata' => $image))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion gibt das gespeicherte Bild des aktuellen users zurück
     * @return array|bool
     */
    public function showPictureFromCurrentUser(){

        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $userId = $this->getUserID();
            $stmt = $this->db->prepare("SELECT {$this->I_IMGNAME},{$this->I_IMGDATA} FROM {$this->TABLE_IMAGES} WHERE {$this->I_UID}=:userId");
            if($stmt->execute(array(':userId' => $userId))){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }else return false;
        }catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion gibt gespeichertes bIld des Users zurück dessen userid übergeben wurde
     * @param $userId
     * @return array|bool
     */
    public function showPictureByUserId($userId){
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT {$this->I_IMGNAME},{$this->I_IMGDATA} FROM {$this->TABLE_IMAGES} WHERE {$this->I_UID}=:userId");
            if($stmt->execute(array(':userId' => $userId))){
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }else return false;
        }catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt die User ID zurück anhand des Usernamens
     * @param $username
     * @return bool
     */
    public function getUserIdByName($username){
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("SELECT {$this->U_ID} AS '0' FROM {$this->TABLE_USER} WHERE {$this->U_NAME}=:username");
            if($stmt->execute(array(':username' => $username))){
                $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $return[0][0];
            }else return false;
        }catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Gibt usernamen zurück anhand der userId
     * @param $userId
     * @return bool
     */
    public function getUserNameById($userId) {
        //try Catch block zum abfangen einer Möglichen Exception
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
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt Usernamen zurück anhand der User is in Chat ID
     * @param $userIsInChatId
     * @return bool
     */
    public function getUserNameFromUserIsInChatId($userIsInChatId)
    {
        //try Catch block zum abfangen einer Möglichen Exception
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
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion gibt den Chatnamen zurück anhand er ChatID
     * @param $chatId
     * @return bool
     */
    public function getChatnameById($chatId) {
        //try Catch block zum abfangen einer Möglichen Exception
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
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Updatet das Profilbild des aktuellen Users
     * @param $name
     * @param $image
     * @return bool|string
     */
    public function updatePictureFromCurrentUser($name, $image){
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            /*
             * UPDATE images SET imgdata=:imgdata,imgname=:imgname WEHRE uid=:userId
            */
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_IMAGES}
                    SET {$this->I_IMGNAME}=:imgname,{$this->I_IMGDATA}=:imgdata WHERE {$this->I_UID}=:userId");
            $userId = $this->getUserID();
            if ($stmt->execute(array(':userId' => $userId,':imgname' => $name,':imgdata' => $image))) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }

    /**
     * Funktion gibt zurück ob der aktuelle User bereits ein profilbild hat oder nicht
     * @return bool
     */
    public function haveUserPicture(){
        $picture = $this->showPictureFromCurrentUser();
        $count = count($picture);
        if($count>0){
            return true;
        }else return false;
    }


    /**
     * Löscht das Profilbild aus der Datenbank des aktuellen users
     * @return bool
     */
    public function deletePicture(){
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            /*
             * DELETE FROM `images` WHERE `uid`
            */
            $stmt = $this->db->prepare("DELETE FROM {$this->TABLE_IMAGES}
                    WHERE {$this->I_UID} = :userId");
            $userId = $this->getUserID();
            if ($stmt->execute(array(':userId' => $userId))) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Gibt die letze Message ID des Chats zurück dess chatid übergeben wurde
     * @param $chatId
     * @return bool
     */
    public function getLastMessageIdFromChat($chatId){
        /*
         *  SELECT m.mid
            FROM message AS m
            JOIN user_is_in_chat AS uiic on (m.uiicid = uiic.uiicid)
            WHERE uiic.cid = 3
            ORDER BY m.timeadded DESC
            LIMIT 1
         */
        //try Catch block zum abfangen einer Möglichen Exception
        try {
            $stmt = $this->db->prepare("SELECT {$this->M_ID}
                                                FROM {$this->TABLE_MESSAGE}
                                                JOIN {$this->TABLE_USER_IS_IN_CHAT} on ({$this->M_UIICID}={$this->UIIC_ID})
                                                WHERE {$this->UIIC_CID} = :chatid
                                                ORDER BY {$this->M_TIMEADDED} DESC 
                                                LIMIT 1");

            if ($stmt->execute(array(':chatid' => $chatId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['mid'];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }

    }


    /**
     * Funktion holt die Nachrichten aus dem Chat die noch nicht gelesen wurden
     * anhand der letzten bekannten messageID
     * @param $chatId
     * @param $messageId
     * @return array|bool
     */
    public function getNewMessages($chatId, $messageId){
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            /*            SELECT m.mid, m.message, u.name, m.timeadded,
            DATEDIFF(NOW(), m.timeadded) AS date
            FROM message AS m
            JOIN user_is_in_chat AS uiic on (m.uiicid = uiic.uiicid)
            JOIN user AS u on (uiic.uid = u.uid)
            JOIN chat AS c on (uiic.cid = c.cid)
            WHERE uiic.cid = 3 AND m.mid > 50
            ORDER BY m.timeadded
            */
            $stmt = $this->db->prepare("SELECT {$this->M_ID},{$this->M_UIICID}, {$this->UIIC_UID}, {$this->M_MESSAGE}, {$this->M_TIMEADDED}, DATEDIFF(NOW(), {$this->M_TIMEADDED}) AS datediff, DATE_FORMAT({$this->M_TIMEADDED}, '%d.%m.%Y') AS date, DATE_FORMAT({$this->M_TIMEADDED}, '%H:%i') AS time, {$this->U_ID}, {$this->U_NAME}
                    FROM {$this->TABLE_MESSAGE}
                    JOIN {$this->TABLE_USER_IS_IN_CHAT} ON ({$this->M_UIICID} = {$this->UIIC_ID})
                    JOIN {$this->TABLE_USER} ON ({$this->UIIC_UID} = {$this->U_ID})
                    WHERE {$this->UIIC_CID} = :chatid AND {$this->M_ID} > :messageId
                    ORDER BY {$this->M_TIMEADDED} ASC, {$this->M_ID} ASC");
            if ($stmt->execute(array(':chatid' => $chatId,':messageId' => $messageId))) {
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

        }catch (PDOException $e) {
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Setzt Flag in der Datenbank das alle nachrichten in dem Chat gelesen wurden
     * @param $chatId
     * @param $userId
     * @return bool|string
     */
    public function setFlagUnreadMessage($chatId, $userId) {

        //try Catch block zum abfangen einer Möglichen Exception
        try{
            /*
             * UPDATE `user_is_in_chat` SET `unreadMessage`= WHERE cid= AND uid =
             */
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_USER_IS_IN_CHAT}
                    SET {$this->UIIC_UNREADMESSAGE} = 0
                    WHERE {$this->UIIC_CID} = :chatId AND {$this->UIIC_UID} = :userId");

            if($stmt->execute(array(':chatId' => $chatId,'userId'=>$userId))) {
                return $this->db->lastInsertId();
            }
            else {
                return false;
            }
        } catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Setzt Flag in der Datenbank auf ungelesende Nachrichten
     * für alle die in dem Chat sind dessen chat id übergeben wurde
     * für alle die nicht der aktuelle user sind (da der aktuelle user die nachricht geschrieben hat)
     * @param $chatId
     * @param $userId
     * @param $setbit
     * @return bool|string
     */
    public function setFlagUnreadMessageForEveryOne ($chatId, $userId, $setbit) {
        /*
             * UPDATE `user_is_in_chat` SET `unreadMessage`=1 WHERE cid=3 AND NOT uid = 3
             */
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $stmt = $this->db->prepare("UPDATE {$this->TABLE_USER_IS_IN_CHAT}
                    SET {$this->UIIC_UNREADMESSAGE} = :setbit
                    WHERE {$this->UIIC_CID} = :chatId AND NOT {$this->UIIC_UID} = :userId");

            if($stmt->execute(array(':chatId' => $chatId,'userId'=>$userId,'setbit'=>$setbit))) {
                return $this->db->lastInsertId();
            }
            else {
                return false;
            }
        } catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }


    /**
     * Funktion Überprüft ob in einem Chat indem der aktuelle user ist eine neue nachricht geschrieben wurde (flag überprüfen)
     * wenn dies der fall ist gibt er die ChatID zurück in der eine neue nachricht ist
     * @return array|bool
     */
    public function proofForNewMessages () {
        /*
         * SELECT user_is_in_chat.cid FROM `user_is_in_chat` WHERE user_is_in_chat.uid = 5 AND user_is_in_chat.unreadMessage = 1
         */
        //try Catch block zum abfangen einer Möglichen Exception
        try{
            $userId = $this->getUserID();
            $stmt = $this->db->prepare("SELECT {$this->UIIC_CID}
                    FROM {$this->TABLE_USER_IS_IN_CHAT}
                    WHERE {$this->UIIC_UNREADMESSAGE} = 1 AND {$this->UIIC_UID} = :userId");

            if($stmt->execute(array('userId'=>$userId))) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                return false;
            }
        } catch (PDOException $e){
            //Gibt Error meldung bei Execption aus
            echo "<div class='alert alert-danger'>Error: ".$e->getMessage()."</div>";
        }
    }
}
