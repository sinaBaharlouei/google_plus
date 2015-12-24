var profile;

var retrievedObject = localStorage.getItem('profile');
if(retrievedObject && false) {
    profile = JSON.parse(retrievedObject);
    XMLProcess();
}
else {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var xmlDoc = xmlhttp.responseXML;
            profile = xmlDoc.getElementsByTagName("profile")[0];
	        //alert(profile.innerHTML);
            localStorage.setItem('profile', JSON.stringify(profile));
            XMLProcess();
        }
    };

    xmlhttp.open("POST", "http://plus.local/profile/getxml", true);
    xmlhttp.send();
}

function XMLProcess() {

	// Get basic information of the user
	var basicInfo = profile.getElementsByTagName("basicInfo");
    var keys = document.getElementById("keys");
    var values = document.getElementById("values");

	for (j = 0; j < basicInfo[0].childNodes.length; j++) {
		var bi_element = basicInfo[0].childNodes[j];
		if (bi_element.nodeType === 1) {

			// Add key
			var new_key = document.createElement("p");
			new_key.innerHTML = bi_element.nodeName;

			// Add value
			var new_value = document.createElement("p");
			new_value.innerHTML = bi_element.innerHTML;

			keys.appendChild(new_key);
			values.appendChild(new_value);
		}
	}
	// Contact information
	var contacts_div = document.getElementById("contacts");
	var contacts = profile.getElementsByTagName("contacts");
	for (j = 0; j < contacts[0].childNodes.length; j++) {
		var contact_element = contacts[0].childNodes[j];
		if (contact_element.nodeType === 1) {

			// Add key
			var new_info = document.createElement("span");
			new_info.className = "bold_text";
			new_info.innerHTML = contact_element.nodeName;
			contacts_div.appendChild(new_info);

			// Add value
			var new_val = document.createElement("span");
			new_val.className = "phone";
			new_val.innerHTML = contact_element.innerHTML;
			contacts_div.appendChild(new_val);

			var br = document.createElement("br");
			contacts_div.appendChild(br);

		}
	}

	var images = profile.getElementsByTagName("followersPics");
	var img_div = document.getElementById("follower_list");

	for (j = 0; j < images[0].childNodes.length; j++) {
		var img_element = images[0].childNodes[j];
		if (img_element.nodeType === 1) {

			// Add key
			var new_image = document.createElement("img");
			new_image.src = img_element.innerHTML;
			new_image.alt = "-";
			new_image.className = "friend_img";

			img_div.appendChild(new_image);
		}
	}


    // get profile picture
	document.getElementById("user_pic").src = profile.getElementsByTagName("profilePic")[0].innerHTML;

	// get cover picture
	document.getElementById("cover_pic").src = profile.getElementsByTagName("cover")[0].innerHTML;

	// get profile picture
	document.getElementById("views").innerHTML = profile.getElementsByTagName("view")[0].innerHTML;

	//var slider_file = profile.getElementsByTagName("slider")[0].innerHTML;
    //showSlider(slider_file);

	// Other info
	document.getElementById("username").innerHTML = profile.getElementsByTagName("name")[0].innerHTML;
	document.getElementById("about").innerHTML = profile.getElementsByTagName("about")[0].innerHTML;

	document.getElementById("followers").innerHTML = profile.getElementsByTagName("followers")[0].innerHTML;
    document.getElementById("follower").innerHTML = profile.getElementsByTagName("followers")[0].innerHTML + " people";

	// Place
	var place = profile.getElementsByTagName("places")[0].getElementsByTagName("place")[0].innerHTML;
	document.getElementById("place").innerHTML = place;
	getGoogleMapXML(place);

	// Education
	var education = profile.getElementsByTagName("education");
	var education_box = document.getElementById("education");
	for (var j = 0; j < education[0].childNodes.length; j++) {
		var element = education[0].childNodes[j];
		if (element.nodeType === 1) {

			// Add location
			var location = document.createElement("p");
			location.className = "bold_text";
			location.innerHTML = element.innerHTML;

			// Add time
			var time = document.createElement("p");
			time.className = "info_text";
			time.innerHTML = element.nodeName;

			education_box.appendChild(location);
			education_box.appendChild(time);
		}
	}

	// Story
	var story = profile.getElementsByTagName("story");
	var story_box = document.getElementById("story");
	for (j = 0; j < story[0].childNodes.length; j++) {
		var story_element = story[0].childNodes[j];
		if (story_element.nodeType === 1) {

			var new_div = document.createElement("div");
			new_div.className = "info_box";


			// Add location
			var slocation = document.createElement("p");
			slocation.className = "bold_text";
			slocation.innerHTML = story_element.nodeName;

			// Add time
			var svalue = document.createElement("p");
			svalue.className = "info_text";
			svalue.innerHTML = story_element.innerHTML;


			new_div.appendChild(slocation);
			new_div.appendChild(svalue);
			story_box.appendChild(new_div);
		}
	}

	// Work

	var work_box = document.getElementById("work");
	var work = profile.getElementsByTagName('work');
	for (j = 0; j < work[0].childNodes.length; j++) {
		var work_element = work[0].childNodes[j];
		if (work_element.nodeType === 1) {

			// Add info
			var div = document.createElement("div");
			div.className = "info_box";

			var title = document.createElement("p");
			title.className = "bold_text";
			title.innerHTML = work_element.nodeName;

			// Add description
			var desc = document.createElement("p");
			desc.className = "info_text";
			desc.innerHTML = work_element.innerHTML;

			div.appendChild(title);
			div.appendChild(desc);
			work_box.appendChild(div);
		}
	}

	// Add links
	var link_div = document.getElementById("links");
	var links = profile.getElementsByTagName("links");
	for (j = 0; links[0].childNodes.length > j; j++) {
		var link = links[0].childNodes[j];
			var new_link = document.createElement('a');
			new_link.className = "info_text light_sky";
			new_link.innerHTML = link.innerHTML;
			new_link.href = link.innerHTML;
			link_div.appendChild(new_link);

			br = document.createElement("br");
			link_div.appendChild(br);
	}


}

function getGoogleMapXML(place) {
	var my_address = "http://maps.google.com/maps/api/geocode/xml?address=" + place + '&sensor=false';
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			xmlDoc = xmlhttp.responseXML;
			var GeoCodeResponse = xmlDoc.getElementsByTagName("GeocodeResponse")[0];
			var result = GeoCodeResponse.getElementsByTagName("result")[0];
			document.getElementById("place").innerHTML = result.getElementsByTagName("formatted_address")[0].innerHTML;
			var location = result.getElementsByTagName("geometry")[0].getElementsByTagName("location")[0];
			var lat = location.getElementsByTagName("lat")[0].innerHTML;
			var lng = location.getElementsByTagName("lng")[0].innerHTML;


			var myLatlng1 = new google.maps.LatLng(lat, lng);

			var mapOptions = {
				zoom: 10,
				center: myLatlng1,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById('map'),
				mapOptions);

			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function (position) {
					initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
					map.setCenter(initialLocation);
				});
			}
		}
	};
	xmlhttp.open("GET", my_address, true);
	xmlhttp.send();

}

function showSlider(file) {
    var image_div = document.getElementById("profile_pic");
    image_div.innerHTML = "";
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var xmlDoc = xmlhttp.responseXML;
            var slides = xmlDoc.getElementsByTagName("slides")[0];
            var slide_array = slides.getElementsByTagName("slide");
            var image,time,link, effect;
            var times = 0;
            var time_arr = new Array();
            var effects = new Array();
            for(var i=0;i<slide_array.length;i++) {
                image = slide_array[i].getElementsByTagName("img")[0];
                time = slide_array[i].getElementsByTagName("time")[0];
                link = slide_array[i].getElementsByTagName("link")[0];
                effect = slide_array[i].getElementsByTagName("effect")[0];
                effects.push(effect.innerHTML);
                createImg(image, link, i);
                times+= parseInt(time.innerHTML);
                time_arr.push(parseInt(time.innerHTML)/1000);
            }
            times = times/1000;
            var current = 1;
            setInterval(function(){

                    if(current == time_arr[0]) {

                        if(effects[0] == "fade") {
                            $("#img0").fadeOut(0);
                            $("#img1").fadeIn(500);
                        }
                        else {
                            $("#img0").hide();
                            $("#img1").show();
                        }
                    }
                    else if(current ==  time_arr[0] + time_arr[1]) {
                        if(effects[1] == "normal") {
                            $("#img1").hide();
                            $("#img2").show();
                        }
                        else {
                            $("#img1").fadeOut(0);
                            $("#img2").fadeIn(500);
                        }
                    }
                    else if(current ==  time_arr[0] + time_arr[1] + time_arr[2]){
                        if(effects[2] == "normal") {
                            $("#img1").hide();
                            $("#img2").show();
                        }
                        else {
                            $("#img2").fadeOut(0);
                            $("#img0").fadeIn(500);
                        }
                    }
                    current = (current)%times + 1;
                },
                1000);
        }
    };
    xmlhttp.open("GET", file, false);
    xmlhttp.send();

}

function createImg(image, link, i) {

    var image_div = document.getElementById("profile_pic");
    var a = document.createElement("a");
    a.href = link.innerHTML;
    var new_img = document.createElement("img");
    new_img.src = image.innerHTML;
    new_img.alt = "-";
    new_img.className = "image1";
    new_img.id = "img" + i;
    if(i!=0)
        new_img.style.display = "none";
    new_img.id = "img" + i;
    a.appendChild(new_img);
    image_div.appendChild(a);
}