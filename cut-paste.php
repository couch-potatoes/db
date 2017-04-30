
        //handle POST request for everything
		if (array_keys($_POST)[0] == "ineed"){
			if ($_POST["ineed"] == "all"){
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				}
				else {
					$sql = "SELECT * FROM StatusEntries";
					$query_result = $conn->query($sql);

					if ($query_result->num_rows > 0){
						$entry = array();
						while($col = $query_result->fetch_assoc()) {
							$entry[$col["participantId"]]["date"] = $col["date"];
							$entry[$col["participantId"]]["id"] = $col["participantId"];
							$entry[$col["participantId"]]["calories"] = $col["calories"];
							$entry[$col["participantId"]]["carbs"] = $col["carbs"];
							$entry[$col["participantId"]]["proteins"] = $col["proteins"];
							$entry[$col["participantId"]]["fats"] = $col["fats"];
							$entry[$col["participantId"]]["exerciseLength"] = $col["exerciseLength"];
							$entry[$col["participantId"]]["eatBefore"] = $col["eatBefore"];
							$entry[$col["participantId"]]["eatAfter"] = $col["eatAfter"];
							$entry[$col["participantId"]]["sleepQuality"] = $col["sleepQuality"];
							$entry[$col["participantId"]]["stressLevel"] = $col["stressLevel"];
							$entry[$col["participantId"]]["energyLevel"] = $col["energyLevel"];
							$entry[$col["participantId"]]["numMeals"] = $col["numMeals"];
							$entry[$col["participantId"]]["breakfast"] = $col["breakfast"];
							$entry[$col["participantId"]]["sleepLength"] = $col["sleepLength"];
						}
						echo json_encode($entry);
					}
					else {
						header('HTTP/1.1 500 Internal Server');
						header('Content-Type: application/json; charset=UTF-8');
					}
				}
			}
		}