var map;
var currentMarker=[];
var markers=[];
var startDate=moment("01/01/2018", "DD/MM/YYYY");
var endDate=moment("31/12/2018", "DD/MM/YYYY");

function initMap() {
	$('#reportrange span').html(startDate.format("MM/DD/YY") + ' - ' + endDate.format("MM/DD/YY"));
	$('#reportrange').daterangepicker({
		"applyClass": "btn-default",
		"cancelClass": "btn-warning",
		locale: {
            format: 'MM/DD/YY'
        },
		"startDate":moment().subtract(29, 'days'),
		"endDate":moment(),
		ranges: {
           'All dates': [moment(), moment()],
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
		"drops": "up"
	}, function(start, end, label) {
		startDate=start;
		endDate=end;
		$('#reportrange span').html(startDate.format("MM/DD/YY") + ' - ' + endDate.format("MM/DD/YY"));
		filterMarkers();
	});

	$.ajax({
	  dataType: "json",
	  url: "returntype.php",
		success: function(e){
			$("#filterlist").append('<span href="#" class="list-group-item active">Filter by type</span>');
			for (var k in e) {
				$("#newtype").append('<option value="'+e[k].id+'">'+e[k].bdesc+'</option>');
				$("#filterlist").append(
				'<div class="list-group-item">'+
					'<img data-type="'+e[k].bdesc+'" src="'+e[k].bdesc+'.png?v=1"/> &nbsp '+e[k].bdesc+' &nbsp <input type="checkbox" checked class="toggle" data-size="mini" name="types" value="'+e[k].bdesc+'">'+
				'</div>');
			}
			$.ajax({
			  dataType: "json",
			  url: "returngroup.php",
				success: function(e){
					$("#filtergroup").append('<span href="#" class="list-group-item active">Filter by group</span>');
					for (var k in e) {
						$("#newgroup").append('<option value="'+e[k].id+'">'+e[k].bdesc+'</option>');
						$("#filtergroup").append(
						'<div class="list-group-item">'+
							e[k].bdesc+' &nbsp <input type="checkbox" checked class="toggle" data-size="mini" name="groups" value="'+e[k].bdesc+'">'+
						'</div>');
					}
					$('.toggle').bootstrapToggle();
					$("input[type=checkbox]").change(function(e) {
						filterMarkers();
					});
				}
			});
		}
	});
	
	$.ajax({
	  dataType: "json",
	  url: "returndata.php",
		success: function(e){
			var uluru=e;
			map = new google.maps.Map(document.getElementById('map'), {
				fullscreenControl: false,
				mapTypeControlOptions: {
					style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
					position: google.maps.ControlPosition.TOP_RIGHT
				}
			});
			var bounds  = new google.maps.LatLngBounds();
			for (var k in uluru) {
				$("#images").append('<img data-id="'+uluru[k].id+'" data-type="'+uluru[k].type+'" data-lat="'+uluru[k].lat+'" data-lng="'+uluru[k].lng+'" src="'+uluru[k].thumb+'"/>');
				var marker = new google.maps.Marker({
					data: uluru[k],
					position: uluru[k],
					map: map,
					icon:uluru[k].type+".png"
				});
				marker.addListener('click', function(e) {
					showDetails(this.data.id);
				});
				markers.push(marker);
				var loc = new google.maps.LatLng(marker.position.lat(), marker.position.lng());
				bounds.extend(loc);
			};
			map.fitBounds(bounds); 
			map.panToBounds(bounds);
			var circle;
			$("#images > img").hover(
				function(e){
					var loc = new google.maps.LatLng(Number(e.target.attributes['data-lat'].value), Number(e.target.attributes['data-lng'].value));
					map.panTo(loc);
					map.panBy(-100, 0);
					circle = new google.maps.Circle({
						map: map,
						radius: 500,
						fillColor: '#AA0000',
						center: loc
					});
				},
				function(e){
					circle.setMap(null);
				}
			);
		}
	});
	
	$("#images").on("click",function(e){
		var loc = new google.maps.LatLng(Number(e.target.attributes['data-lat'].value), Number(e.target.attributes['data-lng'].value));
		//map.panTo(loc);
		map.panBy(0, -100);
		showDetails(e.target.attributes['data-id'].value);
	});
};
var infowindow;
var old=[];
function showDetails(id){
	for (var k in markers) {
		if(markers[k].data.id==id){
			if(old.length!==0)old.close();
			contentString="Date taken: "+moment(markers[k].data.posttime, "YYYY-MM-DD").format("MM/DD/YY")+"<br>"+
			markers[k].data.comments + '<br><div class="holder"><img height="200" id="newfullimage" src="'+markers[k].data.thumb+'"/></div>';
			infowindow = new google.maps.InfoWindow();
			infowindow.setContent(contentString);
			infowindow.setPosition(markers[k].position)
			//infowindow.open(map);
			infowindow.open(map, markers[k]);
			old=infowindow;
			var url=markers[k].data.img;
			setTimeout(function(){
				$.get(url, function(data, status){
					$("#newfullimage").attr("src", url);
				});
			},500);


			$("#newfullimage").on("click",function(e){
				var cur=infowindow.anchor;
				$.each(BootstrapDialog.dialogs, function(id, dialog){
					dialog.close();
				});
				BootstrapDialog.show({
					cssClass: 'imagepreview',
					modal: true,
					title: "Date taken: "+moment(cur.data.posttime, "YYYY-MM-DD").format("MM/DD/YY")+
					"<br>"+cur.data.comments,
					message: '<img id="newfullimagee" src="images/loading_circle.gif"/>',
					draggable: false,
					animate: false
				});
				var url=cur.data.fullimg;
				$.get(url, function(data, status){
					$("#newfullimagee").attr("src", url);
				});
			});
		}
	}
};

function filterMarkers(){
	var filtertypes=[];
	$('input[type=checkbox][name=types]').each(function () {
		var sThisVal = (this.checked ? $(this).val() : false);
		if(sThisVal)filtertypes.push(sThisVal);
	});
	
	var filtergroups=[];
	$('input[type=checkbox][name=groups]').each(function () {
		var sThisVal = (this.checked ? $(this).val() : false);
		if(sThisVal)filtergroups.push(sThisVal);
	});
	
	setMapOnAll(filtertypes,filtergroups);
}

function setMapOnAll(types,groups) {
	for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(null);
	}
	var bounds  = new google.maps.LatLngBounds();
	for (var i = 0; i < markers.length; i++) {
		var compareDate = moment(markers[i].data.posttime, "YYYY-MM-DD");
		var a=compareDate.isBetween(startDate, endDate);
		if(types.indexOf(markers[i].data.type)>=0 && groups.indexOf(markers[i].data.group)>=0 && a){
			markers[i].setMap(map);
			var loc = new google.maps.LatLng(markers[i].position.lat(), markers[i].position.lng());
			bounds.extend(loc);
			$('img[data-id='+markers[i].data.id+']').show();
		}else{
			$('img[data-id='+markers[i].data.id+']').hide();
		}
	}
};

$(document).ready(function (e) {
	$("#uploadimage").on('submit',(function(e) {
		e.preventDefault();
		$("#taken2").val(moment($("#taken").val(), "MM/DD/YY").format("YYYY-MM-DD"));
		$("#message").empty(); 
		$('#loading').show();
		$.ajax({
        	url: "upload.php",
			type: "POST",
			data:  new FormData(this),
			contentType: false,
    	    cache: false,
			processData:false,
			success: function(data){
				$('#loading').hide();
				$("#message").html(data);			
		    }
		});
	}));

	$(function() {
		$("#file").change(function() {
			$("#message").empty();
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];	
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2]))){
				$('#previewing').attr('src','images/noimage.png');
				$("#message").html("Please select valid image.");
				return false;
			}else{
                var reader = new FileReader();	
                reader.onload = imageIsLoaded;
                reader.readAsDataURL(this.files[0]);
				
				//Read exif
				var file = this.files[0];
				if (file && file.name) {
					EXIF.getData(file, function() {
						if(EXIF.getTag(this, 'GPSLatitude')){
							var latt=toDecimal(EXIF.getTag(this, 'GPSLatitude'));
							var lonn=toDecimal(EXIF.getTag(this, 'GPSLongitude'));
							var taken=moment(EXIF.getTag(this, 'DateTimeOriginal'), "YYYY:MM:DD");
							if(this.exifdata.GPSLatitudeRef=="S")latt=latt*-1;
							if(this.exifdata.GPSLongitudeRef=="W")lonn=lonn*-1;
							$("#lat").val(latt);
							$("#lon").val(lonn);
							$("#message").append("Location data are found in exif.");
							if(EXIF.getTag(this, 'DateTimeOriginal')){
								$("#taken").attr("disabled",true);
								$("#taken").val(taken.format("MM/DD/YY"));
							}else{
								$("#message").append("<br>Date when photo is captured is not found in exif data. Please select date.");
								$("#taken").attr("disabled",false);
								$("#taken").daterangepicker({
									locale: {
										format: 'MM/DD/YY'
									},
									singleDatePicker: true,
									showDropdowns: true
								});
							}
				
							//Preview map
							var myLatLng = {lat: latt, lng: lonn};
							bounds  = new google.maps.LatLngBounds();
							var previewmap = new google.maps.Map(document.getElementById('previewmap'), {
							  zoom: 10,
							  center: myLatLng
							});
							var marker = new google.maps.Marker({
							  position: myLatLng,
							  map: previewmap
							});
							loc = new google.maps.LatLng(marker.position.lat(), marker.position.lng());
							bounds.extend(loc);
							//previewmap.fitBounds(bounds); 
							//previewmap.panToBounds(bounds);
						}else{
							$("#message").append("<br>Location data is not found in exif. We will try to use your current location.");
							$("#taken").attr("disabled",false);
							$("#taken").daterangepicker({
								locale: {
									format: 'MM/DD/YY'
								},
								singleDatePicker: true,
								showDropdowns: true
							});
							if (navigator.geolocation) {
							  navigator.geolocation.getCurrentPosition(function(position) {
								  $("#lat").val(position.coords.latitude);
								  $("#lon").val(position.coords.longitude);
									var mapa = new google.maps.Map(document.getElementById('previewmap'), {
									  zoom: 10,
									  center: {lat: position.coords.latitude, lng: position.coords.longitude}
									});
									bounds  = new google.maps.LatLngBounds();
									var marker = new google.maps.Marker({
										position: {lat:position.coords.latitude, lng:position.coords.longitude},
										map: mapa
									});
									loc = new google.maps.LatLng(marker.position.lat(), marker.position.lng());
									bounds.extend(loc);
									//mapa.fitBounds(bounds); 
									//mapa.panToBounds(bounds);
							  }, function(){
									$("#message").append("<br>We can't determine your location. Please select location on map.");
									var mapa = new google.maps.Map(document.getElementById('previewmap'), {
										zoom: 10,
										center: {lat:43.302936, lng:-25.359292}
									});
									currentMarker = new google.maps.Marker({
										position: {lat:43.302936, lng:-25.359292},
										map: mapa
									});
									mapa.addListener('click', function(e) {
										currentMarker.setMap(null);
										currentMarker = new google.maps.Marker({
											position: e.latLng, 
											map: mapa
										});
										$("#lat").val(currentMarker.position.lat);
										$("#lon").val(currentMarker.position.lng);
									});
							  });
							}
						}
					});
				}
			}
		});
    });
	
	function imageIsLoaded(e) {
		$("#file").css("color","green");
        $('#previewing').attr('src', e.target.result);
	};
			
	$("#maindivbtnS").on('click',function(e) {
		$('#maindiv').css("left", "10px");
		$('#maindivbtnH').css("display", "block");
		$('#maindivbtnS').css("display", "none");
	});
	
	$("#maindivbtnH").on('click',function(e) {
		$('#maindiv').css("left", "-200px");
		$('#maindivbtnS').css("display", "block");
		$('#maindivbtnH').css("display", "none");
	});
	
	$("#bottombtnS").on('click',function(e) {
		//$('#addnewbtn').css("left", "10px");
		$('#images').css("left", "85px");
		$('#images').css("right", "50px");
		$('#bottombtnH').css("display", "block");
		$('#bottombtnS').css("display", "none");
	});
	
	$("#bottombtnH").on('click',function(e) {
		//$('#addnewbtn').css("left", "-2000px");
		$('#images').css("left", "-2000px");
		$('#images').css("right", "20000px");
		$('#bottombtnS').css("display", "block");
		$('#bottombtnH').css("display", "none");
	});
		
	var toDecimal = function (number) {
		return number[0] + number[1] / 60 + number[2] / 3600;
	};
});