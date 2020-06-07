<?php
/*
* テスト用のダミーデータのcsvを集計するバッチプログラム
* make_csv.phpで生成された2つのcsvファイルの5カラム目ログインIDを比較し、完全一致した行を出力する
*/

// 2つのcsvからlogin_idを抽出する
$fp = fopen('file_1.csv', 'r');
$login_ids_1 = [];
while (($data = fgetcsv($fp)) !== FALSE) {
  $login_ids_1[] = $data[4];
}
fclose($fp);
$fp = fopen('file_2.csv', 'r');
$login_ids_2 = [];
while (($data = fgetcsv($fp)) !== FALSE) {
  $login_ids_2[] = $data[4];
}
fclose($fp);

// 2つの配列の共通項のキーを、それぞれ配列としてまとめる
$result1 = array_intersect($login_ids_1, $login_ids_2);
$keys1 = [];
foreach($result1 as $k => $v) {
  $keys1[] = $k + 1;
}
$result2 = array_intersect($login_ids_2, $login_ids_1);
$keys2 = [];
foreach($result2 as $k => $v) {
  $keys2[] = $k + 1;
}

if(empty($keys1) && empty($keys2)){
  print "完全一致した行は見つかりませんでした";
  exit;
} 

// 2つの配列の共通項のキーを元に、出力する行を取得する
$fp = fopen('file_1.csv', 'r');
$csv1 = [];
while (($data = fgetcsv($fp)) !== FALSE) {
  if(in_array($data[0], $keys1)) {
    $csv1[] = $data;
  }
}
fclose($fp);
$fp = fopen('file_2.csv', 'r');
$csv2 = [];
while (($data = fgetcsv($fp)) !== FALSE) {
  if(in_array($data[0], $keys2)) {
    $csv2[] = $data;
  }
}
fclose($fp);

// ソートする
$csv1_ids = array_column($csv1, 4);
$csv2_ids = array_column($csv2, 4);
array_multisort($csv1_ids, SORT_DESC, $csv1);
array_multisort($csv2_ids, SORT_DESC, $csv2);

$fp = fopen('file_common.csv', 'w');

foreach($csv1 as $k => $v) {
  fputcsv($fp, $v);
  fputcsv($fp, $csv2[$k]);
}
fclose($fp);
?>