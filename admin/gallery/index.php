<?php
session_name("dva_pet_autopub");
session_start();
if(!isset($_SESSION['id']))die();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gallery</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link href="./dropzone.css" type="text/css" rel="stylesheet" />
	<script src="./dropzone.js"></script>
  <style>
  html,body{max-height: 100%; height: 100%;overflow: hidden; padding:0; margin:0;}
  #postlist { min-height: 50px; list-style-type: none; margin: 0; padding: 0; width: 100%; float:left; }
  #postlist li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; font-size: 4em; text-align: center; }
  #alllist { min-height: 50px; list-style-type: none; margin: 0; padding: 0; width: 100%; float:left;}
  #alllist li { margin: 3px 3px 3px 0; padding: 1px; float: left; width: 100px; height: 90px; font-size: 4em; text-align: center; }
  li  {overflow:hidden!important;}
.dodatno{font-size: 0; background-color:#333;}
</style>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body>
<div style="width:48%; float:left; background-color: aliceblue; padding:1%; max-height: 100%; height: 100%; overflow-y: scroll;">
<h3>Images for article id: <?=$_GET['postid']?></h3>
<?php
if(in_array($_SESSION['id'], [1,2])){
?>
  <fieldset>
    <legend>Bulk image assignation:</legend>
	<input id="article_bulk" value="<?=$_GET['postid']?>" hidden/>
	First image ID: <input id="firstimg_bulk"/><br>
	Last image ID: <input id="lastimg_bulk"/><br>
    <input type="submit" id="BulkImage"/>
  </fieldset>
<?php
}
?>
<ul id="postlist" class="droptrue" data-postid="<?=$_GET['postid']?>">
<?php
	include('../config.php');
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
	$sql='SELECT * FROM images where article="'.$_GET['postid'].'"';
	$result = mysqli_query($con, $sql);
	while ($row = mysqli_fetch_array($result)) {
		$slika=mysqli_fetch_array(mysqli_query($con, 'SELECT * FROM gimages where id="'.$row['image'].'"'));
		$image=$slika['server'].'t/'.substr($row['image'],-1,1).'/'.substr($row['image'],-2,1).'/'.substr($row['image'],-3,1).'/'.$row['image'].'.jpg';
		echo '<li class="ui-state-default" alt="'.$slika['title'].'" data-imageid="'.$row['image'].'">
<div class="dodatno"><button class="det">Details</button><button>X</button></div>
<img src="'.$image.'"></li>';
	}
?>
</ul>
</div>
<div style="width:48%; float:left; background-color: deepskyblue; padding:1%; max-height: 100%; height: 100%; overflow-y: scroll;">
<h3>All images</h3>
<div id="dpz" class="dropzone c50"></div>
<h4 style="background-color:white; margin:5px;">Here will be search form</h4>
<ul id="alllist" class="dropfalse" data-postid="<?=$_GET['postid']?>">
<?php	
	$con = mysqli_connect($db_host, $db_user, $db_pass, $db_db);
	$sql='SELECT * FROM gimages order by id desc limit 30';
	$result = mysqli_query($con, $sql);
	while ($row = mysqli_fetch_array($result)) {
		$image=$row['server'].'t/'.substr($row['id'],-1,1).'/'.substr($row['id'],-2,1).'/'.substr($row['id'],-3,1).'/'.$row['id'].'.jpg';
		echo '<li class="ui-state-default" alt="'.$row['title'].'" data-imageid="'.$row['id'].'">
<div class="dodatno"><button class="det">Details</button><button>X</button></div>
<img src="'.$image.'"></li>';
	}
?>
</ul>
</div>
<div id="forma" style="display:none; position: absolute; top:0; left:0; width:100%; height:100%; background-color: #0e0e0e;">
    <div style="position: absolute; top:0; left:0; width:400px; padding:5px; background-color: #00bcd4;">
        <legend>Image properties:</legend>
        <fieldset style="text-align: right;">
            <img src="" style="float:left;">
            Image ID: <input id="imagep_id" value="" disabled/><br>
            Title: <input id="imagep_title"/><br>
        </fieldset>
        <button id="SubmitProp">Submit</button>
        <button id="CancelProp">Close</button>
    </div>
</div>
<script>
Dropzone.autoDiscover = false;
var myDropzone = new Dropzone("div#dpz", { 
	url: "upload.php",
	parallelUploads:1,
	createImageThumbnails:false,
	acceptedFiles:".jpg"
	});
  myDropzone.on("queuecomplete", function(file) {
    location.reload();
  });
</script>

<script>
    $( function() {
        $( "#alllist" ).sortable({
            connectWith: "#postlist"
        });
        $( "#postlist" ).sortable({
            receive: function( event, ui ) {
                //console.log($(this).attr('data-postid'),$(ui.item).attr("data-imageid"));
                var lista={};
                lista.image=$(ui.item).attr("data-imageid");
                lista.postid=$(this).attr('data-postid');
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "upload.php",
                    data: {connectimage:JSON.stringify(lista)},
                    success: function(data){
                        //console.log(data);
                    },
                    error: function(e){
                        //console.log(e);
                    }
                });
            },
            stop: function( event, ui ) {
                var lista={};
                lista.images={};
                lista.postid=$(this).attr('data-postid');
                $( "#postlist li" ).each(function( index ) {
                    var kljuc = $(this).attr('data-imageid');
                    lista['images'][kljuc]=index;
                });
                //console.log(lista);
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "upload.php",
                    data: {sortimages:JSON.stringify(lista)},
                    success: function(data){
                        //console.log(data);
                    },
                    error: function(e){
                        //console.log(e);
                    }
                });
            }
        });
        $( "#alllist, #postlist" ).disableSelection();
    } );

    $( function() {
        $( document ).tooltip({
            items: "li, [data-geo], [title]",
            content: function() {
                var element = $( this );
                if ( element.is( "li" ) ) {
                    return "ID: " + element.attr( "data-imageid" )+"<br>Title: " + element.attr( "alt" );
                }
            }
        });
    } );

    $( function() {
        $("#BulkImage").click(function(ea){
            ea.preventDefault();
            $("#BulkImage").prop('disabled', true);
            var lista={};
            lista.article_bulk=$("#article_bulk").val();
            lista.firstimg_bulk=$("#firstimg_bulk").val();
            lista.lastimg_bulk=$("#lastimg_bulk").val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "upload.php",
                data: {bulkconnectimage:JSON.stringify(lista)},
                success: function(data){
                    location.reload();
                },
                error: function(e){
                    console.log(e);
                }
            });
        });
    } );
</script>
<script>
    $('.det').click(function (e) {
        e.preventDefault();
        $('#forma #imagep_id').val($(this).parent().parent().attr('data-imageid'));
        $('#forma #imagep_title').val($(this).parent().parent().attr('alt'));
        $('#forma img').attr('src', $(this).parent().parent().find('img').attr('src'));
        $('#forma').show();
        $('#imagep_title').focus();
    })
    $('#CancelProp').click(function (e) {$('#forma').hide();});
    $("#SubmitProp").click(function(ea){
        ea.preventDefault();
        var lista={};
        lista.imagep_id=$("#imagep_id").val();
        lista.imagep_title=$("#imagep_title").val();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "upload.php",
            data: {updateimageprop:JSON.stringify(lista)},
            success: function(data){
                location.reload();
            },
            error: function(e){
                console.log(e);
            }
        });
    });
</script>
</body>
</html>