<?php
	$ipdb_yellow_interval = 60;
	$ipdb_red_interval = 300;
	$ipdb_del_interval = 3600;
	
	if(file_exists("db.sqlite")){
		$exists = 1;
	}
	
	$handle = sqlite_open("db.sqlite", 0666, $sqlite_error);
	if(!$handle){
		echo "データベースへの接続に失敗しました<br>";	
	}
	
	if($exists == 0){
		$sql = "create table ipdb (id varchar(64), ip varchar(64), lastupdate int)";
		$result_flag = sqlite_query($handle, $sql, SQLITE_BOTH, $sqlite_error);
		if(!$result_flag){
			echo "テーブルの作成でエラーが発生しました".$sqlite_error."<br>";
		}
	}
	
	if(isset($_GET["id"]) == 1){
	
	$sql = "SELECT id, ip, lastupdate FROM ipdb WHERE id='".sqlite_escape_string($_GET["id"])."'";
	$ret = sqlite_query($handle, $sql, SQLITE_BOTH, $sqlite_error);
	$rows = sqlite_num_rows($ret);
	$row = sqlite_fetch_array($ret, SQLITE_ASSOC);
	
	if($rows == 0){
		$sql = "INSERT INTO ipdb (id, ip, lastupdate) VALUES ('".sqlite_escape_string($_GET["id"])."', '".sqlite_escape_string($_SERVER{"REMOTE_ADDR"})."', '".time()."')";
		sqlite_exec($handle, $sql, $sqlite_error);
	}
	else{
		$sql = "UPDATE ipdb SET ip='".sqlite_escape_string($_SERVER{"REMOTE_ADDR"})."', lastupdate='".time()."' WHERE id='".sqlite_escape_string($_GET["id"])."'";
		sqlite_exec($handle, $sql, $sqlite_error);
	}
	
	}
	
	$sql = "SELECT * FROM ipdb ORDER BY lastupdate DESC;";
	$ret = sqlite_query($handle, $sql, SQLITE_BOTH, $sqlite_error);
	$rows = sqlite_num_rows($ret);

echo <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="ja">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!--<meta name="viewport" content="width=device-width,user-scalable=no,maximum-scale=1" />-->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<title>Portable IPDB</title>
</head>

<body>

<div class="hero-unit">
	<h1>Portable IPDB</h1>
	<p>動的に割り当てられる複数マシンのIPを集中管理</p>
</div>

<div class="alert alert-info">
	<button class="close" data-dismiss="alert">&times;</button>
	<strong>このシステムの管理対象にマシンを加えるには、マシン上で次のコマンドを定期的に実行してください</strong> <br>wget -O - 'http://examples.com/ipdb/index.php?id=(「識別用のID」フィールドに表示したい文字列)' > /dev/null
</div>

<table class="table table-bordered table-condensed" style="width: 50%;">
	<thead>
		<tr><th>識別用のID</th><th>IPアドレス</th><th>最終更新日時</th></tr>
	</thead>
EOF;

	for($ii = 0; $ii < $rows; $ii++){
		$row = sqlite_fetch_array($ret, SQLITE_ASSOC);	
		
		$timediff = time() - $row["lastupdate"];
		$render = "";
		if($timediff > $ipdb_yellow_interval){
			$render = '<span style="color: yellow;">▲</span>'.date("Y/m/d(D) H:i", $row["lastupdate"]);
		}
		if($timediff > $ipdb_red_interval){
			$render = '<span style="color: red;">×</span>'.date("Y/m/d(D) H:i", $row["lastupdate"]);
		}
		if($timediff > $ipdb_del_interval){
			$render = 'removed';
		}
		if(strcmp($render, "") == 0){
			$render = '<span style="color: blue;">●</span>'.date("Y/m/d(D) H:i", $row["lastupdate"]);
		}
		
		if(strcmp($render, "removed") != 0){
		
echo <<<EOF
	<tbody>
		<tr><td>{$row["id"]}</td><td>{$row["ip"]}</td><td>{$render}</td>
	</tbody>
EOF;
		
		}
	}
	
echo <<<EOF
</table>

<script src="js/jquery-1.8.2.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>

</html>
EOF;
	
	sqlite_close($handle);
?>