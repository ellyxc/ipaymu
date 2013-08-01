<?php
$url = 'http://ipaymu.local/ipaymu/index/unotify/orderId/4';
$params = array(
    'status' => 'berhasil',
    'trx_id' => '3440',
    'sid' => 'f0b3e464352b2642100b911c94db82da63c182ceef41e21d4b5cd8b0b80c8642dbcb562ddb3f02d2b08cee9037e5234bb94c18aad9a11310b5ea931560aa1e83',
    'product' => 'test',
    'quantity' => 1,
    'merchant' => 'nitybudaya',
    'buyer' => 'ellyxc',
    'total' => 3000,
    'action' => 'payment',
    'comments' => 'test',
    'referer' => 'https://my.ipaymu.com'
);
 
$params_string = http_build_query($params);
 
//open connection
$ch = curl_init();
 
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, count($params));
curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
 
//execute post
$request = curl_exec($ch);
 
if ( $request === false ) {
    echo 'Curl Error: ' . curl_error($ch);
} else {
 
    echo 'success';
}
 
//close connection
curl_close($ch);