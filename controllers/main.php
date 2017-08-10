<?php
require("template.php");
require("models/orbit.php");
require("models/categories.php");
require("models/games.php");

$dbconfig = parse_ini_file("configs/database.ini");
$mysqli = mysqli_connect($dbconfig["host"], $dbconfig["user"], $dbconfig["password"], $dbconfig["database"]); {
	$orbit = new Orbit($mysqli, $dbconfig["table"]);
	$orbit = $orbit->load();

	$categories = new Categories($mysqli, $dbconfig["table"]);
	$games = new Games($mysqli, $dbconfig["table"], $categories);
	$games = $games->load();
} $mysqli->close();

$template = new Template();
$template->setVariable("orbit", $orbit);
$template->setVariable("games", $games);
$template->loadView("main");
?>
