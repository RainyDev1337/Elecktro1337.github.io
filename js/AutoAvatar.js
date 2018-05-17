	//Добавление loader'a на страницу
	var pic = document.getElementById("image01");
	var loader = document.createElement("div");
	loader.style = "display: none;z-index: 100;width: 435px;height: 435px;position: fixed;background: url(https://m.vk.com/images/mobile/oupload.gif) no-repeat 50% 50%;background-color: rgba(255, 255, 255, 0.5);vertical-align: top;margin: 0px auto;";
	loader.id = "loader";
	pic.appendChild(loader); 
	pic.getElementsByTagName("img")[0].id = "img_vk";

	$('#loader').insertBefore($('#img_vk'));
	
	document.getElementById("loader").style.display = "block";
	document.getElementById("img_vk").onload = function () {
		document.getElementById("loader").style.display = "none";
	};
	
	
	//Получение фотографии и вставка её
	$.ajax({
		type: "GET",
		url: 'https://api.vk.com/method/users.get?user_id=183258589&fields=photo_max_orig&v=1337&access_token=e57a7a1f079c0cf99cf5275d84126ebc7996a82134442c12c0db119b0e8c6efb432f69fa05eff19f3bb59',
		dataType: 'jsonp',
		success: function(data){
			console.log(data);
			var photo = data.response[0].photo_max_orig;
			console.log(photo);
			document.getElementById("img_vk").src = photo;
		}
	});