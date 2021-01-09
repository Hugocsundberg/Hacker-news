<?php
require('header.php');
require('../functions.php');
logMessage();

//Database connection
$db = new PDO('sqlite:../hacker_news_database.sqlite3');

//Fetch posts from database
$result = $db->query("SELECT * FROM Posts ORDER BY \"date\" DESC");
$posts = $result->fetchAll(PDO::FETCH_ASSOC);
?>


<body>
    <a href="/views/login.php">Login</a>
    <?php
    if (isset($_SESSION['user'])) : ?>
        <a href="/account/logout.php">Logout</a>
    <?php endif ?>

    <a href="/views/create_post.php">Create post</a>
    <a href="/views/account.php">Account</a>

    <?php foreach ($posts as $post) : ?>
        <?php
        $postId = $post['id'];

        //Fetch all comments on post
        $commentResult = $db->query("SELECT * FROM Comments WHERE post_id = $postId");
        $comments = $commentResult->fetchAll(PDO::FETCH_ASSOC);

        //Fetch likes on post
        $likesResult = $db->query("SELECT COUNT(user_id) AS 'likes' FROM Likes WHERE post_id = $postId AND up_down = 1");
        $likes = $likesResult->fetch(PDO::FETCH_ASSOC)['likes'];

        //Fetch dislikes
        $dislikeResult = $db->query("SELECT COUNT(user_id) AS 'dislikes' FROM Likes WHERE post_id = $postId AND up_down = 0");
        $dislikes = $dislikeResult->fetch(PDO::FETCH_ASSOC)['dislikes'];

        $LikesSum = $likes - $dislikes;

        //Fetch user from database
        $userId = $post['user_id'];
        $result = $db->query("SELECT * FROM Users WHERE id = $userId");
        $user = $result->fetch(PDO::FETCH_ASSOC);

        //If user has a name, set it to $userName
        if (isset($user['name'])) {
            $userName = $user['name'];
        } else {
            $userName = 'IHaveNoName';
        }

        ?>
        <div class="post id<?= $postId ?>">
            <div class="date-section">
                <div class="left">
                    <img src="/images/photo-1609050470947-f35aa6071497.jpeg" alt="">
                    <p class="name"><?= $userName ?></p>
                </div>
                <div class="right">
                    <p class="date"><?= date('D M Y H:i', $post['date']) ?></p>
                </div>
            </div>
            <div class="image-section">
                <img src="/images/photo-1609050470947-f35aa6071497.jpeg" alt="">
            </div>
            <div class="text-section">
                <div class="text-section-text">
                    <h2><?= $post['header'] ?></h2>
                    <p><?= $post['body'] ?></p>
                </div>
                <div class="text-section-vote" data-post="<?= $post['id'] ?>">
                    <div class="img-container">
                        <img class="upvote" src="/assets/up-arrow.svg" alt="">
                    </div>
                    <p><?= $LikesSum ?></p>
                    <div class="img-container">
                        <img class="downvote" src="/assets/down-arrow.svg" alt="">
                    </div>
                </div>
            </div>
            <div class="bottom-section">
                <div class="left">
                    <button>comment</button>
                </div>
                <?php if (isset($_SESSION['user']) && $post['user_id'] === $_SESSION['user']['id']) : ?>
                    <div class="right">
                        <button class="edit-button">Edit</button>
                        <button class="delete-button">Delete</button>
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

            //Fetch commenter
            $commenterId = $comment['user_id'];
            $result = $db->query("SELECT * FROM Users WHERE id = $commenterId");
            $commenter = $result->fetch(PDO::FETCH_ASSOC);

            //If user has a name, set it to $userName
            if (isset($commenter['name'])) {
                $commenter_name = $commenter['name'];
            } else {
                $commenter_name = 'IHaveNoName';
            }
            ?>
            <div class="comment">
                <div class="upper">
                    <div class="left">
                        <img src="/images/photo-1609050470947-f35aa6071497.jpeg" alt="">
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
                    <?php if (isset($_SESSION['user']) && $comment['user_id'] === $_SESSION['user']['id']) : ?>
                        <div class="right">
                            <button class="edit-button button">Edit</button>
                            <button class="delete-button button">Delete</button>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        <?php endforeach ?>
    <?php endforeach ?>
    <script src="/script.js"></script>
</body>
<?php createMessage(3) ?>

<?php require('footer.php') ?>