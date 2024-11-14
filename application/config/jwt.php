<?php
defined('BASEPATH') or exit('No direct script access allowed');

$config['jwt_key'] = '5c0082b9e31a9e658895842de92b4a33f6c56fb2c3bca72287facee82d9f719536fd67089b5950f1fde6f3920287be449ddd44afaab9fab8b2a47253d9407dfa83eaf39821c61a554971b2b5b7ffdb5721cd4c4b8b8dc812d809ff2258b76434eda33b5d1e8e3be79901a75ffef564fd8d92a075bf179658d11a701def0a87e045ab183b5bcaba061feed26cb0dcb29b3dc1b214f06bd6c4563c88ef006bc486d82e594283b82ab8a51a238a26ae7ba5b8e9ab69488f52bf824a30fdfec26966130742db309fbb4e12f1b8b51b19b6bee72602bc0f7c5e784f329492471ab58f48fb24417131a229c63445af61889415c198d9ca707bfdedae221e3eecca3d79';
// $config['jwt_key'] = getenv('JWT_SECRET');
$config['jwt_algorithm'] = 'HS256';
$config['token_header'] = 'Authorization';
$config['token_expire_time'] = '7200';
