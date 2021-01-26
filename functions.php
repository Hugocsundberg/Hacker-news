<?php

declare(strict_types=1);
session_start();
if (isset($_SESSION['message'])) {
} else {
    $_SESSION['message'] = [];
}

function createMessage(int $type, string $message = 'ok')
{
    $messageArray = ['type' => $type, 'message' => $message];
    array_push($_SESSION['message'], $messageArray);
}

function logMessage()
{
    $errorArray = [];
    if (count($_SESSION['message']) > 0) { //Only try logging if array is not empty
        foreach (($_SESSION['message']) as $message) {
            if ($message['type'] !== 3) {
                $errorArray[] = $message['message'];
            } else {
                $errorArray = [];
            }
        }
        if (count($errorArray) > 0) {
            print_r($errorArray);
        }
    }
}

function redirect(string $path)
{
    header("Location: ${path}");
    exit;
}

// function errorLog(string $errorMessage)
// {
//     $errors = file_get_contents(__DIR__ . '/errors.json');
//     $decodedJSON = json_decode($errors);
//     $errorObject = new stdClass;
//     $errorObject->error = $errorMessage;
//     $decodedJSON[] = $errorObject;
//     $encodedJSON = json_encode($decodedJSON);
//     file_put_contents('../errors.json', $encodedJSON);
// }

// function errorPrint()
// {
//     $errors = file_get_contents(__DIR__ . '/errors.json');
//     $decodedJSON = json_decode($errors);
//     foreach ($decodedJSON as $error) {
//         echo $error->error;
//     }
// }

function existUpvote($db, $commentid, $userid)
{
    $query = 'SELECT * FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id';
    $statement = $db->prepare($query);

    if (!$statement) {
        die(var_dump($db->errorinfo()));
    }

    $statement->bindParam(':comment_id', $commentid, PDO::PARAM_INT);
    $statement->bindParam(':user_id', $userid, PDO::PARAM_INT);
    $statement->execute();

    $upvote = $statement->fetch(PDO::FETCH_ASSOC);

    if ($upvote) {
        return true;
    } else {
        return false;
    }
}

function getUpvotes($db, $id): int
{
    $query = 'SELECT COUNT(*) FROM comment_likes WHERE comment_id = :comment_id';
    $statement = $db->prepare($query);

    $statement->bindParam(':comment_id', $id, PDO::PARAM_INT);
    $statement->execute();

    $upvotes = $statement->fetch(PDO::FETCH_ASSOC);
    return (int) $upvotes["COUNT(*)"];
}

function likeComment($db, $commentid, $userid)
{
    $query = 'INSERT INTO comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)';
    $statement = $db->prepare($query);

    $statement->bindParam(':comment_id', $commentid, PDO::PARAM_INT);
    $statement->bindParam(':user_id', $userid, PDO::PARAM_INT);
    $statement->execute();
}

function unlikeComment($db, $commentid, $userid)
{
    $query = 'DELETE FROM comment_likes WHERE user_id = :user_id AND comment_id = :comment_id';
    $statement = $db->prepare($query);

    $statement->bindParam(':comment_id', $commentid, PDO::PARAM_INT);
    $statement->bindParam(':user_id', $userid, PDO::PARAM_INT);
    $statement->execute();
}
