<?php

	error_reporting(0);

 	header('Access-Control-Allow-Origin: *');

 		//use environment variables from heroku
 	$servername = getenv(servername);
 	$username = getenv(username);
 	$password = getenv(password);
 	$database = getenv(database);

 	$login_user = getenv(login_user);
 	$login_pass = getenv(login_pass);

 		//use login.php for local development
 	include 'login.php';

 		//sql connection information
	$conn = new mysqli($servername, $username, $password, $database);

		//if broswer request with GET method (normal web page view method)
	if ($_SERVER['REQUEST_METHOD'] === 'GET'){
		$html = "
			<html>
				<head>
					<title>
						Potatoes Backup Restore
					</title>
					<style>
						h1, h2, h3, h4, h5 {
							padding: 1em;
							text-align: center;
						}
						#loginForm {
							display: block;
							padding: 5em;
							border: 1px solid black;
							border-radius: 5px;
							max-width: 20em;
							margin: 0 auto;
						}
						#loginForm .row {
							display: block;
							padding: 1em;
							top: .5em;
						}
						#loginForm .button input {
							background-color: white;
							margin: 0 auto;
						}
						#loginForm .button input:hover {
							background-color: black;
							color: white;
							cursor: pointer;
						}
						#loginForm .row input {
							border: 1px solid black;
							border-radius: 2px;
							padding: 5px;
							font-size: 1em;
							float: right;
						}
					</style>
				</head>
				<body>
					<h2> Potatoes Data Backup </h2>
					<h3> Administrator Login </h3>
					<form action='db-backup.php' method='post' id='loginForm'>
						<input type='hidden' name='action' value='login'>
						<span class='row'> Username: <input type='text' name='username'> </span>
						<span class='row'> Password: <input type='password' name='password'> </span>
						<span class='row button'> <input type='submit' value='Login'> </span>
					</form>
				</body>
			</html>
		";
		echo $html;
	}
		//if front end sends request with POST method
	if ($_SERVER['REQUEST_METHOD'] === 'POST'){

			//valid username and password
		if ($_POST[username] == $login_user && $_POST[password] == $login_pass){

				//request to show login page
			if ($_POST[action] == 'login') {
				$html = "
					<html>
						<head>
							<title>
								Potatoes Backup-Restore
							</title>
							<style>
								h1, h2, h3, h4, h5 {
									padding: 1em;
									text-align: center;
								}
								#actions {
									display: block;
									padding: 5em;
									border: 1px solid black;
									border-radius: 5px;
									max-width: 20em;
									margin: 0 auto;
								}
								#actions .button {
									display: block;
									width: 15em;
									text-align: center;
									font-size: 1em;
									padding: 10px;
									margin: 0 auto;
									background-color: white;
									border: 1px solid black;
									border-radius: 5px;
									color: black;
								}
								#actions .button:hover{
									background-color: black;
									color: white;
									cursor: pointer;
								}
							</style>
						</head>
						<body>

							<h2> Potatoes Data Backup </h2>
							<h3> You are logged in as: $_POST[username] <br/><br/>
							<a href='db-backup.php'> <button> Log Out </button> </a>
							</h3>
							<div id='actions'> 
								<div id='buttons'>
									<form action='db-backup.php' method='post'>
										<input type='hidden' name='username' value='$_POST[username]'>
										<input type='hidden' name='password' value='$_POST[password]'>
										<input type='hidden' name='action' value='download'>
										<input type='submit' value='Download Data Backup' class='button'>
									</form>
									<form action='db-backup.php' method='post'>
										<input type='hidden' name='username' value='$_POST[username]'>
										<input type='hidden' name='password' value='$_POST[password]'>
										<input type='hidden' name='action' value='restore'>
										<input type='submit' value='Restore Data Backup' class='button'>
									</form>
								</div>
							</div>
						</body>
					</html>
				";

				echo $html;
			}
				//request to Download data file
			elseif ($_POST[action] == 'download'){
				export_data($conn);
			}

				//if action is to show Restore page
			elseif ($_POST[action] == 'restore'){
				$html = "
					<html>
						<head>
							<title>
								Potatoes Backup-Restore
							</title>
							<style>
								h1, h2, h3, h4, h5 {
									padding: 1em;
									text-align: center;
								}
								#restoreForm {
									display: block;
									padding: 5em;
									padding-top: 2em !important;
									border: 1px solid black;
									border-radius: 5px;
									max-width: 20em;
									margin: 0 auto;
								}
								#restoreForm .row, 
								#back .row
								{
									display: flex;
									padding: 1em;
									top: .5em;
								}
								#restoreForm .button input, 
								#back .button input
								{
									background-color: white;
									margin: 0 auto;
								}
								#restoreForm .button input:hover,
								#back .button input:hover {
									background-color: black;
									color: white;
									cursor: pointer;
								}
								#restoreForm .row input,
								#back .row input
								{
									max-width: 18em;
									border: 1px solid black;
									border-radius: 2px;
									padding: 5px;
									font-size: 1em;
									float: right;
								}
							</style>
						</head>
						<body>
							<h2> Potatoes Data Backup </h2>
							<h3> Restore Backup </h3>
							<form action='db-backup.php' method='post' id='back'>
								<input type='hidden' name='action' value='login'>
								<input type='hidden' name='username' value='$_POST[username]'>
								<input type='hidden' name='password' value='$_POST[password]'>
								<span class='row button'> <input type='submit' value='Back'> </span>
							</form>
							<form action='db-backup.php' method='post' id='restoreForm'>
								<h4> Upload a backup file to restore. <br/>(extention must be .sql or .potatoe) </h4>

								<input type='hidden' name='action' value='upload-file'>
								<input type='hidden' name='username' value='$_POST[username]'>
								<input type='hidden' name='password' value='$_POST[password]'>
								<span class='row'> <input type='file' name='backup_file' id='backup_file' accept='.sql, .potatoe, .jpg, .png'> </span>
								<span class='row button'> <input type='submit' name='submit' value='Upload'> </span>
							</form>
						</body>
					</html>
				";

				echo $html;
			}
				//if action is to restore the file
			elseif ($_POST[action] == 'upload-file'){

				$uploadInfo = "No File Uploaded!";

				$html = "
					<html>
						<head>
							<title>
								Potatoes Backup-Restore
							</title>
							<style>
								h1, h2, h3, h4, h5 {
									padding: 1em;
									text-align: center;
								}
								#restoreForm {
									display: block;
									padding: 5em;
									padding-top: 2em !important;
									border: 1px solid black;
									border-radius: 5px;
									max-width: 20em;
									margin: 0 auto;
								}
								#restoreForm .row, 
								#back .row
								{
									display: flex;
									padding: 1em;
									top: .5em;
								}
								#restoreForm .button input, 
								#back .button input
								{
									background-color: white;
									margin: 0 auto;
								}
								#restoreForm .button input:hover,
								#back .button input:hover {
									background-color: black;
									color: white;
									cursor: pointer;
								}
								#restoreForm .row input,
								#back .row input
								{
									max-width: 18em;
									border: 1px solid black;
									border-radius: 2px;
									padding: 5px;
									font-size: 1em;
									float: right;
								}
							</style>
						</head>
						<body>
							<h2> Potatoes Data Backup </h2>
							<h3> Restore Backup </h3>
							<form action='db-backup.php' method='post' id='back'>
								<input type='hidden' name='action' value='restore'>
								<input type='hidden' name='username' value='$_POST[username]'>
								<input type='hidden' name='password' value='$_POST[password]'>
								<span class='row button'> <input type='submit' value='Back'> </span>
							</form>
							<form action='db-backup.php' method='post' enctype='multipart/form-data' id='restoreForm'>
								<h4> $uploadInfo </h4>

								<input type='hidden' name='action' value='upload-file'>
								<input type='hidden' name='username' value='$_POST[username]'>
								<input type='hidden' name='password' value='$_POST[password]'>
								<span class='row button'> <input type='submit' value='Restore'> </span>
							</form>
						</body>
					</html>
				";

				echo $html;
			}
		}
			//if login with wrong username or password
		else {
			$html =	"
				<html>
					<head>
						<title>
							Potatoes Backup-Restore
						</title>
						<style>
							h1, h2, h3, h4, h5 {
								padding: 1em;
								text-align: center;
							}
							#actions {
								display: block;
								padding: 5em;
								border: 1px solid black;
								border-radius: 5px;
								max-width: 20em;
								margin: 0 auto;
							}
							#buttons {
								display: flex;
								margin: 0 auto;
							}
							#actions a {
								display: inline-block;
								width: 5em;
								text-align: center;
								padding: 10px;
								margin: 0 auto;
								text-decoration: none;
								border: 1px solid black;
								border-radius: 5px;
								color: black;
							}
							#actions a:hover{
								background-color: black;
								color: white;
							}
						</style>
					</head>
					<body>

						<h2> Potatoes Data Backup </h2>
						<div id='actions'> 
							<h3> Error! </br> Invalid Username or Password </h3>
							<div id='buttons'>
								<a href='db-backup.php'>Retry</a>
							</div>
						</div>
					</body>
				</html>
			";

			echo $html;
		}
	}

	//export_data($conn);

	function export_data($conn){
		
		if ($conn->connect_error) {
			header('HTTP/1.1 503 Cannot Connect to Database Server');
		}

		else {

				//setup filename
			$filename = 'data_backup_' . date("Y-m-d_h-m-s") . '.potato';

				//create file header
			header('Content-Type: text/x-sql');
			header('Content-Disposition: attachment; filename="'.$filename.'"');

				//force download
			$fp = fopen('php://output', 'w'); 
			$data = "";
				
				//request and get result back from sql server for Participant Table
			$query = "SELECT * FROM Participant";
			$query_result = $conn->query($query);

			if ($query_result != null){

					//add data to file
				$data .= "CREATE TABLE IF NOT EXISTS Participant (id int(11) NOT NULL, realm varchar(512) DEFAULT NULL, username varchar(512) DEFAULT NULL, password varchar(512) NOT NULL, email varchar(512) NOT NULL, emailVerified tinyint(1) DEFAULT NULL, verificationToken varchar(512) DEFAULT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n";

				while($col = $query_result->fetch_assoc()){
					$data .= "INSERT INTO Participant (id, realm, username, password, email, emailVerified, verificationToken) VALUES ";
					$data .= "('" . $col["id"] . "',";
					$data .= "'" . $col["realm"] . "',";
					$data .= "'" . $col["username"] . "',";
					$data .= "'" . $col["password"] . "',";
					$data .= "'" . $col["email"] . "',";
					$data .= "'" . $col["emailVerified"] . "',";
					$data .= "'" . $col["verificationToken"] . "');";
					$data .= "\n";
				}
			}

				//request and get result back from sql server for ParticipantProfile Table
			$query = "SELECT * FROM ParticipantProfile";
			$query_result = $conn->query($query);

			if ($query_result != null){

					//add data to file
				$data .= "CREATE TABLE IF NOT EXISTS ParticipantProfile (participantId int(11) NOT NULL, height int(11) NOT NULL, weight int(11) NOT NULL, age int(11) NOT NULL, sports text NOT NULL, gender varchar(512) NOT NULL, PRIMARY KEY (participantId)) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n";

				while($col = $query_result->fetch_assoc()){
					$data .= "INSERT INTO ParticipantProfile (participantId, height, weight, age, sports, gender) VALUES ";
					$data .= "('" . $col["participantId"] . "',";
					$data .= "'" . $col["height"] . "',";
					$data .= "'" . $col["weight"] . "',";
					$data .= "'" . $col["age"] . "',";
					$data .= "'" . $col["sports"] . "',";
					$data .= "'" . $col["gender"] . "');";
					$data .= "\n";
				}
			}

				//request and get result back from sql server for Researcher Table
			$query = "SELECT * FROM Researcher";
			$query_result = $conn->query($query);

			if ($query_result != null){

					//add data to file
				$data .= "CREATE TABLE IF NOT EXISTS Researcher (id int(11) NOT NULL, realm varchar(512) DEFAULT NULL, username varchar(512) DEFAULT NULL, password varchar(512) NOT NULL, email varchar(512) NOT NULL, emailVerified tinyint(1) DEFAULT NULL, verificationToken varchar(512) DEFAULT NULL, PRIMARY KEY(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n";

				while($col = $query_result->fetch_assoc()){
					$data .= "INSERT INTO Researcher (id, realm, username, password, email, emailVerified, verificationToken) VALUES ";
					$data .= "('" . $col["id"] . "',";
					$data .= "'" . $col["realm"] . "',";
					$data .= "'" . $col["username"] . "',";
					$data .= "'" . $col["password"] . "',";
					$data .= "'" . $col["email"] . "',";
					$data .= "'" . $col["emailVerified"] . "',";
					$data .= "'" . $col["verificationToken"] . "');";
					$data .= "\n";
				}
			}

				//request and get result back from sql server for StatusEntries Table
			$query = "SELECT * FROM StatusEntries";
			$query_result = $conn->query($query);

			if ($query_result != null){

					//add data to file
				$data .= "CREATE TABLE IF NOT EXISTS StatusEntries (date datetime NOT NULL, participantId int(11) NOT NULL, calories int(11) NOT NULL, carbs int(11) NOT NULL, proteins int(11) NOT NULL, fats int(11) NOT NULL, exerciseLength int(11) NOT NULL, eatBefore tinyint(1) NOT NULL, eatAfter tinyint(1) NOT NULL, sleepQuality int(11) NOT NULL, stressLevel int(11) NOT NULL, energyLevel int(11) NOT NULL, numMeals int(11) NOT NULL, breakfast tinyint(1) NOT NULL, sleepLength int(11) NOT NULL, PRIMARY KEY(date, participantId)) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n";

				while($col = $query_result->fetch_assoc()){
					$data .= "INSERT INTO StatusEntries (date, participantId, calories, carbs, proteins, fats, exerciseLength, eatBefore, eatAfter, sleepQuality, stressLevel, energyLevel, numMeals, breakfast, sleepLength) VALUES ";
					$data .= "('" . $col["date"] . "',";
					$data .= "'" . $col["participantId"] . "',";
					$data .= "'" . $col["calories"] . "',";
					$data .= "'" . $col["carbs"] . "',";
					$data .= "'" . $col["proteins"] . "',";
					$data .= "'" . $col["fats"] . "',";
					$data .= "'" . $col["exerciseLength"] . "',";
					$data .= "'" . $col["eatBefore"] . "',";
					$data .= "'" . $col["eatAfter"] . "',";
					$data .= "'" . $col["sleepQuality"] . "',";
					$data .= "'" . $col["stressLevel"] . "',";
					$data .= "'" . $col["energyLevel"] . "',";
					$data .= "'" . $col["numMeals"] . "',";
					$data .= "'" . $col["breakfast"] . "',";
					$data .= "'" . $col["sleepLength"] . "');";
					$data .= "\n";
				}
			}

			fwrite ($fp, $data);
			//echo $data;

				//close the file
			fclose($fp);
		}
	}
 ?>