<?php
session_start();
require_once __DIR__ . '/lib/src/Facebook/autoload.php';
require_once __DIR__ . '/config/config.php';

$fb = new Facebook\Facebook([
  'app_id' => APP_ID,
  'app_secret' => APP_SECRET,
  'default_graph_version' => GRAPH_VERSION,
]);

$helper = $fb->getCanvasHelper();
$permissions = ['user_friends'];

try {
	if (isset($_SESSION['facebook_access_token'])) {
	$accessToken = $_SESSION['facebook_access_token'];
	} else {
  		$accessToken = $helper->getAccessToken();
	}
} catch(Facebook\Exceptions\FacebookResponseException $e) {
 	echo 'Graph returned an error: ' . $e->getMessage();
  	exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
  	exit;
}

if (isset($accessToken)) {
	if (isset($_SESSION['facebook_access_token'])) {
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	} else {
		$_SESSION['facebook_access_token'] = (string) $accessToken;
	  	$oAuth2Client = $fb->getOAuth2Client();
		$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
		$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
		$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
	}
	
	try {
		$request = $fb->get('/me');
		$userInfo = $request->getGraphUser();
		$_SESSION['user_id'] = $userInfo['id'];
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		if ($e->getCode() == 190) {
			unset($_SESSION['facebook_access_token']);
			$helper = $fb->getRedirectLoginHelper();
			$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/bcakirdotcom/', $permissions);
			echo "<script>window.top.location.href='".$loginUrl."'</script>";
		}
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	try {
		$requestFriends = $fb->get('/me/friends');
		$friends = $requestFriends->getGraphEdge();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
} else {
	$helper = $fb->getRedirectLoginHelper();
	$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/bcakirdotcom/', $permissions);
	echo "<script>window.top.location.href='".$loginUrl."'</script>";
}

include __DIR__ . '/lib/Classes/Database.php';
include __DIR__ . '/lib/Classes/UserAction.php';

$userAction = new UserAction();
$userData = $userAction->controller($userInfo, $friends);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta name="author" content="bcakir" />
    <link rel="stylesheet" type="text/css" href="assets/ext/bootstrap/css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/style.css?v=1.281" />
    <script type="text/javascript" src="assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="assets/ext/bootstrap/js/bootstrap.min.js"></script>
	<div id="fb-root"></div>
	<script src="https://connect.facebook.net/en_US/all.js"></script>
	<script type="text/javascript" src="assets/js/gift.js?v=1.281"></script>
	<script>FB.init({appId: '<?php echo APP_ID; ?>', status: true, cookie: true, xfbml: true});</script>
    <title>Facebook Gift App</title>
</head>
<body>

<div id="content">
	<div class="userAccount">
		<div class="user">
			<span class="bold">Ad Soyad:</span>
			<span><?php echo $userInfo['name']; ?></span>
		</div>
		<div class="coins">
			<span class="bold">Coins:</span>
			<span id="coins"><?php echo $userData['user']['coins']; ?></span>
		</div>
	</div>
	
	<div class="menu">
		<ul class="nav nav-pills">
		  <li class="active"><a>Hediyelerim</a></li>
		  <li><a>Hediye Gönder</a></li>
		  <li><a>Davet Et</a></li>
		</ul>	
	</div>
	
	<div class="menuDiv">
		<div id="currentUserGifts" class="tab tab_0">
			<h3>Hediyelerim</h3>
			<table class="table">
				<tbody>
					<?php foreach ($userData['currentUserGifts'] As $gift) { ?>		
						<tr id="getGift_<?php echo $gift['gift_id']; ?>">
							<td><?php echo $gift['user']; ?></td>
							<td><img src="assets/<?php echo $gift['picture']; ?>" /></td>
							<td><?php echo $gift['gift_name']; ?></td>
							<td class="giftControl"><a class="getGift_<?php echo $gift['gift_id']; ?>">Ekle</a></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<div class="userGiftResponse">
				<?php if ($userData['currentUserGifts'] == null) { ?>
					<div class="alert">Hediyeniz bulunmamaktadır.</div>
				<?php } ?>
			</div>
		</div>
		
		<div id="sendGift" class="tab tab_1">
			<h3>Hediye Gönder</h3>
			<table class="table">
				<tbody>
					<tr>
						<td colspan="2" style="text-align: center; border: none;">
							<span>Seçiniz:</span>
							<select class="btn giftType">
								<?php foreach ($userData['activeGifts'] As $gift) { ?>
									<option value="<?php echo $gift['id']; ?>"><?php echo $gift['name']; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					
					<?php foreach ($friends As $friend) { ?>
						<?php if (in_array($friend['id'], $userData['unSentFriends'])) { ?>
							<tr id="sendGift_<?php echo $friend['id']; ?>">
								<td><?php echo $friend['name']; ?></td>
								<td class="giftControl"><a class="sendGift_<?php echo $friend['id']; ?>">Gönder</a></td>
							</tr>
						<?php } ?>
					<?php } ?>
				</tbody>
			</table>
			<div class="sendGiftResponse">
				<?php if ($friends == null) { ?>
					<div class="alert">Uygulamayı kullanan arkadaşınız bulunmamaktadır.</div>
				<?php } ?>
			</div>
		</div>

		<div id="friendsInvite" class="tab tab_2">
			<h3>Davet Et</h3>
			<a id="invite">Arkadaş davet penceresini aç</a>
		</div>
	</div>
	
</div>

</body>
</html>