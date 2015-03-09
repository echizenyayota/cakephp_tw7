<?php
// index15.phpでデータベースからだすツイートを選別
try {
    $dbh = new PDO('mysql:host=localhost;dbname=tweet1;charset=utf8', 'myusername','mypassword');
} catch(PDOException $e) {
    var_dump($e->getMessage());
    exit;
}
$sql = "select * from tweet_mm2 where
        (tw_txt not like '%@%')
            and
        (tw_img0 like 'http://pbs.twimg.com/media/%')
        order by tw_date desc";
$stmt = $dbh->query($sql);
?>