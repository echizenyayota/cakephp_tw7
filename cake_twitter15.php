<?php
// cakephp_tw6と同じ
// selection15.phpへ ユーザータイムラインのデータをDBに格納
function userTimeline($screenname) {
	// データベースの接続（最新のステータスIDを取得）
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=tweet1;charset=utf8', 'myusername','mypassword');
	} catch(PDOException $e) {
		var_dump($e->getMessage());
		exit;
	}

	// もっとも大きい（新しい）ステータスIDを1件選択するためのクエリ文を発行
	$sql = "select tw_id from tweet_mm2 order by tw_id desc limit 1";
	$stmt = $dbh->query($sql);

	// ステータスIDを取得する
	foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $user) {
		// 0以上ではダメなので1以上にする
		if ($user['tw_id'] >= 1) {
			$sinceid_str = $user['tw_id'];
		}
		// 文字列型を整数型に
		$sinceid = intval($sinceid_str);
	}

	// 切断
	$dbh = null;

	// twitterOAuth呼び出し
	require_once("twitteroauth/twitteroauth.php");

	$consumerKey = "MYCONSUMERKEY";
	$consumerSecret = "MYCONSUMERSECRET";
	$accessToken = "MYACCESSTOKEN";
	$accessTokenSecret = "MYACCESSTOKENSECRET";

	$twObj = new TwitterOAuth($consumerKey,$consumerSecret,$accessToken,$accessTokenSecret);

	$request = $twObj->OAuthRequest('https://api.twitter.com/1.1/statuses/user_timeline.json','GET',
		array(
			'count'=>'1',
			'screen_name' => $screenname,
			'since_id' => $sinceid,
	 		));
	$results = json_decode($request);

	if(isset($results) && empty($results->errors)){
		foreach($results as $tweet){
			 // データベースへ接続（ツイートの格納）
			try {
				$dbh = new PDO('mysql:host=localhost;dbname=tweet1;charset=utf8', 'myusername','mypassword');
			} catch(PDOException $e) {
				var_dump($e->getMessage());
				exit;
			}
			$stmt = $dbh->prepare(
				"insert into tweet_mm2 (
					tw_id,
					tw_name,
					tw_screen,
					tw_prf,
					tw_date,
					tw_txt,
					tw_img0,
					tw_img1,
					tw_img2,
					tw_img3
					) values (
					:tw_id,
					:tw_name,
					:tw_screen,
					:tw_prf,
					:tw_date,
					:tw_txt,
					:tw_img0,
					:tw_img1,
					:tw_img2,
					:tw_img3)");

			$stmt->execute(
			// ステータスID、スクリーンネーム、日付は必ずあるという前提
			array(
				":tw_id" => $tweet->id,
				":tw_name" => $tweet->user->name,
				":tw_screen" => $tweet->user->screen_name,
				":tw_prf" => $tweet->user->profile_image_url,
				":tw_date" => date('Y-m-d H:i:s', strtotime($tweet->created_at)),
				":tw_txt" => (!isset($tweet->text) ? "" : $tweet->text),
				":tw_img0" => (!isset($tweet->extended_entities->media[0]->media_url) ? "": $tweet->extended_entities->media[0]->media_url ),
				":tw_img1" => (!isset($tweet->extended_entities->media[1]->media_url) ? "": $tweet->extended_entities->media[1]->media_url ),
				":tw_img2" => (!isset($tweet->extended_entities->media[2]->media_url) ? "" : $tweet->extended_entities->media[2]->media_url),
				":tw_img3" => (!isset($tweet->extended_entities->media[3]->media_url) ? "" : $tweet->extended_entities->media[3]->media_url )
				)
			);
			// 実行
			$dbh = null;
		}
	} else {
		echo "関連したつぶやきがありません。";
	}
}

// 複数アカウントのツイートを取得
userTimeline("nhk_news");
userTimeline("nhk_osaka_JOBK");
userTimeline("nhk_seikatsu");

// 確認
echo "done!";

?>
