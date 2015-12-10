window.onload = function() {
	
	$(".nav-pills li").click(function() {
		$("li.active").removeClass("active");
		$(this).addClass("active");
		$(".menuDiv .tab").hide();
		var index = $(this).index();
		$(".tab_" + index).show();
	});
	
	$(document).on("click", "[class*=getGift_]", function(event) {
		var classId = $(this).attr('class');
		var gift = classId.split('_');
		if (typeof gift['1'] != "undefined") {
			var giftId = gift['1'];
			var data = {type: 'getGift', giftId: giftId};
			giftSender(data);
		}
	});
	
	$(document).on("click", "[class*=sendGift_]", function(event) {
		var classId = $(this).attr('class');
		var gift = classId.split('_');
		var giftType = $(".giftType").val();
		if (typeof gift['1'] != "undefined") {
			var userId = gift['1'];
			var data = {type: 'sendGift', userId: userId, giftType: giftType};
			giftSender(data);
		}
	});
	
	$('#invite').click(function() {
		inviteFriends();
	});
}

function giftSender(data) {
	$.ajax({
		type: 'POST',
		url:  './ajax/GiftActions.php?v=1.3',
		data: data,
		dataType: 'json',
		success: function(response) {
			if (data.type == 'getGift') {
				if (response.success) {
					$('#getGift_' + data.giftId).remove();
					$('.userGiftResponse').html('<div class="alert alert-success">Hediye başarıyla alındı.</div>');
					$("#coins").html(parseInt(response.value) + parseInt($("#coins").html()));
				} else {
					$('.userGiftResponse').html('<div class="alert alert-error">Hediye alınamadı. Lütfen tekrar deneyin.</div>');
				}
			} else if (data.type == 'sendGift') {
				if (response.success) {
					$('#sendGift_' + data.userId).remove();
					$('.sendGiftResponse').html('<div class="alert alert-success">Hediye başarıyla gönderildi.</div>');
				} else {
					$('.sendGiftResponse').html('<div class="alert alert-error">Hediye gönderilemedi. Lütfen tekrar deneyin.</div>');
				}
			}
		}
	});
}

function inviteFriends() {
	FB.ui({
		method: 'apprequests',
		message: 'Davet Et'
	});
}