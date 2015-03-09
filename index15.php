<!-- Miss of Miss　-->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>user timeline</title>
    <link rel="stylesheet" href="mystyle15.css">
    <script src="//twemoji.maxcdn.com/twemoji.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body>
    <?php require("selection15.php"); ?>
    <?php require("functions15.php"); // img_tag関数　?>

    <h1>Miss of Miss</h1>

    <?php foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $user) { ?>
        <div class= "tw_screen">
            <?php echo "@" . $user{'tw_screen'} . "<br/>"; ?>
        </div>
        <div class= "tw_name">
            <?php echo $user{'tw_name'} . "<br/>"; ?>
        </div>
        <div class= "tw_prf">
            <?php img_tag($user{'tw_prf'}); echo "<br/>"; ?>
        </div>
        <div class= "tw_date">
            <?php echo $user{'tw_date'} . "<br/>"; ?>
        </div>
        <div class= "tw_txt">
            <?php echo $user{'tw_txt'} . "<br/>"; ?>
        </div>
        <div id="tw_imgs">
            <?php for($i=0; $i<=3; $i++){ ?>
                <?php if (!empty($user{'tw_img' . $i})) { ?>
                    <?php img_tag($user{'tw_img' . $i} .':small'); ?>
                    <?php echo "<br>"; ?>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } ?>
<script>twemoji.parse(document.body);</script>
    <script>
        // <div class= "tw_screen">の数だけ繰り返す
        function check(){
            var image = document.getElementById("tw_imgs");
            var num = image.getElementsByTagName("img");
            var len = num.length;
            return len;
        }
        var n = check();

        for(var i = 0; i <= n ; i++) {
            (function(num){
                $(".tw_img").eq(num).click(function() {
                    $(this).fadeOut(800);
                    $(this).next().fadeIn(800);
                    // (n - 1)回目のときは最初の画像に戻る
                    // if (num == (n - 1)) {
                    if (num == n) {
                        console.log(1);
                        $(".tw_img").eq(1).fadeIn(800);
                        console.log(2);
                    }
                });
            })(i);
        }
    </script>
</body>
</html>
