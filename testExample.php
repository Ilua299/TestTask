<?php
	require_once 'phpQuery/phpQuery/phpQuery.php';
	ini_set('max_execution_time', 100000);
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	setlocale(LC_ALL, 'ru_RU');
	date_default_timezone_set('Europe/Moscow');
	header('Content-type: text/html; charset=utf-8');

	$mysqli = new mysqli('127.0.0.1', 'root', '', 'test', NULL);

	$images_src = array(); // Ссылка на картинку
	$college_name = array();// Название Колледжа
	$city = array(); // Город
	$state = array(); // Штат
	$address = array(); // Адрес
	$phone = array();// Телефон
	$site = array(); // Сайт
	for($i = 1; $i <= 100; $i++) {
		//test 1
		$doc = phpQuery::newDocument(file_get_contents('https://www.princetonreview.com/college-search?ceid=cp-1022984&page='.$i));
		$school_locations = $doc->find('.location');
		if(!$school_locations) continue;
		$locations = array();
		foreach ($school_locations as $row) {
			array_push($locations,pq($row)->text());
		}

		foreach ($locations as $row) {
			list($city[],$state[]) = explode(',',$row);
		}
		$img_alt = array();
		$school_images = $doc->find('.school-image,.school-image-large');
		$collegies_names = $doc->find('h2 a');
		foreach ($collegies_names as $row) {
			array_push($college_name, pq($row)->text());
			foreach ($school_images as $img) {
				if ((pq($img)->attr('alt')) == pq($row)->text()){
					array_push($img_alt, pq($img)->attr('alt'));
					array_push($images_src, pq($img)->attr('src'));
				}
			}
			if(end($college_name) != end($img_alt)){
				array_push($images_src, 'Without img');
				array_push($img_alt, end($college_name));
			}
		}


		//test 2
		foreach ($collegies_names as $val) {
			$link = pq($val)->attr('href');
			$newDoc =  phpQuery::newDocument(file_get_contents("https://www.princetonreview.com" . $link));
			if($college_site = $newDoc->find('.school-headline-address a')->attr('href')){
				array_push($site, $college_site);	
			}
			else{
				array_push($site, "Without site");	
			}
			$addr =  $newDoc->find('.row .col-xs-6.bold');
			$temp_address = false;
			$temp_phone = false;
			foreach ($addr as $value){
				$text = pq($value)->text();
				$text = preg_replace('/\s+/', '', $text); 

				if(strcasecmp($text,"Address") == 0){
					array_push($address,pq($value)->next()->text());
					$temp_address = true;
				}
				if(strcasecmp($text,"Phone") == 0){
					array_push($phone,pq($value)->next()->text());
					$temp_phone = true;
				}
			}
			if(!$temp_address)
				array_push($address,"Without address");	
			if(!$temp_phone)
				array_push($phone,"Without phone");
		}
	}


	$count = count($college_name);
	for ($i = 0;$i < $count;$i++){
		$result = mysqli_query($mysqli,"SELECT * FROM test_1 WHERE college_name='".$mysqli->real_escape_string($college_name[$i])."'"); 
		$num_rows = mysqli_num_rows($result); 
		if($num_rows == 0){
			$sql = "INSERT INTO test_1 (image_src,college_name,city,state) VALUES ('".$mysqli->real_escape_string($images_src[$i])."','".$mysqli->real_escape_string($college_name[$i])."','".$mysqli->real_escape_string($city[$i])."','".$mysqli->real_escape_string($state[$i])."')";
			if (mysqli_query($mysqli,$sql) === TRUE) {
		    	echo "New record created successfully";
			} else {
			    echo "Error: " . $sql . "<br>" . $mysqli->error;
			}
		}
	}
	for ($i = 0;$i < $count;$i++){
		$result = mysqli_query($mysqli,"SELECT * FROM test_2 WHERE college_name='".$mysqli->real_escape_string($college_name[$i])."'"); 
		$num_rows = mysqli_num_rows($result); 
		if($num_rows == 0){
			$sql = "INSERT INTO test_2 (college_name,address,phone,site) VALUES ('".$mysqli->real_escape_string($college_name[$i])."','".$mysqli->real_escape_string($address[$i])."','".$mysqli->real_escape_string($phone[$i])."','".$mysqli->real_escape_string($site[$i])."')";
			if (mysqli_query($mysqli,$sql) === TRUE) {
		    	echo "New record created successfully";
			} else {
			    echo "Error: " . $sql . "<br>" . $mysqli->error;
			}
		}
	}

	$result = mysqli_query($mysqli,"SELECT college_name FROM test_1");
	$array_data = $result->fetch_all(MYSQLI_NUM);
	$array_data = array_map('current', $array_data);
	$deleted_colleges = array_diff($array_data,$college_name);
	foreach ($deleted_colleges as $row) {
		$sql = "DELETE FROM test_1 WHERE college_name - '".$mysqli->real_escape_string($row)."'";
		mysqli_query($mysqli,$sql);
		$sql = "DELETE FROM test_2 WHERE college_name - '".$mysqli->real_escape_string($row)."'";
		mysqli_query($mysqli,$sql);
	}

	$mysqli->close();


?>