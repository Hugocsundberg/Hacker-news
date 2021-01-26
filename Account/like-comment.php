<?php

declare(strict_types=1);
require('../functions.php');

$db = new PDO('sqlite:../hacker_news_database.sqlite3');

header('Content-Type: application/json');

if (isset($_POST['like-comment'])) {
    $commentid = filter_var($_POST['like-comment'], FILTER_SANITIZE_NUMBER_INT);
    $userid = $_SESSION['user']['id'];

    $query = 'SELECT * FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id';
    $statement = $db->prepare($query);

    if (!$statement) {
        die(var_dump($db->errorinfo()));
    }

    $statement->bindParam(':comment_id', $commentid, PDO::PARAM_INT);
    $statement->bindParam(':user_id', $userid, PDO::PARAM_INT);
    $statement->execute();

    $upvote = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$upvote) {
        likeComment($db, $commentid, $userid);
        $voteCount = getUpvotes($db, $commentid);
        $status = "upvote";
        $response = [
            'voteCount' => $voteCount,
            'status' => $status
        ];

        echo json_encode($response);
    } else {
        unlikeComment($db, $commentid, $userid);
        $voteCount = getUpvotes($db, $commentid);
        $status = "unvote";
        $response = [
            'voteCount' => $voteCount,
            'status' => $status
        ];
        echo json_encode($response);
    }
}
