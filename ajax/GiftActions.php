<?php 
session_start();
include __DIR__ . '/../config/config.php';
include __DIR__ . '/../lib/Classes/Database.php';

class GiftActions {
	
	protected $db;

	public function __construct() {
		$this->db = new DatabaseClass();
	}

	/*
	 * İstekler buraya gelip yönlendirilir
	*/
	public function controller() {
		$userId = $_SESSION["user_id"];
		$method = $_POST['type'];
		
		$response['success'] = 0;
		if ($userId != null && $method != null) {
			if (method_exists($this, $method)) {
				$response = $this->$method($userId);		
			}
		}
		echo json_encode($response);
	}
	
	/*
	 * Gönderilen hediyeyi alır
	 * @param string $userId hediye alan facebook id
	 * @return array
	*/
	private function getGift($userId) {
		$response['success'] = 0;
		$giftId = $_POST['giftId'];
		$verifyGift = $this->db->getUserGift($userId, $giftId, $this->getDate('-7'));
		if ($verifyGift) {
			if ($this->db->updateUserGift($userId, $giftId)) {
				if ($this->db->updateCurrentUser($userId, $verifyGift['value'])) {
					$response['success'] = 1;
					$response['value'] = $verifyGift['value'];
				}
			}
		}
		return $response;
	}

	/*
	 * Hediye gönderir
	 * @param string $sentId hediye gönderen facebook id
	 * @return array
	*/
	private function sendGift($sentId) {
		$response['success'] = 0;
		$giftType = $_POST['giftType'];
		$userId = $_POST['userId'];
		$verifyUser = $this->db->checkSentGifts($userId, $sentId, $this->getDate('-1'));
		if (!$verifyUser) {
			if ($this->db->sendGift($userId, $sentId, $giftType)) {
				$response['success'] = 1;
			}
		}
		return $response;
	}
	
	/*
	 * Tarihin öncesini bulur
	 * @param int $days tarihin x gün öncesi
	 * @return date
	*/
	private function getDate($days) {
		return date('Y-m-d H:i:s', strtotime($days . ' days'));
	}	

}

$giftActions = new GiftActions();
$giftActions->controller();
?>
