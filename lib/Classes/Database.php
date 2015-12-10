<?php 

class DatabaseClass
{
    protected $conn;
 
    public function __construct() { //açılışta çalıştır
        $this->dbConnect();
    }
 
    public function dbConnect() { //veritabanı bağlantısı kurma
        try{
            $this->conn = new PDO("mysql:dbname=".MYSQL_DB.";host=".HOST, USER, PASS);
            $this->conn->query("SET NAMES 'utf8'");
            $this->conn->query('set character set utf8');
        }catch(PDOException $e){
             die($e->getMessage());
        }
    }
 
	public function isUserExists($faceId) { //user var mı?
        $query = $this->conn->prepare('SELECT id, name, coins FROM users WHERE faceId = :faceId');
        $query->execute(array('faceId' => $faceId));
        return $query->fetch(PDO::FETCH_ASSOC);
    }
 
    public function addUser($userData) { //kullanıcıyı tabloya ekleme
        $query = $this->conn->prepare('INSERT INTO users (faceId,name) VALUES (:faceId,:name)');
        return $query->execute(array('faceId' => $userData['id'], 'name' => $userData['name']));
    }

	public function getGifts() { //hediyeleri alma
		$query = $this->conn->prepare('SELECT id, name, picture FROM gifts WHERE status = :status');
        $query->execute(array('status' => 1));
        return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function getCurrentUserGifts($faceId, $created_at) { //aktif kullanıcının son 7 gün içerisinde kullanmadığı hediyeler
		$query = $this->conn->prepare('	SELECT ug.id As gift_id, g.name As gift_name, g.picture, (SELECT name FROM users WHERE id = ug.sent_id) As user 
										FROM gifts As g, users As u, user_gift As ug
										WHERE ug.user_id = u.id And ug.gift_id = g.id And ug.status = :status And 
											  u.faceId = :faceId And ug.created_at > :created_at
									  ');
        $query->execute(array('faceId' => $faceId, 'created_at' => $created_at, 'status' => 1));
        return $query->fetchAll(PDO::FETCH_ASSOC);
	}

	public function getSentUser($faceId, $created_at) { //kullanıcının son 1 gün içerisinde hediye gönderdiği arkadaşları
		$query = $this->conn->prepare('	SELECT u.faceId FROM users As u, user_gift As ug 
										WHERE ug.user_id = u.id And ug.status = :status And ug.created_at > :created_at And 
											  ug.sent_id = (SELECT id FROM users WHERE faceId = :faceId)
									  ');
        $query->execute(array('faceId' => $faceId, 'created_at' => $created_at, 'status' => 1));
        return $query->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	public function getUserGift($faceId, $giftId, $created_at) { //kullanıcının hediyesini alma
		$query = $this->conn->prepare(' SELECT ug.id, g.value
										FROM gifts As g, users As u, user_gift As ug
										WHERE ug.status = :status And ug.created_at > :created_at And u.faceId = :faceId And 
											  ug.id = :giftId And ug.gift_id = g.id And u.id = ug.user_id
									');
        $query->execute(array('faceId' => $faceId, 'giftId' => $giftId, 'created_at' => $created_at, 'status' => 1));
        return $query->fetch(PDO::FETCH_ASSOC);
	}
	
	public function updateUserGift($faceId, $giftId) { //alınan hediyeyi deaktif etme
		$query = $this->conn->prepare('UPDATE user_gift SET status = :status WHERE id = :giftId And user_id = (SELECT id FROM users WHERE faceId = :faceId)');
        return $query->execute(array('status' => 0, 'faceId' => $faceId, 'giftId' => $giftId));
	}
	
	public function updateCurrentUser($faceId, $coins) { //kullanıcının hediyesini hesabına ekleme
		$query = $this->conn->prepare('UPDATE users SET coins = coins + ' . $coins . ' WHERE faceId = :faceId');
        return $query->execute(array('faceId' => $faceId));
	}
	
	
	public function checkSentGifts($userId, $sentId, $created_at) { //kullanıcı aynı kişiye hediye göndermiş mi?
		$query = $this->conn->prepare(' SELECT ug.id
										FROM users As u, user_gift As ug
										WHERE ug.created_at > :created_at And u.faceId = :sentId And u.id = ug.sent_id And
											  ug.user_id = (SELECT id FROM users WHERE faceId = :userId)
									');
        $query->execute(array('sentId' => $sentId, 'userId' => $userId, 'created_at' => $created_at));
        return $query->fetchColumn();
	}
	
	public function sendGift($userId, $sentId, $giftType) { //hediyeyi tabloya ekleme
		$query = $this->conn->prepare('INSERT INTO user_gift (gift_id,created_at,status,user_id,sent_id) VALUES (?,?,?,(SELECT id FROM users WHERE faceId = ?),(SELECT id FROM users WHERE faceId = ?))');
        return $query->execute(array($giftType, date("Y-m-d H:i:s"), 1, $userId, $sentId));
	}
	
	
    public function __destruct() { //bağlantıyı sonlandırma
        $this->conn = null;
    }
	
}