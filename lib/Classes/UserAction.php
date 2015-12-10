<?php

class UserAction
{

	protected $db;
	private $user = null;

	public function __construct() {
		$this->db = new DatabaseClass();
	}

	/*
	 * İstekler buraya gelip yönlendirilir
	 * @param array $userData facabook user data
	 * @param array $friends facebook arkadaşlar
	 * @return array
	*/
	public function controller($userData, $friends) {
		$isAdd = $this->userCheck($userData);
		if (!$isAdd) die("Sistemde hata meydana geldi. Lütfen sayfayı yenileyiniz.");
		$friends = $this->setArrayFriendIds($friends);
		
		$activeGifts = $this->db->getGifts();
		$currentUserGifts = $this->db->getCurrentUserGifts($userData['id'], $this->getDate('-7'));
		$sentUsers = $this->setOrder( $this->db->getSentUser($userData['id'], $this->getDate('-1')) );
		$unSentFriends = $this->getUnSentFriends($friends, $sentUsers);
		
		return array(
			'user' => $this->user, 
			'currentUserGifts' => $currentUserGifts, 
			'unSentFriends' => $unSentFriends, 
			'activeGifts' => $activeGifts
		);
	}

	/*
	 * User daha önce kayıt edildi mi?
	 * @param array $userData facabook user data
	 * @return boolean
	*/
	private function userCheck($userData) {
		$this->user = $this->db->isUserExists($userData['id']);
		if ($this->user == null) { //yeni kayıt
			return $this->db->addUser($userData);
		}
		return true;
	}
	
	/*
	 * Tarihin öncesini bulur
	 * @param int $days tarihin x gün öncesi
	 * @return array
	*/
	private function getDate($days) {
		return date('Y-m-d H:i:s', strtotime($days . ' days'));
	}
	
	/*
	 * Tarihin öncesini bulur
	 * @param int $days tarihin x gün öncesi
	 * @return array
	*/
	private function getUnSentFriends($friends, $sent) {
		$unSent = array();
		if ($friends != null) {
			foreach ($friends As $friend) {
				if (!in_array($friend, $sent)) {
					$unSent[] = $friend;
				}
			}
		}
		return $unSent;
	}
	
	/*
	 * Arkadaşları single array olarak getirir
	 * @param array $friends facebook arkadaşlar
	 * @return array
	*/
	private function setOrder($friends) {
		$sent = array();
		if ($friends != null) {
			foreach ($friends As $friend) {
				$sent[] = $friend['faceId'];
			}
		}
		return $sent;
	}
		
	/*
	 * Arkadaşların idlerini single array olarak getirir
	 * @param array $friends facebook arkadaşlar
	 * @return array
	*/
	private function setArrayFriendIds($friends) {
		$friendIds = array();
		if ($friends != null) {
			foreach ($friends As $friend) {
				$friendIds[] = $friend['id'];
			}
		}
		return $friendIds;
	}
	
}