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
	$downloadOK = false;

	if ($_SERVER['REQUEST_METHOD'] === 'GET'){
		if ($_GET[action] == null) {
			$html = "
				<html>
					<head>
						<title>
							Potatoes Backup Restore
						</title>
					</head>
					<body>
						<h2> Data Backup </h2>
						<h4> Administrator Login </h4>
						<form action='db-backup.php' method='post'>
							Username: <input type='text' name='username'> <br/>
							Password: <input type='password' name='password'> <br/>
							<input type='submit' value='Login'>
						</form>
					</body>
				</html>
			";
		}

		elseif ($_GET[action] == 'download' && $_GET[username] == $login_user && $_GET[password] == $login_pass){
			export_data($conn);
		}

		echo $html;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST'){

		if ($_POST[username] == $login_user && $_POST[password] == $login_pass){
			$html = "
				<html>
					<head>
						<title>
							Potatoes Backup Restore
						</title>
						<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
					</head>
					<body>
						<div id='current-user' style='display:none'>
							<span id='username'> $_POST[username] </span>
							<span id='password'> $_POST[password] </span>
						</div>
						<div id='actions'> 
							<button id='download'> Download </button>
							<button id='restore'> Restore </button>
						</div>
						<script>
							$(document).ready(()=> {
								var user = $.trim($('#username').text());
								var pass = $.trim($('#password').text());
								$('#download').on('click', () => {
									$.ajax({
										method: 'GET',
										url: 'db-backup.php',
										data: {'username': user, 'password': pass, 'action': 'download'},
									});

									alert('>' + user + '<>' + pass + '<');
								});
							});
						</script>
					</body>
				</html>
			";

			echo $html;
		}
		else {
			echo "Incorrect";
		}
	}

	//export_data($conn);

	function export_data($conn){
		
		if ($conn->connect_error) {
			header('HTTP/1.1 503 Cannot Connect to Database Server');
		}

		else {

				//setup filename
			$filename = 'data_backup_' . date("Y-m-d_h-m-s") . '.potatoe';

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