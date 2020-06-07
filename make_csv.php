<?php
/*
* テスト用のダミーデータのcsvを生成するバッチプログラム
* メモリ1GBまで使う前提で、1000000行まで生成可能なことを確認
* 
* csvのカラム
* 1 ID シーケンシャルな1から始まる整数
* 2 メールアドレス ランダムで、ファイル全体でユニーク
*   ドメインは、大手ドメインと小規模ドメイン多数が含まれるように、一定の比率で。
* 3 SMTP応答コード
* 4 日時 YYYY/MM/DD hh:mm:ss型式 一定の期間の範囲からランダムに
* 5 ログインID 6文字のランダムな英数小文字　必ずユニーク
* 6 回答データ 100文字のランダムな英数小文字
*/

$fp = fopen('file.csv', 'w');

$max = 1000000;

// メールアドレス文字列の40%を占めるランダムなドメインを作成
$rand_domain_num = ($max * 0.4) / 50;
$rand_domains = [];
for($i=0;$i<$rand_domain_num;$i++){
    $rand_domains[] = '@'.uniqid().'.jp';
}

// 26の5乗
$rand_arr = range(0, 11881376);
//range()の範囲の整数を値に持つ配列を$rand_arに取得
shuffle($rand_arr);

for($i=0;$i<$max;$i++) {
    $arr = [];
    $arr[] = $i + 1;
    $arr[] = getMailAddress();
    $arr[] = getSMTP();
    $arr[] = getTime();
    $arr[] = getLoginId($rand_arr[$i]);
    $arr[] = getRandStr(100, true);
    fputcsv($fp, $arr);
}
fclose($fp);

// メールアドレス文字列
function getMailAddress() {
    global $rand_domains;
    $mail = uniqid();
    $rand = mt_rand(1, 100);
    if($rand < 30) {
        return $mail.'@gmail.co.jp';
    } else if ($rand < 50){
        return $mail.'@yahoo.co.jp';
    } else if ($rand < 60){
        return $mail.'@hotmail.co.jp';
    } else {
        $key = array_rand($rand_domains);
        return $mail.$rand_domains[$key];
    }
}

// SMTPの応答コードをランダムに生成
function getSMTP() {
    $smtps = [421, 450, 451, 452, 550, 551, 552, 553, 554];
    $rand = mt_rand(1, 100);
    if($rand < 90) {
        return 250;
    } else {
        $key = array_rand($smtps);
        return $smtps[$key];
    }
}

// 日時をランダムに生成
function getTime () {
    $min = strtotime("2019/06/08 00:00:00");
    $max = strtotime("2020/06/08 23:59:59");
    $date = mt_rand($min, $max);
    return date('Y/m/d h:m:s', $date);
}

// ログインIDを想定したID文字列
// 6桁すべてランダムだとメモリが足りないので、5桁で一意性を確保してランダムな6桁目を足す
function getLoginId($i) {
    $arr = range('a', 'z');
    $rand = $i;
    $mod = [];
    $digit = 0;
    while(true) {
        if($rand > 0) {
            $mod[] = $rand % 26;
            $digit++;
            $rand = floor($rand / 26);
        } else {
            break;
        }
    }
    $id_arr = [];
    for($j=0;$j<$digit;$j++) {
        $id_arr[] = $arr[$mod[$j]];
    }
    $id = implode('', $id_arr);
    for($k=0;$k<6-$digit;$k++) {
        $id = $arr[mt_rand(0,25)].$id;
    }
    return $id;
}

// 100文字のランダムな英数小文字
function getRandStr($length, $append = null) {
    $arr = range('a', 'z');
    if($append = true) {
        $arr += range('0', '9');
    }
    $str = null;
    $limit = count($arr) - 1;
    for ($i=0;$i<$length;$i++) {
        $str .= $arr[mt_rand(0, $limit)];
    }
    return $str;
}

?>