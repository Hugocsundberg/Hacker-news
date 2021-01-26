<?php

declare(strict_types=1);
require('../functions.php');

$user_id = intval($_SESSION['user']['id']);
$db = new PDO('sqlite:../hacker_news_database.sqlite3');

$query = 'DELETE FROM Users WHERE id = :id';
$statement = $db->prepare($query);
$statement->bindParam(':id', $user_id, PDO::PARAM_INT);
$statement->execute();

$query = 'DELETE FROM Comments WHERE user_id = :id';
$statement = $db->prepare($query);
$statement->bindParam(':id', $user_id, PDO::PARAM_INT);
$statement->execute();

$query = 'DELETE FROM Likes WHERE user_id = :id';
$statement = $db->prepare($query);
$statement->bindParam(':id', $user_id, PDO::PARAM_INT);
$statement->execute();

$query = 'DELETE FROM Posts WHERE user_id = :id';
$statement = $db->prepare($query);
$statement->bindParam(':id', $user_id, PDO::PARAM_INT);
$statement->execute();

$query = 'DELETE FROM comment_likes WHERE user_id = :id';
$statement = $db->prepare($query);
$statement->bindParam(':id', $user_id, PDO::PARAM_INT);
$statement->execute();

session_destroy();
redirect('/views/index.php');
