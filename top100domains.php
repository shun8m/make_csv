<?php
/*
* テスト用のダミーデータのcsvを集計するバッチプログラム
* 2カラム目:メールアドレスのドメインを登録者数が多い順に集計し、
* 上位100件までのドメインと件数をcsvで出力する
*/

$fp = fopen('file.csv', 'r');

$counter = 0;
$count_arr = ['gmail.co.jp' => 0, 'yahoo.co.jp' => 0, 'hotmail.co.jp' => 0,];
$domains = [];

while (($data = fgetcsv($fp)) !== FALSE) {
  $mail = $data[1];
  $arr = explode('@', $mail);
  if($arr[1] === 'gmail.co.jp') {
    $count_arr['gmail.co.jp']++;
  } else if($arr[1] === 'yahoo.co.jp') {
    $count_arr['yahoo.co.jp']++;
  } else if($arr[1] === 'hotmail.co.jp') {
    $count_arr['hotmail.co.jp']++;
  } else {
    if(in_array($arr[1], $domains)) {
      $count_arr[$arr[1]]++;
    } else {
      $domains[] = $arr[1];
      $count_arr += [$arr[1] => 1];
    }
  }
}

fclose($fp);

arsort($count_arr);

$fp = fopen('100domains.csv', 'w');
$i=0;

foreach($count_arr as $k => $v) {
  $arr = [];
  $arr[] = $k;
  $arr[] = $v;
  fputcsv($fp, $arr);
  if($i > 98) { // 0~99で100
    break;
  }
  $i++;
}

fclose($fp);

?>