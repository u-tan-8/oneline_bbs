<?php 

//データベースに接続
$link = new mysqli('localhost', 'root','root','oneline_bbs');
//接続状況の確認
if (mysqli_connect_errno()) {
    echo "データベース接続失敗" . PHP_EOL;
    echo "errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "error: " . mysqli_connect_error() . PHP_EOL;
    exit();
}
 
// echo 'データベース接続成功';

//POSTなら保存処理実行
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //名前が正しく入力されているかチェック
    //issetは値が入っているか確認
    //strlenは文字列を確認
    $name = null;
    if (!isset($_POST['name']) || !strlen($_POST['name'])) {
        $errors['name'] = '名前を入力してください';
    } elseif (strlen($_POST['name']) > 40) {
        $errors['name'] = '名前は40文字以内で入力してください';
    } else {
        $name = $_POST['name'];
    }


    //ひとことが正しく入力されているかチェック
    $comment = null;
    if (!isset($_POST['comment']) || !strlen($_POST['comment'])) {
        $errors['comment'] = 'ひとことを入力してください';
    } elseif (strlen($_POST['comment']) > 200) {
        $errors['comment'] = 'ひとことは200文字以内で入力してください';
    } else {
        $comment = $_POST['comment'];
    }

    //エラーがなければ保存
    if (count($errors) === 0) {
        //保存するためのSQL文
        $sql = "INSERT INTO `post` (`name`,`comment`,`created_at`) VALUES ('"
        . mysqli_real_escape_string($link, $name) . "', '"
        . mysqli_real_escape_string($link, $comment) . "', '"
        . date('Y-m-d H:i:s') . "')";

        //保存する
        mysqli_query($link, $sql);

        mysqli_close($link);

        header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    }
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ひとこと掲示板</title>
</head>

<body>
    <h1>ひとこと掲示板</h1>

    <form action="bbs.php" method="post">
    <?php if (count($errors)): ?>
        <ul class="error_list">
            <?php foreach ($errors as $error): ?>
            <li>
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
        名前：<input type="text" name="name" /><br />
        ひとこと：<input type="text" name="comment" size="60" /><br />
        <input type="submit" name="submit" value="送信" />
    </form>

    <?php
        //投稿された内容を取得するSQLを作成して結果を取得
        $sql = "SELECT * FROM `post` ORDER BY `created_at` DESC";
        $result = mysqli_query($link, $sql);
    ?>

    <?php if ($result !== false && mysqli_num_rows($result)): ?>
        <ul>
            <?php while ($post = mysqli_fetch_assoc($result)): ?>
                <li>
                    <?php echo htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8'); ?>
                    <?php echo htmlspecialchars($post['comment'], ENT_QUOTES, 'UTF-8'); ?>
                    - <?php echo htmlspecialchars($post['created_at'], ENT_QUOTES, 'UTF-8'); ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>

    <?php
    //取得結果を開放して接続を閉じる
    mysqli_free_result($result);
    mysqli_close($link);
    ?>


</body>

</html>