<!DOCTYPE html>
<html>
<head>
	<title>Pocket Realms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="/realms/pages/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="/realms/pages/assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" media="screen">
    <link href="/realms/pages/assets/bootstrap/css/metro-bootstrap.css" rel="stylesheet" medial="screen">
    <style>
    	body {
	    	background-color: #353535;
    	}
    	
    	form {
	    	background-color: #fff;
	    	padding-top: 20px;
    	}
    	
    	#logo {
	    	display: block;
	    	margin-left: auto;
	    	margin-right: auto;
    	}
    	
    </style>
</head>
<body>

	<div class="row-fluid">&nbsp;</div>
	<div class="row-fluid">
		<div class="span5 offset4">
			<form class="form-horizontal">
				<fieldset>
				<div class="control-group">
					<img id="logo" src="/realms/pages/img/pocketrealms.png" alt="Pocket Realms">
				</div>
				<hr>
				<div class="control-group">
					<label class="control-label" for="inputEmail">Email</label>
					<div class="controls">
						<input type="hidden" id="authenticityToken" name="authenticityToken">
						<input type="email" id="email" name="email" placeholder="Email">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputPassword">Password</label>
					<div class="controls">
						<input type="password" id="password" name="password" placeholder="Password">
					</div>
				</div>
				<div class="form-actions">
					<button type="submit" class="btn btn-primary pull-right">Sign in</button>
					<a href="/realms/m/launch" class="btn btn-inverse pull-right">Cancel</a>
					<!--<a href="/realms/m/register" class="btn pull-right">Register</a>-->
				</div>
				</fieldset>
			</form>
		</div>
	</div>
		
	<script src="http://code.jquery.com/jquery.js"></script>
	<script src="/realms/pages/assets/bootstrap/js/bootstrap.min.js"></script>
	
</body>
</html>
