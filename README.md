PortableIPDB
======================
ダイナミックDNS的なものがほしいけど、わざわざDNSでやるほどでも…というような時に、ホストとIPの対応表を自動的に作成するアプリです。

ファイル構成
------
このアプリは以下のファイル構成となっています。  
* index.php  
アプリ本体
* css、img、js  
twitter bootstrap（ http://twitter.github.com/bootstrap/ ）およびjQuery（ http://jquery.com/ ）が格納されているディレクトリ
* README.md

動作環境
------
Apache2+PHPな環境で動作しますが、PDOじゃなくて素のSQLite系列の関数が使用可能である必要があります（sqlite_open等）。

使い方
------
index.phpにアクセスすることで、すでに登録されているホストとIPの対応表を表示することができます。   
新たに対応表にマシンを加えるには、「wget -O - 'http://(設置先パス)/index.php?id=(「識別用のID」フィールドに表示したい文字列)' > /dev/null」などという感じで、周期的に呼び出します。   
デフォルトでは最終更新から1分間、5分間、1時間の経過で黄色マーク表示、赤色マーク表示、非表示となりますが、以下の部分を変更することで間隔を変更することができます。
```
	$ipdb_yellow_interval = 60;
	$ipdb_red_interval = 300;
	$ipdb_del_interval = 3600;
```

ライセンス
------

```
Copyright 2012 Muzeria Lushe

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
```