<?php
?>
<!DOCTYPE html>
<html>
<head>

	<title>Image Recognition Programme</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" href="css/ae_base.css">
	<link rel="stylesheet" href="autocomplete/easy-autocomplete.css" type="text/css" />
	<link rel="stylesheet" href="autocomplete/easy-autocomplete.themes.css" type="text/css" />
	<link href="https://fonts.googleapis.com/css?family=Quicksand" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	<link href="css/jquery.selectareas.css" rel="stylesheet">

	<script src="js/jquery-3.2.1.js" type="text/javascript"></script>	
	<script src="js/jquery.selectareas.js" type="text/javascript"></script>
	<script src="autocomplete/jquery.easy-autocomplete.js" type="text/javascript"></script>
<?php
		if ( isset($_GET['image']) && !empty($_GET['image'])) {
					echo "<style>body{border:5px solid red;}</style>";
		}
	?>
<script>
var selection_xy = [];
var tag_error_raised = false;
var new_zone_created = false;
var init_finished = false;
var current_area = null;
MESSAGE_EMPTY_TAG = "You need to enter a tag here";
RED_COLOR = "#C70039";
GREEN_COLOR = "#80A60E";
var changeStatusMessage = false;

var json_annotations = [];
var list_of_tags = [];
var current_visible_area_id = -1;
/*$("#image_to_process").on('load', function() {
 alert("On Load");
})*/

/*$(window).load(function(){

   alert($("#image_to_process").width());

});*/

var user_id = "";
var image_info = {
                url: "",
				id:"",
				folder: "",
                width: 0,
                height: 0,
				annotations: [],
            };

var lastLoadedWidth  = 0;
var lastLoadedHeight = 0;
var firstWidth = 0;
$(document).ready(function ()
{
	$('#image_to_process').on("load", function()
	{
	    console.log("INFO: " + "on('load', function() ");
		changeStatusMessage = false;

		lastLoadedWidth = $('#image_to_process').get(0).naturalWidth;
		lastLoadedHeight = $('#image_to_process').get(0).naturalHeight;

		image_info.width  = lastLoadedWidth;
		image_info.height = lastLoadedHeight;

		console.log("INFO: " + "image_info.width = "  + image_info.width);
		console.log("INFO: " + "image_info.height = " + image_info.height);

		if (firstWidth == 0)
		{
			firstWidth = $('#image_to_process').get(0).width;
		}

		ratio = lastLoadedWidth / lastLoadedHeight;

		col2_width = 2*$(window).width() / 3;
		// Image is to large
		if (image_info.width < col2_width)
		{
			col2_width = image_info.width;
		}

		image_screen_width = col2_width;
		image_screen_height = image_screen_width/ratio;

		// Image is to high
		if (image_screen_height > $(window).height()*0.75)

		{
			image_screen_height = parseInt($(window).height()*0.75,10);
			image_screen_width = parseInt(image_screen_height*ratio,10);
			col2_width = image_screen_width;

		}

		// alert("New image size = " + image_screen_width + "px , " + image_screen_height + "px");

		remaining_x_space = $(window).width() - col2_width;
		col1_width = 1*remaining_x_space /6;
		col3_width = 4*remaining_x_space /6;
		col4_width = 1*remaining_x_space /6;

		$("#image_to_process").attr({width:image_screen_width+"px"});
		$("#image_to_process").attr({height:image_screen_height+"px"});

		// col1_width = ($(window).width() - $('#image_to_process').get(0).width - 350) /2;
		// col2_width = $('#image_to_process').get(0).width;
		// col3_width = ($(window).width() - $('#image_to_process').get(0).width - 350) /2;

		console.log("INFO: " + "$(window).width() = " + $(window).width());
		console.log("INFO: " + "$(window).height() = " + $(window).height());

		console.log("INFO: " + "col1_width = " + col1_width);
		console.log("INFO: " + "col2_width = " + col2_width);
		console.log("INFO: " + "col3_width = " + col3_width);
		console.log("INFO: " + "col4_width = " + col4_width);

		$("#col1").css("width",col1_width+"px");
		$("#col2").css("width",col2_width+"px");
		$("#col3").css("width",col3_width+"px");
		$("#col4").css("width",col4_width+"px");

		// image_screen_width = 600;
		// image_screen_height = image_screen_width/ratio;
		// Set image dimension
		//$("#image_to_process").attr({width:image_screen_width+"px"});
		//$("#image_to_process").attr({height:image_screen_height+"px"});

		ratio_original_to_screen_x = image_screen_width / lastLoadedWidth;
		ratio_original_to_screen_y = image_screen_height / lastLoadedHeight;

		/*$('#image_to_process').selectAreas(
			{
				allowNudge: false,
				// onChanged:onAreaChanged,
				onDeleted:onAreaDeleted,
			});		*/

		// Loop on tag info
		json_annotations.forEach(function(element) {
			var areaOptions = {
				x: (element.x * ratio_original_to_screen_x),
				y: (element.y * ratio_original_to_screen_y),
				width: (element.width * ratio_original_to_screen_x),
				height: (element.height * ratio_original_to_screen_y),
				tag:element.tag, };

			// We have to convert x, y and size to image in the HTML page
            console.log('Area options');
            console.log(areaOptions);
			$('#image_to_process').selectAreas('add', areaOptions);
		});


<?php
	if(isset($_GET['image']) && !empty($_GET['image'])) {
		echo "var addPrevious = false;";
	} else {
		echo "var addPrevious = true;";
	}
?>
        // Add anotations from previous image
        if (JSON.parse(localStorage.getItem("lastAnnotations")) != null && addPrevious) {
            var annotations = JSON.parse(localStorage.getItem("lastAnnotations"));
            annotations = annotations.annotations;
            for (var i = 0, max = annotations.length; i < max; i++) {
                var areaOptions = {
                    x: (annotations[i].x * ratio_original_to_screen_x),
                    y: (annotations[i].y * ratio_original_to_screen_y),
                    width: (annotations[i].width * ratio_original_to_screen_x),
                    height: (annotations[i].height * ratio_original_to_screen_y),
                    tag: annotations[i].tag };

                $("#image_to_process").selectAreas('add', areaOptions);
            }
        }

		// Get list of areas
		var areas = $('#image_to_process').selectAreas('areas');
		if (areas.length>0)
		{
			// Selected area is the first one
			current_area = areas[0];
			// Select first area
			onAreaChanged(null, current_area.id, areas)
			$('#image_to_process').selectAreas('set', current_area.id);
		}
		else
		{
			current_area = null;
		}

	});

	var options = {
		//data: list_of_tags,

		url: "resources/list_of_tags.json",
		getValue: "name",

		 template: {
			type: "iconRight",
				fields: {
					iconSrc: "icon"
					}
			},


		list: {
			maxNumberOfElements: 8,

			match: {
				enabled: true
			},

			sort: {
				enabled: true
			},

			onClickEvent: function() {
				isTagInAuthorizedList()
			},

			/*onKeyEnterEvent: function() {
				alert("Enter !");
			},
			onChooseEvent: function() {
				alert("onChooseEvent !");
			},*/

		},
	};
	$('#image_to_process').on("changed", function(event, id, areas)
	{
		// console.log("INFO: " + "index.html on(changed)");

		for (var i = 0; i < areas.length; i++)
		{
			// console.log("INFO: " + "area " + areas[i].id);
			if (areas[i].id == id)
			{
				area = areas[i];
				current_area = area;
			}
		}

		if (area == null)
		{
			console.log("INFO: area is null return");
			current_area = null;
			return;
		}

		// Add the top/left offset of the image position in the page
		var region_tag_length = $("#tag_input").val().length;

		// Set back the area tag if stored
		var stored_tag = area.tag
		if (stored_tag.length >= 3)
		{
			setValidInputTag(stored_tag);
			region_tag_length = area.tag.length;
		}
		else
		{
			// Set en empty tag
			setNotYetValidInputTag("");
			region_tag_length = 0;
		}

		// Do not focus if tag is not empty and at begining or after new zone creation
		if ( (init_finished) && (region_tag_length==0) )
		{
			$("#tag_input").focus();
		}
		else
		{
			$("#tag_input").focusout();
		}
		new_zone_created = false

		// Check region size
		/*if (isAreaTooSmall(area))*/
		if (isAreaTooSmall(area))
		{
			setStatusAndColor("The region is too small (must be >80px).", RED_COLOR)

			newWidth = area.width;
			newHeight = area.height;

			if (newWidth < 80)
			{
				newWidth = 80;
			}

			if (newHeight < 80)
			{
				newHeight = 80;
			}

			$('#image_to_process').selectAreas('resizeArea', current_area.id, newWidth, newHeight);
		}
		else
		{
			if (changeStatusMessage)
			{
				setStatus("Region has been selected.");
			}
		}

	});
	$("#tag_input").easyAutocomplete(options);

	new_zone_created = false

	$('#add_region').click(function(e) {
		// Sets a random selection
			setTagAndRegion();
		});

	$('#validate_button').click(function(e) {
		// Sets a random selection
			validateTagsAndRegions();
		});

	$('#all_annotations_button').click(function(e) {
	        // Show all annotations
			current_visible_area_id = -1;
			var areas = $('#image_to_process').selectAreas('areas');

			for (var i = 0; i < areas.length; i++)
			{
				$('#image_to_process').selectAreas('setVisibility',  areas[i].id, 1);
			}
	});

	$('#none_annotation_button').click(function(e) {
		    // Hide annotations
			var areas = $('#image_to_process').selectAreas('areas');
			current_visible_area_id = -1;

			for (var i = 0; i < areas.length; i++)
			{
				$('#image_to_process').selectAreas('setVisibility',  areas[i].id, 0);
			}
        });

	$('#one_annotation_button').click(function(e) {

			 // Hide annotations
			var areas = $('#image_to_process').selectAreas('areas');
			current_visible_area_id++;

			/*if (areas.length==0)
			{
			    $('#one_annotation_button').button( "option", "label", "");
			}*/

			if (current_visible_area_id+1 > areas.length)
			{
				current_visible_area_id = 0;
			}

			for (var i = 0; i < areas.length; i++)
			{
				// alert("i " + i);
				// alert("current_visible_area_id " + current_visible_area_id);

				if (i == current_visible_area_id)
				{
					$('#image_to_process').selectAreas('setVisibility',  areas[i].id, 1);
					// $('#one_annotation_button').button( "option", "label", "areas[i].tag");
				}
				else
				{
				    $('#image_to_process').selectAreas('setVisibility',  areas[i].id, 0);
				}
			}

		});

	$("#tag_input").focus(function() {

		if (tag_error_raised)
		{
			tag_error_raised = false
			$("#tag_input").val("");
			$('#tag_input').css({'color':'#000'});
		}
	});

	$("#tag_input").focusout(function() {
		isTagInAuthorizedList()
	});

	$("#tag_input").on('keyup', function (e) {
		if (e.keyCode == 13)
		{
		    // alert("Entering #13");
			var region_tag = $("#tag_input").val();
			if (region_tag.length >= 3)
			{
				setTagAndRegion();
			}
		}
	});

	init_finished = true;
})

function setValidInputTag(_tag)
{
	$("#tag_input").val(_tag);
	isTagInAuthorizedList();
}

function setNotYetValidInputTag(_tag)
{
	$("#tag_input").val(_tag);
	isTagInAuthorizedList();
}

function isTagInAuthorizedList()
{
	var region_tag = $("#tag_input").val();

	list_of_tags = $("#tag_input").getAllItemData();

	if (list_of_tags.indexOf(region_tag)>=0)
	{
		$('#tag_input').css({'color':'#14AEE1'});
		return true;
	}
	else
	{
		$('#tag_input').css({'color':'#000'});
		return false;
	}

}

  function onAreaDeleted(event, id, areas)
  {
	if (current_area !=null)
	{
		if (current_area.id == id)
		{
			current_area = null;
			setNotYetValidInputTag("");
		}
	}
	// if current unset tag @todo later
  }


    function onAreaChanged(event, id, areas)
	{
		// alert(id);

		// console.log("INFO: " + "onAreaChanged() " + event);
		// Find area by id
		for (var i = 0; i < areas.length; i++)
		{
			// console.log("INFO: " + "area " + areas[i].id);
			if (areas[i].id == id)
			{
				area = areas[i];
				current_area = area;
			}
		}

		if (area == null)
		{
			console.log("INFO: area is null return");
			current_area = null;
			return;
		}

		// Add the top/left offset of the image position in the page
		var region_tag_length = $("#tag_input").val().length;

		// Set back the area tag if stored
		var stored_tag = area.tag
		if (stored_tag.length >= 3)
		{
			setValidInputTag(stored_tag);
			region_tag_length = area.tag.length;
		}
		else
		{
			// Set en empty tag
			setNotYetValidInputTag("");
			region_tag_length = 0;
		}

		// Do not focus if tag is not empty and at begining or after new zone creation
		if ( (init_finished) && (region_tag_length==0) )
		{
			$("#tag_input").focus();
		}
		else
		{
			$("#tag_input").focusout();
		}
		new_zone_created = false

		// Check region size
		if (isAreaTooSmall(area))
		{
			setStatusAndColor("The region is too small (must be >80px).", RED_COLOR)

			newWidth = area.width;
			newHeight = area.height;

			if (newWidth < 80)
			{
				newWidth = 80;
			}

			if (newHeight < 80)
			{
				newHeight = 80;
			}

			$('#image_to_process').selectAreas('resizeArea', current_area.id, newWidth, newHeight);
		}
		else
		{
			if (changeStatusMessage)
			{
				setStatus("Region has been selected.");
			}
		}
	}

	function isAreaTooSmall(area)
	{
		return false;
		if ((area.width<80) || (area.height<80))
		{
			return true;
		}
		else
		{
			return false;
		}

	}

  function px(n) {return Math.round(n) + 'px';	}

  function setStatusAndColor(status_text, color)
  {
	$('#status').css('color', color);
	$("#status").text(status_text);
  }

  // Default color is black
  function setStatus(status_text)
  {
	 setStatusAndColor(status_text, "#000");
  }

  function setTagAndRegion()
  {

	changeStatusMessage = true;

	if (tag_error_raised)
	{
		return false;
	}

	if (current_area == null)
	{
		onAreaChanged(null, current_area.id, areas);
	}

	if (isAreaTooSmall(current_area))
	{
		setStatusAndColor("Tag was not set, the region is too small.", RED_COLOR)
		return false;
	}

	var region_tag = $("#tag_input").val();

	if (region_tag.length < 3)
	{
	    // Display an alert
		tag_error_raised = true
		$("#tag_input").val(MESSAGE_EMPTY_TAG);
		$('#tag_input').css({'color':'#999'});

		return false;
	}

	if (!isTagInAuthorizedList())
	{
		setStatusAndColor("This tag is not in the predefined list.", RED_COLOR);
		return false;
	}

	// Just change the tag, get area, id, ...
	$('#image_to_process').selectAreas('setTag', current_area.id, region_tag);
	setStatus("Tag has been set.");

	// Selection another box
	new_zone_created = true	;

	// Init text
	$("#tag_input").focusout();

	return true;
  }

  function validateTagsAndRegions()
  {

     // Process the list of tags
	 var areas = $('#image_to_process').selectAreas('areas');

	 if (areas.length == 0)
	 {
		setStatusAndColor("Create at least one region with a tag before submitting.", RED_COLOR);
		return false;
	 }

	index_tag = 0;
	ratioX = lastLoadedWidth/$('#image_to_process').width();
	ratioY = lastLoadedHeight/$('#image_to_process').height();
	tmp_annotations = [];

	var error_occurs = false;
	$.each(areas, function (id, area) {
	    var tag_info = {tag:"",x:0,y:0,width:0,height:0};
		tag_info.tag = area.tag;
		tag_info.x = area.x * ratioX;
		tag_info.y = area.y * ratioY;
		tag_info.width = area.width * ratioX;
		tag_info.height = area.height * ratioY;
		// To be checked
		tmp_annotations.push(tag_info);
		// image_info.annotations[index_tag] = tag_info;
		index_tag = index_tag + 1;

		if (area.tag.length<3)
		{
			if (areas.length==1)
			{
				setStatusAndColor("You should add a tag to the region.", RED_COLOR);
			}
			else
			{
				setStatusAndColor("You should add a tag to each region.", RED_COLOR);
			}
			error_occurs = true;
			return false;
		}

	});

	if (error_occurs) return false;

	// image_info.annotations = JSON.stringify(tmp_annotations);
	image_info.annotations = tmp_annotations;
	//alert(image_info.annotations);
	console.log("INFO: annotations : " + image_info.annotations);
	console.log("INFO: annotations : " + JSON.stringify(image_info));
      if ($('#remember').is(':checked')) {
          localStorage.setItem("lastAnnotations", JSON.stringify(image_info));
      }
      /*return;*/
      console.log('Ajax');
	 $.ajax({
       url : 'inc/validateTagsAndRegions.php',
       type : 'POST',
	   data : {sendInfo : JSON.stringify(image_info) },
	   cache: false,
	   dataType : "json",
	   success : function(data, status)
	   {
	       // Reload a page but with a message

           /**
            * TODO temporary
            */

           setStatusAndColor("Data has been sent.", GREEN_COLOR);
		   loadImage(); // replace the image
		   getOrCreateUserId(); // Count also points later on

		   // Message to user
		   $("#message").text(data.message);
		   $('#image_to_process').selectAreas('reset');
		   image_info = {
                url: "",
				folder: "",
				id:"",
                width: 0,
                height: 0,
				annotations: [],
            };

	   },
	   error : function(data, status, error)
	   {
			setStatusAndColor("Unable to send data, please retry or check your connection.", RED_COLOR)
       }
    });

  }

  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

  function getOrCreateUserId()
  {

    user_id = getCookie("user_id");

	if (user_id != "")
	{
		// The user is known
		$("#message").text("Welcome back !");
    }
	else
	{
		// Create a cookie to identify user
		var date = new Date();
		date.setTime(date.getTime() + (365*24*60*60*1000)); // ms
		expires = "; expires=" + date.toUTCString();
		user_id = getRandomToken();
		document.cookie = "user_id="+ user_id + expires + "; path=/";
		$("#message").text("Welcome !");
    }

  }

  function getRandomToken()
  {
    // E.g. 8 * 32 = 256 bits token
    var randomPool = new Uint8Array(32);

    var crypto = window.crypto || window.msCrypto;

    crypto.getRandomValues(randomPool);
    var hex = '';
    for (var i = 0; i < randomPool.length; ++i) {
        hex += randomPool[i].toString(16);
    }
    // E.g. db18458e2782b2b77e36769c569e263a53885a9944dd0a861e5064eac16f1a
    return hex;
  }

  function loadImage()
  {

	  var dataString = JSON.stringify(user_id);
	  console.log("INFO: " + "loadImage() from php");

	  $.ajax({
			type: 'POST',
			url: 'inc/getNewImage.php',
			data: {user_id: dataString, image: '<?= $_GET['image'] ?>'},
			dataType: "json",
			success: function(data)
					 {
					 	console.log(data);
					    var dataJson = JSON.parse(data);
					     console.log(dataJson);
					     $('#image-headline').text(dataJson.id);
						image_info.url = dataJson.url;
						image_info.id = dataJson.id;
						image_info.folder = dataJson.folder;
						image_id   = dataJson.id;
						json_annotations = dataJson.annotations;
						$('#image_id').text(image_id);
						$('#image_to_process').attr("src", image_info.url);

					 },
			dataType: 'html'
		});


	}

</script>

</head>

<body>
<style>

    .centered-content > div:first-child {
        float: left;
        margin-left: 35px;
    }

    .tag-area {
        z-index: 9999;
    }

</style>
<h3 id="image-headline" style="text-align: center;"></h3>
<div class="centered-content" style="background-color:#fff; float: left;">

		<!-- Page content -->

							<img id="image_to_process" src="./images/wait.gif">

							<!--<div style="background-color:#fff">
								<img style="display: block;margin: auto;vertical-align: middle;" src="./images/logo.png" width="100" height="100">
							</div>-->

							 <!--<div style=" padding:2px 2px;background-color:#fff;text-align: center;">
								<span class="serif_medium">Images Annotation Programme</span>
						     </div>-->
<div id="form" style="width: 460px; float:right; margin-right: 56px;">
						     <div style="display: inline-block; width:100%; background-color:#fff;padding-top: 6px; padding-bottom: 3px">
                                 <form action="">
                                     Remember last boxes? <input id="remember" type="checkbox" checked>
                                 </form>
							   <div style="float:left; width:50% ;padding-top: 12px; padding-bottom: 6px">
									<div style="float:left; padding-right: 8px">
										<img src="./images/label_black.png" alt="" width="18" height="18">
									</div>

									<div style="float:left" class="serif_small">
										Tag {Truck, Tractor, Trailer}
									</div>

								</div>

								<div style="text-align:right;float:right;padding-top: 20px;width:50% " class="ae_small_90">
										<span class="ae_small_90" id="image_id">image_001.jpg</span>
								</div>

							</div>

							<div style="padding-top:0px;text-align:left;">
								<input type="text" id="tag_input" name="tag_input">
							</div>

							<div style="padding-top: 4px" class="ae_small_90">
								<span id="status"  id="image_id">> Select a region from the picture.</span>
							</div>

							<div style="padding-top:12px;text-align: right;">
								<button id="add_region" class="tooltip ae_button_level_1">Set Tag
								<button id="clean" class="tooltip ae_button_level_1">Clean
								<span style="padding-left:4px;">
								<img src="./images/ic_add_black_48dp.png" style="vertical-align: bottom;" alt="" width="18" height="18"></span>
								<span class="tooltiptext light_green">Add highlighted region with associated tag.</span></button>
							</div>

							<div style="padding-top:24px;box-sizing: border-box;width=100%;text-align: right;">
								<button id="validate_button" class="tooltip ae_button_level_1">Validate & Get next Image
								<span class="tooltiptext light_green">Validate all the tags added to this image and go to the next image.</span>
								<span style="padding-left:4px;">
								<img src="./images/ic_navigate_next_black_48dp.png" style="vertical-align: bottom;" alt="" width="18" height="18"></span></button>
							</div>
                        <br>
                        <hr>
							 <div style="display: inline-block; width:100%; background-color:#fff;padding-top: 6px; padding-bottom: 3px">
							    <div style="float:left; width:50% ;padding-top: 12px; padding-bottom: 3px">
									<div style="float:left; padding-right: 8px">
										<img src="./images/label_collection.png" alt="" width="18" height="18">
									</div>
									<div style="float:left" class="serif_small">
										Tags View
									</div>
								</div>
							</div>
														
							 <div style="width:100%; background-color:#fff;padding-top: 3px;">
								<button id="all_annotations_button" class="tooltip ae_button_level_3">All
								<span class="tooltiptext light_violet">Display all Annotations</span>
								<span style="padding-left:4px;">
								<img src="./images/label_all_annotations.png" style="vertical-align: bottom;" alt="" width="18" height="18"></span></button>
								
								<button id="none_annotation_button" class="tooltip ae_button_level_3">None
								<span class="tooltiptext light_violet">Hide the Annotations</span>
								<span style="padding-left:4px;">
								<img src="./images/label_none_annotations.png" style="vertical-align: bottom;" alt="" width="18" height="18"></span></button>
								
								<button id="one_annotation_button" class="tooltip ae_button_level_3">
								<span class="tooltiptext light_violet">Click to display next annotation</span>
								<span style="padding-left:4px;">
								<img src="./images/ic_navigate_next_black_48dp.png" style="vertical-align: bottom;" alt="" width="18" height="18"></span></button>
								
							</div>
														
														
							<div style="padding-top:96px;box-sizing: border-box;width=100%;text-align: right;">
								<button onclick="test()" class="tooltip ae_button_level_2">Ignore & Get next image
								<span class="tooltiptext light_blue">Ignore this image and associated tags and go to the next image.</span>
								<span style="padding-left:4px;">
								<img src="./images/ic_replay_black_48dp.png" style="vertical-align: bottom;" alt="" width="18" height="18"></span></button>
							</div>

							<!--<div style="padding-top:24px;box-sizing: border-box;width=100%;text-align: right;">
								<button onclick="window.location.href='thank_you.html'" class="tooltip ae_button_level_2">Leave
								<span class="tooltiptext light_blue">Leave this page, cancel all tags of this image. Validated tags will be kept.</span>
								<span style="padding-left:4px;">
								<img src="./images/ic_exit_to_app_black_48dp.png" style="vertical-align: bottom;" alt="" width="18" height="18"></span></button>
							</div>-->

							<div style="padding-top: 12px" class="ae_small_80">
								<span id="message" class="blue_color_text" style="float:right;"></span>
							</div>
							<?php
                            $file="./data/ignored.txt";
                            $ignored = -1;
                            $handle = fopen($file, "r");
                            while(!feof($handle)){
                                $line = fgets($handle);
                                $ignored++;
                            }
								$data = new FilesystemIterator('./data/images/collection_01/part_4', FilesystemIterator::SKIP_DOTS);
								/*$data2 = new FilesystemIterator('./data/images/collection_01/part_2', FilesystemIterator::SKIP_DOTS);
								$data3 = new FilesystemIterator('./data/images/collection_01/part_3', FilesystemIterator::SKIP_DOTS);*/
								$anotated = new FilesystemIterator('./data/annotations/', FilesystemIterator::SKIP_DOTS);

                            $file="./data/ignored.txt";
                            $ignored = -1;
                            $handle = fopen($file, "r");
                            while(!feof($handle)){
                                $line = fgets($handle);
                                $ignored++;
                            }

                            fclose($handle);
							?>
							<ul>
								<li>Images in collection: <strong><?= iterator_count($data) ?></strong></li>
								<li>Annotated: <?= iterator_count($anotated) ?></li>
                                <li>Ignored: <?= $ignored ?></li>
                                <li>Sum: <?= iterator_count($anotated) . ' + ' . $ignored . ' = ' . '<strong>' . (iterator_count($anotated) + $ignored) . '</strong>' ?></li>
                                <li>Images left: <strong> <?= iterator_count($data) - (iterator_count($anotated) + $ignored) ?> </strong></li>
							</ul>
    <!-- <ul>
        <li>
            <strong>Reviewed:</strong> <?= $ignored ?>
        </li>
    </ul> -->
</div>




		</div>

<script>
   getOrCreateUserId();
   loadImage();
    $(function(){
    	$('#clean').on('click', function (e) {
            e.preventDefault();
            $('#image_to_process').selectAreas('reset');
        });
        /*$('#remember').on('click', function() {
            if(!$(this).is(':checked')){

            }
        });*/
    });
    $(window).on('keypress',function(e) {
            var key = e.which;
            if (key == 122) { //z
                $('#all_annotations_button').click();
            }
            if (key == 121) { //y
                $('#none_annotation_button').click();
            }
            if (key == 100) { //d
                $('#one_annotation_button').click();
            }
        });
    function test () {
        $.ajax({
            url : '/ignore.php',
            type : 'POST',
            data : {image : $('#image-headline').text() },
            cache: false,
            success : function()
            {
                console.log('Image was putted into ignore list.');
                location.reload();
            }
        });
    }
</script>
</body>


</html>