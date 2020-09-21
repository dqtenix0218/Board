<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>Mission_5-1</title>
</head>

<body>
    <?php
    //データベス接続
    $dsn = 'mysql:dbname=*********;host=localhost';
    $user = '*********';
    $password = '********';
    $pdo = new PDO('データベース名', 'ユーザー名','パスワード', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //送信ボタンが押された際の処理
    if (isset($_POST['add'])) {
        if (!empty($_POST['name']) && !empty($_POST['comment'])) {
            //各変数の受け取り
            $name = $_POST['name'];
            $comment = $_POST['comment'];
            $time = date('Y/m/d/ H:i:s');
            $pass = $_POST['pass'];

            //テーブルが存在しなければ生成
            $sql = "CREATE TABLE IF NOT EXISTS member"
                . " ("
                . "id INT AUTO_INCREMENT PRIMARY KEY,"
                . "name varchar(32),"
                . "comment TEXT,"
                . "timestamp datetime,"
                . "password varchar(30)"
                . ");";
            $stmt = $pdo->query($sql);

            //編集入力モードでないときの処理
            if (empty($_POST['id'])) {
                //入力内容をテーブルに追加
                $sql = $pdo->prepare("INSERT INTO member (name, comment,timestamp,password) VALUES (:name, :comment,:timestamp,:password)");
                $sql->bindParam(':name', $name, PDO::PARAM_STR);
                $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql->bindParam(':timestamp', $time, PDO::PARAM_STR);
                $sql->bindParam(':password', $pass, PDO::PARAM_STR);
                $sql->execute();
            } else { //編集入力モードの時
                //テーブルの上書き
                $id = $_POST['id'];
                $sql = 'UPDATE member SET name=:name,comment=:comment,timestamp=:timestamp,password=:password WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':timestamp', $time, PDO::PARAM_STR);
                $stmt->bindParam(':password', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        } else {
            echo '名前もしくはコメントが入力されていません。<br>';
        }
    } elseif (isset($_POST['delete'])) { //削除ボタンが押されたら
        if (!empty($_POST['delete_num']) && !empty($_POST['pass'])) { //編集対象番号、パスワードが入力されているか確認
            //削除対象番号の投稿のパスワードを取得
            $delete_num = $_POST['delete_num'];
            $sql = 'SELECT password FROM member WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delete_num, PDO::PARAM_INT);
            $stmt->execute();
            $rec = $stmt->fetchAll();
            foreach ($rec as $row) {
                $pass = $row['password'];
            }
            //パスワードと一致しているか確認
            if ($pass == $_POST['pass']) {
                //テーブルからデータ削除
                $sql = 'DELETE FROM member WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $id = $_POST['delete_num'];
                $stmt->execute();
            } else {
                echo 'パスワードが違います。';
            }
        } else {
            echo '削除対象番号およびパスワードを入力してください。<br>';
        }
    } elseif (isset($_POST['edit'])) { //編集ボタンが押されたら
        if (!empty($_POST['edit_num']) && !empty($_POST['pass'])) { //編集対象番号、パスワードが入力されているか確認
            //編集対象番号のパスワードを取得
            $id = $_POST['edit_num'];
            $sql = 'SELECT * FROM member WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $rec = $stmt->fetchAll();

            foreach ($rec as $row) {
                $pass = $row['password'];
            }
            //パスワードと一致しているか確認
            if ($pass == $_POST['pass']) {
                foreach ($rec as $row) {
                    //編集対象のデータを取得
                    $edit_num = $row['id'];
                    $edit_name = $row['name'];
                    $edit_comment = $row['comment'];
                }
            } else {
                echo 'パスワードが違います。<br>';
            }
        } else {
            echo '編集対象番号およびパスワードを入力してください。<br>';
        }
    }
    ?>

    <!--入力フォーム-->
    <form method="post">
        <input type="hidden" name="id" value="<?php if (isset($edit_num)) {
                                                    echo $edit_num;
                                                } ?>">
        <input type="password" name="pass" placeholder="パスワード">
        <input type="text" name="name" placeholder="名前" value="<?php if (isset($edit_name)) {
                                                                    echo $edit_name;
                                                                } ?>">
        <input type="text" name="comment" placeholder="コメント" value="<?php if (isset($edit_comment)) {
                                                                        echo $edit_comment;
                                                                    } ?>">
        <input type="submit" name="add">
        <input type="number" name="delete_num" placeholder="削除対象番号">
        <input type="submit" name="delete" value="削除">
        <input type="number" name="edit_num" placeholder="編集対象番号">
        <input type="submit" name="edit" value="編集">
    </form>
    <!--投稿の表示-->
    <?php
    //テーブルから順番に投稿番号、名前、コメント、時間を取得して表示
    $sql = 'SELECT * FROM member';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row) {
        echo $row['id'] . ',';
        echo $row['name'] . ',';
        echo $row['comment'] . ',';
        echo $row['timestamp'];
        echo "<hr>";
    }
    ?>
</body>

</head>

</html>