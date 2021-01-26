<?php
require('header.php');
require('../functions.php');
logMessage();


//SELECT id, user_id, header, body, date, ifnull((select sum(up_down) from likes where posts.id=likes.post_id), 0) AS antallikes FROM Posts ORDER BY antallikes DESC


//Sort by likes or new
// if (isset($_SESSION['user'])) {
//     if ($_SESSION['user']['sort_by'] === 'new') {
//     }
// }

//Fetch posts from database
//Connect to db
$db = new PDO('sqlite:../hacker_news_database.sqlite3');


if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id'];
    $stmt = $db->prepare("SELECT sort_by FROM Users where id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $sort_by = $data['sort_by'];
} else {
    if (isset($_SESSION['sort'])) {
        $sort_by = $_SESSION['sort'];
    } else {
        $sort_by = 'new';
    }
}

$result;
if ($sort_by === 'new') {
    $result = $db->query("SELECT * FROM Posts ORDER BY \"date\" DESC LIMIT 30");
} else if ($sort_by === 'mostupvoted') {
    $result = $db->query("SELECT id, user_id, header, body, date, link, ifnull((select sum(up_down) from likes where posts.id=likes.post_id), 0) AS antallikes FROM Posts ORDER BY antallikes DESC LIMIT 30");
}

$posts = $result->fetchAll(PDO::FETCH_ASSOC);



?>

<body>
    <?php require('./nav.php') ?>

    <select class="sort-by" name="sort" id="">
        <option <?php if ($sort_by === 'new') {
                    echo 'selected';
                } ?>value="new">New 💎</option>
        <option <?php if ($sort_by === 'mostupvoted') {
                    echo 'selected';
                } ?> value="mostupvoted">Likes 💯</option>
    </select>

    <?php foreach ($posts as $post) : ?>
        <?php
        $postId = $post['id'];

        $upvoteImage = '/images/upvote.svg';
        $downvoteImage = '/images/downvote.svg';

        if (isset($_SESSION['user'])) {
            $hasLikedResult = $db->query("SELECT * FROM Likes WHERE post_id = $postId AND user_id = $userId AND up_down = 1");
            $hasLikedData = $hasLikedResult->fetch(PDO::FETCH_ASSOC);
            if (isset($hasLikedData['id'])) {
                $hasLiked = true;
                $upvoteImage = '/images/upvoteActive.svg';
            } else {
                $hasLiked = false;
                $upvoteImage = '/images/upvote.svg';
            }

            $hasDislikedResult = $db->query("SELECT id FROM Likes WHERE post_id = $postId AND user_id = $userId AND up_down = -1");
            $hasDislikedData = $hasDislikedResult->fetch(PDO::FETCH_ASSOC);
            if (isset($hasDislikedData['id'])) {
                $hasDisliked = true;
                $downvoteImage = '/images/downvoteActive.svg';
            } else {
                $hasDisliked = false;
                $downvoteImage = '/images/downvote.svg';
            }
        }



        //Fetch all comments on post
        $commentResult = $db->query("SELECT * FROM Comments WHERE post_id = $postId");
        $comments = $commentResult->fetchAll(PDO::FETCH_ASSOC);

        //Fetch likes on post
        $likesResult = $db->query("SELECT COUNT(user_id) AS 'likes' FROM Likes WHERE post_id = $postId AND up_down = 1");
        $likes = $likesResult->fetch(PDO::FETCH_ASSOC)['likes'];




        //Fetch dislikes
        $dislikeResult = $db->query("SELECT COUNT(user_id) AS 'dislikes' FROM Likes WHERE post_id = $postId AND up_down = -1");
        $dislikes = $dislikeResult->fetch(PDO::FETCH_ASSOC)['dislikes'];

        $LikesSum = $likes - $dislikes;

        //Fetch user from database
        $postUserId = $post['user_id'];
        $result = $db->query("SELECT * FROM Users WHERE id = $postUserId");
        $user = $result->fetch(PDO::FETCH_ASSOC);
        isset($user['avatar_path']) ? $avatarPath = $user['avatar_path'] : $avatarPath = '/Account/uploads/default.svg';

        //If user has a name, set it to $userName
        if (isset($user['name'])) {
            $userName = $user['name'];
        } else {
            $userName = 'IHaveNoName';
        }




        ?>






        <div data-postId="<?= $postId ?>" class="post id<?= $postId ?> post-group<?= $postId ?>">
            <div class="date-section">
                <div class="left">
                    <img src=<?= $avatarPath ?> alt="">
                    <p class="name"><?= $userName ?></p>
                </div>
                <div class="right">
                    <p class="date"><?= date('D M Y H:i', $post['date']) ?></p>
                </div>
            </div>
            <a href="<?= $post['link'] ?>">
                <div class="image-section">
                    <img src="/images/photo-1609050470947-f35aa6071497.jpeg" alt="">
                </div>
            </a>
            <div class="text-section">
                <div class="text-section-text">
                    <h2><?= $post['header'] ?></h2>
                    <p><?= $post['body'] ?></p>
                </div>

                <div class="text-section-vote" data-post="<?= $post['id'] ?>">
                    <div class="upvote-section img-container">
                        <img class="upvote <?= $hasLiked ? 'upvote-active' : 'upvoteInactive' ?>" src="<?= $upvoteImage ?>" alt="">
                    </div>
                    <p><?= $LikesSum ?></p>
                    <div class="downvote-section img-container">
                        <img class="downvote <?= $hasDisliked ? 'downvote-active' : 'downvoteInactive' ?>" src="<?= $downvoteImage ?>" alt="">
                    </div>
                </div>
            </div>
            <div class="bottom-section">
                <div class="left">
                    <button class="post-coment-button">comment</button>
                </div>
                <?php if (isset($_SESSION['user']) && $post['user_id'] === $_SESSION['user']['id']) : ?>
                    <div class="right">
                        <button class="post-edit-button">Edit</button>
                        <button class="post-delete-button">Delete</button>
                    </div>
                <?php endif ?>
            </div>
        </div>

        <?php foreach ($comments as $comment) : ?>
            <?php
            $commenter_id = $comment['user_id'];
            $result = $db->query("SELECT name FROM Users WHERE id = $commenter_id");
            $data = $result->fetch(PDO::FETCH_ASSOC);
            $commenter_name = $data['name'];
            $commentId = $comment['id'];

            //Fetch commenter
            $commenterId = $comment['user_id'];
            $result = $db->query("SELECT * FROM Users WHERE id = $commenterId");
            $commenter = $result->fetch(PDO::FETCH_ASSOC);
            $commentImageURL = $commenter['avatar_path'];
            isset($commenter['avatar_path']) ? $commentImageURL = $commenter['avatar_path'] : $commentImageURL = '/Account/uploads/default.svg';

            //If user has a name, set it to $userName
            if (isset($commenter['name'])) {
                $commenter_name = $commenter['name'];
            } else {
                $commenter_name = 'IHaveNoName';
            }
            ?>
            <div data-postId="<?= $postId ?>" data-id="<?= $commentId ?>" class="comment post<?= $postId ?> post-group<?= $postId ?> comment-id<?= $commentId ?>">
                <div class="upper">
                    <div class="left">
                        <img src=<?= $commentImageURL ?> alt="">
                        <p class="name"><?= $commenter_name ?></p>
                    </div>
                    <div class="right">
                        <p class="date"><?= date('D M Y H:i', $comment['date']) ?></p>
                    </div>
                </div>
                <div class="lower">
                    <div class="left">
                        <p class="comment-paragraph"><?= $comment['body'] ?></p>
                    </div>
                    <div class="right">
                        <?php if (isset($_SESSION['user']) && $comment['user_id'] === $_SESSION['user']['id']) : ?>
                            <button class="edit-button button">Edit</button>
                            <button class="delete-button button">Delete</button>
                        <?php endif; ?>
                        <span class="vote-number" data-id="<?= $commentId; ?>"><?= getUpvotes($db, $commentId) ?></span>
                        <?php if (isset($_SESSION['user'])) : ?>
                            <form class="like-comment-form" action="/Account/like-comment.php" method="post">
                                <input type="hidden" name="like-comment" id="comment-id" value="<?= $commentId; ?>"></input>
                                <?php if (!existUpvote($db, $commentId, $_SESSION['user']['id'])) : ?>
                                    <button style="background-color: black;" value="submit" type="submit" class="like-comment-button" data-id="<?= $commentId; ?>"></button>
                                <?php else : ?>
                                    <button style="background-color: rgb(37, 104, 246);" value="submit" type="submit" class="like-comment-button" data-id="<?= $commentId; ?>"></button>
                                <?php endif; ?>
                            </form>
                        <?php else : ?>
                            <button style="background-color: black;" value="submit" type="submit" class="like-comment-button" data-id="<?= $commentId; ?>"></button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    <?php endforeach ?>
    <script src="/script/like.js"></script>
    <script src="/script/delete_post.js"></script>
    <script src="/script/edit-comment.js"></script>
    <script src="/script/edit_post.js"></script>
    <script src="/script/scroll.js"></script>
    <script src="/script/comment.js"></script>
    <script src="/script/sort.js"></script>
    <script src="/script/hamburger.js"></script>
    <script src="/script/like-comment.js"></script>
</body>
<?php createMessage(3) ?>

<?php require('footer.php') ?>