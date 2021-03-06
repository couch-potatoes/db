<?php

	error_reporting(0);

 	header('Access-Control-Allow-Origin: *');

 		//use environment variables from Heroku
 	$servername = getenv(servername);
 	$username = getenv(username);
 	$password = getenv(password);
 	$database = getenv(database);

 		//use login.php for local
 	//include 'login.php';

 		//sql connection information
	$conn = new mysqli($servername, $username, $password, $database);

		//handle POST requests from clients
	if ($_SERVER['REQUEST_METHOD'] === 'POST'){

		$startDate = '';
		$endDate = '';
		$s = '';
		$sdate = '';
		$e = '';
		$edate = '';

			//extract values from URL

			//get start date
		if ($_POST["startDate"] != null){$s = explode(' ', $_POST["startDate"]);}
		elseif ($_POST["startdate"] != null){$s = explode(' ', $_POST["startdate"]);}
		elseif ($_POST["StartDate"] != null){$s = explode(' ', $_POST["StartDate"]);}
		elseif ($_POST["Startdate"] != null){$s = explode(' ', $_POST["Startdate"]);}
		for($i=0; $i<count($s) && $i<4; $i++) $sdate .= $s[$i].' ';
		$startDate = date_format(new DateTime($sdate), 'Y-m-d');

			//get end date
		if ($_POST["endDate"] != null){$e = explode(' ', $_POST["endDate"]);}
		elseif ($_POST["enddate"] != null){$e = explode(' ', $_POST["enddate"]);}
		elseif ($_POST["EndDate"] != null){$e = explode(' ', $_POST["EndDate"]);}
		elseif ($_POST["Enddate"] != null){$e = explode(' ', $_POST["Enddate"]);}
		for($i=0; $i<count($e) && $i<4; $i++) $sdate .= $e[$i].' ';
		$endDate = date_format(new DateTime($edate), 'Y-m-d');
		
			//get others
		$gender = $_POST["gender"];
		$sports = $_POST["sports"];
		$type = $_POST["chart"];

        	//handle POST request for nutrition data
		if ($type == "nutrition"){

				//prepare a query
			$query = "SELECT cast(date as date) as date, avg(calories) as calories, avg(carbs) as carbs, avg(proteins) as proteins, avg(fats) as fats 
			FROM StatusEntries 
			WHERE cast(date as date) BETWEEN '$startDate' AND '$endDate' ";

				//add gender if user chooses it
			if ($gender != 'na'){
				$query .= "AND participantId IN ( SELECT participantId FROM participantprofile WHERE gender = '$gender' ";
			}

				//add sport if user chooses it
			if ($sports != 'na'){ 
				$sports_arr = explode(' ', $sports);
				for ($i=0; $i < count($sports_arr); $i++){
					if ($i==0) $query .= " AND ( sports LIKE '%$sports_arr[$i]%'";
					else $query .= " OR sports LIKE '%$sports_arr[$i]%' ";
				}
				$query .= ")";
			}

			if ($gender != 'na'){
				$query .= ") ";
			}

			$query .= "GROUP BY cast(date as date);";

				//connect to sql server
			if ($conn->connect_error) {
				header('HTTP/1.1 503 Cannot Connect to Database Server');
			}
			else {

					//get result back from sql server
				$query_result = $conn->query($query);

				if ($query_result != null){

						//prepare query result into array
					if ($query_result->num_rows > 0){
						$result = array();
						while($col = $query_result->fetch_assoc()){
							$result[$col["date"]]["date"] = $col["date"];
							$result[$col["date"]]["calories"] = $col["calories"];
							$result[$col["date"]]["carbs"] = $col["carbs"];
							$result[$col["date"]]["proteins"] = $col["proteins"];
							$result[$col["date"]]["fats"] = $col["fats"];
						}

								//reply result as json
						echo json_encode($result);
					}

							//reply error if server replies nothing
					else {
						header('HTTP/1.1 204 No Entry in Database Server Reply');
					}
				}

						//reply error if server replies nothing
				else {
					header('HTTP/1.1 204 Empty Response from Database Server');
				}
			}
		}

          	//handle POST request for sleep and energy data
	    elseif ($type == "sleep-energy"){

	    		//prepare a query
	    	$query = "SELECT cast(date as date) as date, avg(sleepQuality) as sleepQuality, avg(sleepLength) as sleepLength, avg(stressLevel) as stressLevel, avg(energyLevel) as energyLevel 
	        	FROM StatusEntries
	        	WHERE cast(date as date) BETWEEN '$startDate' AND '$endDate' ";

	        	//add gender if user chooses
			if ($gender != 'na'){
				$query .= "AND participantId IN ( SELECT participantId FROM participantprofile WHERE gender = '$gender' ";
			}

				//add sport if user chooses
			if ($sports != 'na'){ 
				$sports_arr = explode(' ', $sports);
				for ($i=0; $i < count($sports_arr); $i++){
					if ($i==0) $query .= " AND ( sports LIKE '%$sports_arr[$i]%'";
					else $query .= " OR sports LIKE '%$sports_arr[$i]%' ";
				}
				$query .= ")";
			}

			if ($gender != 'na'){
				$query .= ") ";
			}

			$query .= "GROUP BY cast(date as date);";

				//connect to mysql server
	      	if ($conn->connect_error) {
				header('HTTP/1.1 503 Cannot Connect to Database Server');
	      	}

	      		//receive sql reply
	      	else {
		        $query_result = $conn->query($query);

		        if ($query_result != null){
			        if ($query_result->num_rows > 0){
			          	$result = array();
			          	while($col = $query_result->fetch_assoc()){
				            $result[$col["date"]]["date"] = $col["date"];
				            $result[$col["date"]]["sleepQuality"] = $col["sleepQuality"];
				            $result[$col["date"]]["sleepLength"] = $col["sleepLength"];
				            $result[$col["date"]]["stressLevel"] = $col["stressLevel"];
				            $result[$col["date"]]["energyLevel"] = $col["energyLevel"];
			        	}

			        		//reply to front end
			          	echo json_encode($result);
		        	}

			        	//error if server replys 0 rows
			        else {
						header('HTTP/1.1 204 No Entry in Database Server Reply');
			        }
			    }
		        else {
					header('HTTP/1.1 204 Empty Response from Database Server');
				}
	      	}
	    }
	}    //handle POST requests ends

		//handle GET requests
	elseif ($_SERVER['REQUEST_METHOD'] === 'GET'){
		error_reporting(E_ERROR);

		$startDate = '';
		$endDate = '';
		$s = '';
		$sdate = '';
		$e = '';
		$edate = '';

			//extract values from URL

			//get start date
		if ($_GET["startDate"] != null){$s = explode(' ', $_GET["startDate"]);}
		elseif ($_GET["startdate"] != null){$s = explode(' ', $_GET["startdate"]);}
		elseif ($_GET["StartDate"] != null){$s = explode(' ', $_GET["StartDate"]);}
		elseif ($_GET["Startdate"] != null){$s = explode(' ', $_GET["Startdate"]);}
		for($i=0; $i<count($s) && $i<4; $i++) $sdate .= $s[$i].' ';
		$startDate = date_format(new DateTime($sdate), 'Y-m-d');

			//get end date
		if ($_GET["endDate"] != null){$e = explode(' ', $_GET["endDate"]);}
		elseif ($_GET["enddate"] != null){$e = explode(' ', $_GET["enddate"]);}
		elseif ($_GET["EndDate"] != null){$e = explode(' ', $_GET["EndDate"]);}
		elseif ($_GET["Enddate"] != null){$e = explode(' ', $_GET["Enddate"]);}
		for($i=0; $i<count($e) && $i<4; $i++) $sdate .= $e[$i].' ';
		$endDate = date_format(new DateTime($edate), 'Y-m-d');
		
			//get others
		$gender = $_GET["gender"];
		$sports = $_GET["sports"];
		$type = $_GET["chart"];

			//handle request for nutrition
		if ($_GET["chart"] == "nutrition"){

				//prepare sql
			$query = "SELECT cast(date as date) as date, avg(calories) as calories, avg(carbs) as carbs, avg(proteins) as proteins, avg(fats) as fats 
			FROM StatusEntries 
			WHERE cast(date as date) BETWEEN '$startDate' AND '$endDate' ";

				//add gender if user requested
			if ($gender != 'na'){
				$query .= "AND participantId IN ( SELECT participantId FROM participantprofile WHERE gender = '$gender' ";
			}

				//add sport if user requested
			if ($sports != 'na'){ 
				$sports_arr = explode(' ', $sports);
				for ($i=0; $i < count($sports_arr); $i++){
					if ($i==0) $query .= " AND ( sports LIKE '%$sports_arr[$i]%'";
					else $query .= " OR sports LIKE '%$sports_arr[$i]%' ";
				}
				$query .= ")";
			}

			if ($gender != 'na'){
				$query .= ") ";
			}

			$query .= "GROUP BY cast(date as date);";

				//connect the sql server
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			else {
					//receive reply from sql server
				$query_result = $conn->query($query);

				if ($query_result->num_rows > 0){
					$result = array();
					$result["header"] = ['Date', 'Calories', 'Carbs', 'Proteins', 'Fats'];

					while($col = $query_result->fetch_assoc()){
						$result[$col["date"]]["date"] = $col["date"];
						$result[$col["date"]]["calories"] = $col["calories"];
						$result[$col["date"]]["carbs"] = $col["carbs"];
						$result[$col["date"]]["proteins"] = $col["proteins"];
						$result[$col["date"]]["fats"] = $col["fats"];
					}

						//generate csv if user requested
					if($_GET["type"] == 'csv'){
						$filename = 'CSV-Export.csv';

							//create filename
						header('Content-Type: application/excel');
						header('Content-Disposition: attachment; filename="'.$filename.'"');

							//force download
						$fp = fopen('php://output', 'w');
						
						fputcsv($fp, ["From $startDate To $endDate | Gender: ". strtoupper($gender) ." | Sport(s): ". strtoupper($sports)]);
							
							//add data
						foreach($result as $row){
							fputcsv($fp, $row);
						}

							//close the file
						fclose($fp);
					}

						//generate table if user requested CSV
					elseif($_GET["type"] == 'html'){

							//prepare html header and table
						$html = "
						<html> 
							<head> 
								<title> Chart Export </title> 
								<style> 
									h1, h2, h3, h4, h5 {
										text-align: center;
										line-height: 3em;
										border: 1px solid black;
									}
									table {
										width: 100%;
										align: center;
										border: 1px solid black;
									}
									th, tr {
										line-height: 2em;
									}
									td {
										text-align: center;
										border-bottom: 1px solid #eee;
										border-left: 1px solid #eee;
									}
								</style>
							</head> 
							<body> 
								<h2> Data in Table View </h2>
								<h3>
									From $startDate To $endDate |
									Gender: ". strtoupper($gender) ." | Sport(s): ". strtoupper($sports) ."
								</h3> 
							<table>";

								//add data into table
						foreach($result as $row){
							$html .= "<tr>";
							foreach($row as $col){
								$html .= "<td>" . $col . "</td>";
							}
							$html .= "</tr>";
						}
						$html .= "</table></body></html>";

							//output the page
						echo $html;
					}
				}
						//error if there is no data
				else {
					echo "<h2 style='text-align: center; line-height: 3em; border: 1px solid black;'> No Data Available To Export </h2>";
				}
			}
		}

			//handle request for sleep and energy
	    elseif ($_GET["chart"] == "sleep-energy"){

	    		//prepare query
	        $query = "SELECT cast(date as date) as date, avg(sleepQuality) as sleepQuality, avg(sleepLength) as sleepLength, avg(stressLevel) as stressLevel, avg(energyLevel) as energyLevel 
	        	FROM StatusEntries
	        	WHERE cast(date as date) BETWEEN '$startDate' AND '$endDate' ";

	        	//add gender
			if ($gender != 'na'){
				$query .= "AND participantId IN ( SELECT participantId FROM participantprofile WHERE gender = '$gender' ";
			}

				//add sport(s)
			if ($sports != 'na'){ 
				$sports_arr = explode(' ', $sports);
				for ($i=0; $i < count($sports_arr); $i++){
					if ($i==0) $query .= " AND ( sports LIKE '%$sports_arr[$i]%'";
					else $query .= " OR sports LIKE '%$sports_arr[$i]%' ";
				}
				$query .= ")";
			}

			if ($gender != 'na'){
				$query .= ") ";
			}

			$query .= "GROUP BY cast(date as date);";

				//connect to the sql server
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			else {

					//receive result from sql server
	        	$query_result = $conn->query($query);

	        		//show query results
				if ($query_result->num_rows > 0){
					$result = array();

						//put header
					$result["header"] = ['Date', 'Sleep Quality', 'Sleep Length', 'Stress Level', 'Energy Level'];
		          	while($col = $query_result->fetch_assoc()){
			            $result[$col["date"]]["date"] = $col["date"];
			            $result[$col["date"]]["sleepQuality"] = $col["sleepQuality"];
			            $result[$col["date"]]["sleepLength"] = $col["sleepLength"];
			            $result[$col["date"]]["stressLevel"] = $col["stressLevel"];
			            $result[$col["date"]]["energyLevel"] = $col["energyLevel"];
		          	}

		          		//generate CSV file
					if($_GET["type"] == 'csv'){
						$filename = 'CSV-Export.csv';

						header('Content-Type: application/excel');
						header('Content-Disposition: attachment; filename="'.$filename.'"');

							//force download
						$fp = fopen('php://output', 'w');

						fputcsv($fp, ["From $startDate To $endDate | Gender: ". strtoupper($gender) ." | Sport(s): ". strtoupper($sports)]);

							//put data in CSV file
						foreach($result as $row){
							fputcsv($fp, $row);
						}
							//finish the file
						fclose($fp);
					}

						//prepare for html table view
					elseif($_GET["type"] == 'html'){

							//prepare html header and table
						$html = "
						<html> 
							<head> 
								<title> Chart Export </title> 
								<style> 
									h1, h2, h3, h4, h5 {
										text-align: center;
										line-height: 3em;
										border: 1px solid black;
									}
									table {
										width: 100%;
										align: center;
										border: 1px solid black;
									}
									th, tr {
										line-height: 2em;
									}
									td {
										text-align: center;
										border-bottom: 1px solid #eee;
										border-right: 1px solid #eee;
									}
								</style>
							</head> 
							<body> 
								<h2> Data in Table View </h2>
								<h3>
									From $startDate To $endDate |
									Gender: ". strtoupper($gender) ." | Sport(s): ". strtoupper($sports) ."
								</h3> 
							<table>";
						
							//put data in table
						foreach($result as $row){
							$html .= "<tr>";
							foreach($row as $col){
								$html .= "<td>" . $col . "</td>";
							}
							$html .= "</tr>";
						}
						$html .= "</table></body></html>";

							//echo out the html
						echo $html;
					}
				}

					//show error if there is no data
				else {
					"<h2 style='text-align: center; line-height: 3em; border: 1px solid black;'> No Data Available To Export </h2>";
				}
			}
		}

			//show message if nothing is requested
		else {
			echo "<h2 style='text-align: center; line-height: 3em; border: 1px solid black;'> We are Couch Potatoes </h2>";
		}
	}
	$conn->close();
?>
