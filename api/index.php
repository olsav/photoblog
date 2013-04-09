<?php

require 'config.php';
require 'Slim/Slim.php';

$app = new Slim();

$app->get('/photos', 'getPhotos');
$app->get('/photos/:id', 'getPhoto');
$app->get('/photos/search/:query', 'findByName');
$app->post('/photos', 'addPhoto');
$app->put('/photos/:id', 'updatePhoto');
$app->delete('/photos/:id',	'deletePhoto');

$app->run();


function getConnection() {
	$dbhost = DEFAULT_DB_HOST;
	$dbuser = DEFAULT_DB_USER;
	$dbpass = DEFAULT_DB_PASS;
	$dbname = DEFAULT_DB_NAME;
	$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbh;
}

function getPhotos() {
	$sql = "select * FROM `". DEFAULT_DB_TABLE ."` ORDER BY id DESC";
	try {
		$db = getConnection();
		$stmt = $db->query($sql);  
		$photos = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		// echo '{"photo": ' . json_encode($photos) . '}';
		echo json_encode($photos);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getPhoto($id) {
	$sql = "SELECT * FROM `". DEFAULT_DB_TABLE ."` WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$photo = $stmt->fetchObject();  
		$db = null;
		echo json_encode($photo); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function addPhoto() {
	//error_log('addPhoto\n', 3, '/tmp/php.log');
	$request = Slim::getInstance()->request();
	$photo = json_decode($request->getBody());
	$sql = "INSERT INTO `". DEFAULT_DB_TABLE ."` (name, teaser, body, picture, thumb, created) VALUES (:name, :teaser, :body, :picture, :thumb, NOW())";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $photo->name);
		$stmt->bindParam("teaser", $photo->teaser);
		$stmt->bindParam("body", $photo->body);
		$stmt->bindParam("picture", $photo->picture);
		$stmt->bindParam("thumb", $photo->thumb);
		$stmt->execute();
		$photo->id = $db->lastInsertId();
		$db = null;
		echo json_encode($photo); 
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function updatePhoto($id) {
	$request = Slim::getInstance()->request();
	$photo = json_decode($request->getBody());
	$f = fopen('/tmp/upload.log', 'a');
	fputs($f, print_r($photo, true));
	fclose($f);
	$sql = "UPDATE `". DEFAULT_DB_TABLE ."` SET name=:name, teaser=:teaser, body=:body, picture=:picture, thumb=:thumb WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("name", $photo->name);
		$stmt->bindParam("teaser", $photo->teaser);
		$stmt->bindParam("body", $photo->body);
		$stmt->bindParam("picture", $photo->picture);
		$stmt->bindParam("thumb", $photo->thumb);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($photo); 
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deletePhoto($id) {
	$sql = "DELETE FROM `". DEFAULT_DB_TABLE ."` WHERE id=:id";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function findByName($query) {
	$sql = "SELECT * FROM `". DEFAULT_DB_TABLE ."` WHERE UPPER(name) LIKE :query ORDER BY name";
	try {
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";  
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$photos = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($photos);
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

?>