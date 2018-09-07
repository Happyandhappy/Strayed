<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Strayed</title>
		<link href="css/bootstrap-theme.min.css" rel="stylesheet">
		<link href="css/bootstrap-toggle.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
		<link href="css/bootstrap-dialog.css" rel="stylesheet" type="text/css" />
		<link href="css/style.css" rel="stylesheet">
    </head>
    <body> 
		<div id="map"></div>
<!--<div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, autoDisplay: false}, 'google_translate_element');
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>-->
		<div id="maindiv">
			<div class="list-group" id="filterlist"></div>
			<div class="list-group" id="filtergroup"></div>
			<div class="list-group" id="filterdate">
				<span href="#" class="list-group-item active">Filter by date</span>
				<div id="reportrange" class="" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
					<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
					<span></span> <b class="caret"></b>
				</div>
				<button id="addnewbtn" class="btn btn-default" data-toggle="modal" data-target="#addnewmodal">Add to map</button>
				<!--<div id="addnewbtn"><img title="Add new" src="addnew.png"  data-toggle="modal" data-target="#addnewmodal"/></div>-->
				<!--<input type="text" name="daterange" class="form-control"/>-->
			</div>
			<button id="maindivbtnS" class="btn"><i class="glyphicon glyphicon-chevron-right"></i></button>
			<button id="maindivbtnH" class="btn"><i class="glyphicon glyphicon-chevron-left"></i></button>
		</div>
		<button id="bottombtnS" class="btn"><i class="glyphicon glyphicon-chevron-up"></i></button>
		<button id="bottombtnH" class="btn"><i class="glyphicon glyphicon-chevron-down"></i></button>
		<div id="images"></div>
		
		<script src="js/jquery.min.js"></script>
		<script src="js/exif.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-toggle.min.js"></script>
		<script type="text/javascript" src="js/moment.min.js"></script>
		<script type="text/javascript" src="js/daterangepicker.js"></script>
		<script type="text/javascript" src="js/bootstrap-dialog.js"></script>
		<script src="js/script.js"></script>
		<script async defer src="//maps.googleapis.com/maps/api/js?key=AIzaSyDtqvj5_oYmqjVS4N1rKanuK5gscJp67_8&callback=initMap"></script>

<div id="addnewmodal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add new</h4>
      </div>
      <div class="modal-body">
		<form class="form-horizontal" role="form" id="uploadimage" action="" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="file" class="col-sm-2 control-label">
					Select Image
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="file" name="file" id="file" required />
				</div>
			</div>
			<div class="alert alert-warning" id="message"></div>
			<div class="form-group">
				<label for="taken" class="col-sm-2 control-label">
					Select date
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="text" name="taken" id="taken" placeholder="Taken" disabled/>
					<input type="text" name="taken2" id="taken2" hidden/>
				</div>
			</div>
			<div class="form-group hidden">
				<label for="lat" class="col-sm-2 control-label">
					Latitude
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="text" name="lat" id="lat" placeholder="Latitude" />
				</div>
			</div>
			<div class="form-group hidden">
				<label for="lon" class="col-sm-2 control-label">
					Longitute
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="text" name="lon" id="lon" placeholder="Longitute" />
				</div>
			</div>
			<div class="row" style="margin-bottom: 15px;">
				<div class="col-sm-6">
					<div id="image_preview"><img id="previewing" src="images/noimage.png" /></div>
				</div>
				<div class="col-sm-6">
					<div id="previewmap"></div>
				</div>
			</div>
			<div class="form-group">
				<label for="newtype" class="col-sm-2 control-label">
					Type
				</label>
				<div class="col-sm-10">
					<select class="form-control" id="newtype" name="newtype"></select>
				</div>
			</div>
			<div class="form-group">
				<label for="newgroup" class="col-sm-2 control-label">
					Group
				</label>
				<div class="col-sm-10">
					<select class="form-control" id="newgroup" name="newgroup"></select>
				</div>
			</div>
			<div class="form-group">
				<label for="name" class="col-sm-2 control-label">
					Name
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="text" name="name" id="name" />
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-sm-2 control-label">
					Email
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="text" name="email" id="email" />
				</div>
			</div>
			<div class="form-group">
				<label for="phone" class="col-sm-2 control-label">
					Phone
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="text" name="phone" id="phone" />
				</div>
			</div>
			<div class="form-group">
				<label for="comment" class="col-sm-2 control-label">
					Comment
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="text" name="comment" id="comment" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-6">
					<input type="submit" value="Upload" class="btn btn-default submit pull-right" /> 
				</div>
				<div class="col-sm-6">
					<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>		
		<h4 id='loading' ><img src="images/loading_circle.gif"/>&nbsp;&nbsp;Loading...</h4>
      </div>
    </div>
  </div>
</div>

	</body>
</html>
