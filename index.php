<?php
/**
 * To install Neo4j-PHP-Client, we use Composer
 *
 * $ curl -sS https://getcomposer.org/installer | php
 * $ php composer.phar require graphaware/neo4j-php-client
 *
 */


require __DIR__ . '/vendor/autoload.php';

use GraphAware\Neo4j\Client\ClientBuilder;

// change to your hostname, port, username, password
$neo4j_url = "bolt://neo4j:azizakek@localhost";

// setup connection
$client = ClientBuilder::create()
    ->addConnection('default', $neo4j_url)
    ->build();
?>

<head>
    <script src="https://vk.com/js/api/openapi.js?150" type="text/javascript"></script>
</head>

<!--<body>-->
<!---->
<!--<div id="vk_auth"></div>-->
<!--<script type="text/javascript">-->
<!--    window.onload = function () {-->
<!--        VK.init({apiId: 6199555});-->
<!--        VK.Widgets.Auth('vk_auth', {});-->
<!--    }-->
<!--</script>-->
<!---->
<!--</body>-->

<?php

//https://oauth.vk.com/authorize?client_id=6199555&display=popup&redirect_uri=vk.com&scope=friends&response_type=code&v=5.69
const group1 = "Абоненты Мегафона";
const group2 = "Польхователи Android";
const group3 = "Пользователи Windows";
const group4 = "Пользователи iOS";


$vkid = $_POST['vkid'];
$chtest1 = $_POST['checkbox-test1'];
$chtest2 = $_POST['checkbox-test2'];
$hid_name = $_POST['hidden_name'];
session_start();
$_SESSION['hid_group'] = $hid_name;
echo $hid_name.'<br>';

//echo $vkid . '<br>';
//echo $chtest1 . '<br>';
//echo $chtest2 . '<br>';
//echo $hid_name . '<br>';
//
//echo $_SERVER['PHP_SELF'];
//
//echo $link = '<p><a href="https://oauth.vk.com/authorize?client_id=6199555&display=page&redirect_uri=vk.com/&scope=friends&response_type=code&v=5.69">Аутентификация через ВКонтакте</a></p>';
$_GET['code']


?>

<?php
//определяем тип объекта для дальнейшего взаимодействия с ним
$type_id = 'none';
$request_params = [

    'screen_name' => $vkid];
$url = 'https://api.vk.com/method/utils.resolveScreenName?' . http_build_query($request_params);
$response = json_decode(file_get_contents($url), true);
//echo $url;
//echo $response['response']['type'];
$type_id = $response['response']['type'];
$object_id = $response['response']['object_id'];
?>


<?php

$del = <<<EOQ
match (n) detach delete n;
EOQ;

$client->run($del);

$permissions = [
    'notify', 'friends', 'photos', 'audio', 'video', 'docs', 'pages', 'status', 'wall',
    'groups', 'messages', 'email', 'stats'

];


$request_params = [
    'client_id' => '6199555',
    'redirect_url' => 'https://oauth.vk.com/blank.html',
    'response_type' => 'token',
    'display' => 'page'
//'scope' => implode(',', $permissions)


];
$url = 'https://oauth.vk.com/authorize?' . http_build_query($request_params);
//$res = json_decode(file_get_contents($url),true);
//echo $res;

//
//echo $url;https://oauth.vk.com/authorize?client_id=6199555&redirect_uri=https://oauth.vk.com/blank.html&response_type=token&display=page


$token = 'aff2ddb760476813846c8e73813fcc8d1be8d10df56508ab68bb3afdfba4f328d86a12e10e910a65c90ef';


//НАЧАЛО запросов

//Вставка в БД

if ($type_id == 'group') {
    $request_par = [
        'group_id' => $vkid, //ru9gag thebassland eltech4383
        'access_token' => $token,
        'fields' => 'description,members_count'//нужно обновить
    ];
    $url = "https://api.vk.com/method/groups.getById?" . http_build_query($request_par); //getById getSettings
    $result = json_decode(file_get_contents($url), true);

//    echo "<br>";
//    echo $url . '<br>';

    $publos = $result['response'][0];

    $pub_name = $publos['name'];

    $params = ['gid' => $publos['gid'], 'name' => $publos['name'],
        'description' => $publos['description'], 'photo' => $publos['photo_medium'], 'count' => $publos['members_count']];


//    echo $params['description'] . '<br>';  //

    $quer = <<<EOQ
CREATE (n:Group {gid: {gid},
name: {name} ,description: {description}, photo: {photo} ,
count : {count} } )  ;
EOQ;

    $fields = ['connections', 'site', 'education', 'contacts', 'city', 'music', 'movies', 'sex', 'wall_comments', 'relation', 'schools', 'personal', 'tv', 'bdate', 'last_seen' , 'screen_name']; //добавил  , 'screen_name'

    $request_par = [
        'group_id' => $vkid,
        'sort' => 'id_asc',
        'offset' => 0,
//     'count' => 500,
        'fields' => implode(',', $fields),
        'access_token' => $token //нужно обновить
    ];
    $url = "https://api.vk.com/method/groups.getMembers?" . http_build_query($request_par);


    $result = json_decode(file_get_contents($url), true);

//    echo $url . '<br>';


    $users = $result['response']['users'];

//    var_dump($users);

    foreach ($users as &$username) {
        if (!array_key_exists('bdate', $username)) {
            $username['bdate'] = ' ';
//            $str = $username['bdate'];
//        echo "<script>console.log('\".$str.\"');</script>";
        }
        if (!array_key_exists('mobile_phone', $username)) {
            $username['mobile_phone'] = ' ';
//            $str = $username['bdate'];
//        echo "<script>console.log('\".$str.\"');</script>";
        }


    }


    $rs = <<<EOQ
MATCH (g:Group {name: {pub_name}}) 
FOREACH (att in {ats} |
CREATE (n:User)-[:Follows]->(g)
SET n.first_name = att.first_name
SET n.last_name = att.last_name
SET n.sex = att.sex
SET n.uid = att.uid
SET n.phone = att.mobile_phone
SET n.music = att.music
SET n.site = att.site
SET n.skype = att.skype
SET n.instagram = att.instagram
SET n.movies = att.movies
SET n.bdate = att.bdate
SET n.university = att.university_name
SET n.religion = att.personal.religion
SET n.political = att.personal.political
SET n.langs = att.personal.langs
SET n.relation = att.relation
SET n.alcohol = att.personal.alcohol
SET n.smoking = att.personal.smoking
SET n.device = att.last_seen.platform 
SET n.screen_name = att.screen_name
SET n.sub_gr = {pub_name});
EOQ;


    $client->run($quer, $params);
    $client->run($rs, ['ats' => $users, 'pub_name' => $pub_name]);

    $_SESSION['u'] = 0; // group

}
//////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////ПАРСИМ ПОЛЬЗОВАТЕЛЯ/////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////
else {
//    echo "АЗАЗАЗА".'<br>';
    $fields = ['connections', 'site', 'education', 'contacts', 'city', 'music', 'movies', 'sex', 'wall_comments', 'relation', 'schools', 'personal', 'tv', 'bdate', 'last_seen', 'screen_name']; // , 'screen_name'
    $request_par = [
        'user_ids' => $vkid,
        'access_token' => $token,
        'fields' => implode(',', $fields)

    ];
    $url = "https://api.vk.com/method/users.get?" . http_build_query($request_par); //getById getSettings

    $result = json_decode(file_get_contents($url),true);
//    $result = $result['response'][0];
//    $result = json_encode($result);
//    var_dump($result);
//    var_dump($result['response'][0]);


    $quer = <<<EOQ
CREATE (n:MainPerson) 
SET n.first_name = {att}.first_name
SET n.last_name = {att}.last_name
SET n.sex = {att}.sex
SET n.uid = {att}.uid
SET n.phone = {att}.mobile_phone
SET n.music = {att}.music
SET n.site = {att}.site
SET n.skype = {att}.skype
SET n.instagram = {att}.instagram
SET n.movies = {att}.movies
SET n.bdate = {att}.bdate
SET n.university = {att}.university_name
SET n.religion = {att}.personal.religion
SET n.political = {att}.personal.political
SET n.langs = {att}.personal.langs
SET n.relation = {att}.relation
SET n.alcohol = {att}.personal.alcohol
SET n.smoking = {att}.personal.smoking
SET n.device = {att}.last_seen.platform
SET n.screen_name = {att}.screen_name;
EOQ;
    $client->run($quer, ['att' => $result['response'][0]]);
//    echo 'Здесь что-то есть'.'<br>';
//    echo "";
    $user_id = $result['response'][0]['uid'];


    $request_par = [
        // 'user_id' => $user['uid'],
        'user_id' => $user_id,
        'order' => 'name',
        'offset' => 0,
        'count' => 2000,
        'fields' => implode(',', $fields),
        'access_token' => $token

    ];

    $url = "https://api.vk.com/method/friends.get?" . http_build_query($request_par);
//    echo '<br>'.$url.'<br>';

    $result = json_decode(file_get_contents($url),true);
//    var_dump($result);

    foreach ($result['response'] as &$username) { // СУКА РЕСПОНС БЛЯТЬ!
        if (!array_key_exists('bdate', $username)) {
            $username['bdate'] = ' ';
//            $str = $username['bdate'];
//        echo "<script>console.log('\".$str.\"');</script>";
        }
        if (!array_key_exists('mobile_phone', $username)) {
            $username['mobile_phone'] = ' ';
//            $str = $username['bdate'];
//        echo "<script>console.log('\".$str.\"');</script>";
        }

    }


    $query = <<<EOQ
MATCH (a:MainPerson)
FOREACH (att in {ats} |
CREATE (n:User)-[:Friends]->(a)
SET n.first_name = att.first_name
SET n.last_name = att.last_name
SET n.sex = att.sex
SET n.uid = att.uid
SET n.phone = att.mobile_phone
SET n.music = att.music
SET n.site = att.site
SET n.skype = att.skype
SET n.instagram = att.instagram
SET n.movies = att.movies
SET n.bdate = att.bdate
SET n.university = att.university_name
SET n.religion = att.personal.religion
SET n.political = att.personal.political
SET n.langs = att.personal.langs
SET n.relation = att.relation
SET n.alcohol = att.personal.alcohol
SET n.smoking = att.personal.smoking
SET n.device = att.last_seen.platform
SET n.screen_name = att.screen_name);
EOQ;


    $users = $result['response'] ;//0 ?
//    echo '<br><br>';
//    var_dump($sasat);
    $params = ['new_friends' => $users ]; //тут может не надо 0 ебаный
    $client->run($query,['ats' => $users ]);
//    echo "ЗДЕСЬ".'<br>';

    $_SESSION['u'] = 1; // поользоваетль

}


//echo "бойся 228".'<br>';
//
////ПОиск пидоров ВК
////$code =  'var users = [210700286,21070286,2107286,217286];'
////    . ' users.forEach(function(user, i, users) {'
////    .'var friends = API.friends.get({"user_id":   user  , "order": "name", "offset": "0", "count": "2000"}).items;' // делаем первый запрос и создаем массив
////    .'});'
////    .    'return friends;'; // вернуть массив друзей
//
//$code = 'var users = [210700286,21070286,2107286,217286];'
//    . 'var i = 0;'
//    . ' while (i < 4) {'
//    . 'var friends = API.friends.get({"user_id": users[i], "order": "name", "offset": "0", "count": "2000"}).items;' // делаем первый запрос и создаем массив
//    . 'i= i+1;'
//    . '};'
//    .    'return friends;'; // вернуть массив друзей
//echo $code.'<br>';
//$url = "https://api.vk.com/method/execute?code=" . urlencode($code)."&access_token=".$token;
//
//echo $url;
//
//
//echo "бойся 228".'<br>';
//$result = json_decode(file_get_contents($url),true);
//
//
//echo "бойся 228".'<br>';
//var_dump($result);



//$fields = ['sex'];
//
//foreach ($users as $user){
//    $request_par = [
//       // 'user_id' => $user['uid'],
//        'user_id' => $user['uid'],
//        'order' => 'name',
//        'offset' => 0,
//        'count' => 2000,
//        'fields' => implode(',', $fields),
//        'access_token' => $token
//
//   ];
//    echo "ДО АПИ".'<br>';
//    $url = "https://api.vk.com/method/friends.get?" . http_build_query($request_par);
//    $result = json_decode(file_get_contents($url),true);
//    echo "ПОСЛЕ АПИ".'<br>';
//    $query = <<<EOQ
//MATCH (n:User {uid: {uid}})
//FOREACH ( fr in {new_friends} |
//MERGE (a { uid: fr.uid })
//MERGE (a)-[:Friends]->(n)
//ON CREATE SET a.first_name = fr.first_name
//ON CREATE SET a.last_name = fr.last_name
//ON CREATE SET a.bdate = fr.bdate
//ON CREATE SET a.sex = fr.sex
//SET a:Friend );
//EOQ;
//        $sasat = $result['response'];
//        var_dump($sasat).'<br>';
//        if(!is_null($sasat)){
//            $params = ['uid' =>  $user['uid'], 'new_friends' => $sasat ]; //тут может не надо 0 ебаный
//            $client->run($query,$params);
//            echo "нормас".'<br>';
//        }
//        else
//            echo "НЕНОРМАС".'<br>';
//
//}

//Запрос на обработку друзей пользователя

//$count_us = count($users);
//$fields = ['sex', 'bdate'];
//for ($indx = 0; $indx < $count_us; $indx++) {
//    //echo count($users).'<br>';
//    //echo ++$counter;
//    echo $indx.'<br>';
//    $request_par = [
//       // 'user_id' => $user['uid'],
//        'user_id' => $users[$indx]['uid'],
//        'order' => 'name',
//        'offset' => 0,
//        'count' => 3,
//        'fields' => implode(',', $fields),
//        'access_token' => $token
//
//   ];
//
//    echo $users[$indx]['uid'].'<br>';
//
//    $url = "https://api.vk.com/method/friends.get?" . http_build_query($request_par);
//    echo "ПОСЛЕ БИЛДА ПЕРЕД JSON".'<br>';
//    if (file_get_contents($url) == false){
////        echo "ФАЛСЕ".'<br>';
//    }
//    else {
//        $result = json_decode(file_get_contents($url),true);
////        echo $url.'<br>';
////        echo 'После АПИ'.'<br>';
//        // echo $result['response'][0]['first_name'].'<br>';
//        $query = <<<EOQ
//MATCH (n:User {uid: {uid}})
//FOREACH ( fr in {new_friends} |
//MERGE (a { uid: fr.uid })
//MERGE (a)-[:Friends]->(n)
//ON CREATE SET a.first_name = fr.first_name
//ON CREATE SET a.last_name = fr.last_name
//ON CREATE SET a.bdate = fr.bdate
//ON CREATE SET a.sex = fr.sex
//SET a:Friend );
//EOQ;
//        $sasat = $result['response'];
//        $user = $users[$indx]['uid'];
//        $params = ['uid' =>  $user, 'new_friends' => $sasat ]; //тут может не надо 0 ебаный
//        $client->run($query,$params);
//    }
//
//
//
//}


//Запрос на поиск МТС-пользователей

//echo "ПОКА НЕ МТС" . '<br>';

//
//$query = <<<EOQ
//CREATE (g:HiddenGroup {name: "Пользователи мтс"})
//match (n:User) where n.phone =~ '^.*[9](([0][248])|([1][0-9])|(78)|(8[0-9])).*$'
//SET n:МТС
//CREATE (n)-[:isProvidedBy]->(g);
//EOQ;

//$query = <<<EOQ
//match (n:User) where n.phone =~ '^.*[9](([2][0-9])|([3][0-9])).*$'
//Merge (g:HiddenGroup {name: "Пользователи мегафон"})
//SET n:Мегафон
//CREATE (n)-[:isProvidedBy]->(g)
//return *;
//EOQ;


//$palec =  $client->run("match (n) return n;");
//$json_palec = json_encode(array($palec->records()));
////var_dump($palec->records());
////echo '<br>'.'ЧТО так'.'<br>';
////var_dump($json_palec);
//
//$_SESSION['myj'] = $json_palec;
//
$_SESSION['h'] = 1;

//echo $_SESSION['hid_group'].'<br>';
//echo $_SESSION['h'].'<br>';
//
//echo "МТС" . '<br>';
//запрос на билайн-пользователей
//match (n) where n.phone =~ '^.*[9](([0][35689])|([5][13])|(6[0-9])).*$'
//return n.phone;

//запрос на мегафон-пользователей
//match (n) where n.phone =~ '^.*[9](([2][0-9])|([3][0-9])).*$'
//return n.phone;


//$html = 'http://vk.com/foaf.php?id=164283384';
//echo "ЗДЕСЬ ВСЕ";
////$dom = new DOMDocument;
////echo "ЗДЕСЬ ВСЕ";
////$dom->loadHTML($html);
//$xml = simplexml_load_file(($html));
//var_dump($xml);
//echo "ЗДЕСЬ еще что-то";
//$page = new SimpleXMLElement($xml);
//
//echo "ЗДЕСЬ ничто";
//echo $page->getDocNamespaces(); //['foaf:Person']->['ya:created']
//echo "ЗДЕСЬ что";
//
////echo "ЗДЕСЬ ВСЕ";
////var_dump($dom);
////$kek = $dom->getElementsByTagName('body');
////var_dump($kek) ;
//echo "ИЛИ НЕ ВСЕ";

//$usersid = array("9762442", "146120368"); //'0'=> '1'=>
//$usersid = ['0' =>9762442, '1' =>146120368];
//$usersid =
//echo "<br><br><br>";
//$code = urlencode('var b = [210700286,164283384];'
//    .'var c = 0;'
//    .'var i = 0;'
//    .'while (i < 2) {'
//    .'b = API.photos.get({"owner_id":b[i] ,"album_id": "wall"});'
//    .'i = i+1;}'
//    .'return c;');
//$url = "https://api.vk.com/method/execute?code=".$code."&access_token=".$token;//."&usersid=".$usersid;
//echo ($url);

//$code = urlencode('var a;'
//    .'var itemList = [];'
//    .'var i = 0;'
//    .'while (i < 2) {'
////   .'a = a + [API.users.get({"user_ids": Args.userid[i] , "fields": "counters"})];'
//    .'a = API.users.get({"user_ids": Args.userid[i] , "fields": "counters"});'
//    .'var kek = Args.userid;'
//    .'itemList.push(kek[0]);'
//    .'i = i+1;}'
//    .'return itemList;');
//$url = "https://api.vk.com/method/execute?code=".$code."&access_token=".$token."&userid=".$usersid;//."&usersid=".$usersid;
//echo ($url);

//ПРОБУЕМ ЭТУ ФУНКЦИЮ для преобразования объекта рекурсивного в массив
//function obj2arr($obj)
//{
//    if (!is_object($obj) && !is_array($obj)) return $obj;
//    if (is_object($obj)) {
//        $obj = get_object_vars($obj);
//    }
//    if (is_array($obj)) {
//        foreach ($obj as $key => $val) {
//            $obj[$key] = $this->obj2arr($val);
//        }
//    }
//    return $obj;
//}
echo "ЛОЛ";
//function obj2arr($obj)
//{
//    if (!is_object($obj) && !is_array($obj)) return $obj;
//    if (is_object($obj)) {
//        $obj = get_object_vars($obj);
//    }
//    if (is_array($obj)) {
//        foreach ($obj as $key => $val) {
//            $obj[$key] = $this->obj2arr($val);
//        }
//    }
//    return $obj;
//}

class Vkapi_Model_Api {

    private $_accessToken = null;

    private $_apiUrl = 'https://api.vk.com/method/';

    public function __construct($accessToken) {

        $this->_accessToken = $accessToken;

    }

    public function api($method, $params = array())
    {
        $params['access_token'] = $this->_accessToken;
        $query = $this->_apiUrl. $method . '?' . $this->_params($params);
        $responseStr = file_get_contents($query);
        if(!is_string($responseStr)){
            return null;
        }

        $responseObj = json_decode($responseStr, true); //добавил true  в конце
        return $responseObj;
//        return $responseStr;
    }

    private function _params($params)
    {
        $pice = array();
        foreach ($params as $k => $v) {
            $pice[] = $k . '=' . urlencode($v);
        }
        return implode('&', $pice);
    }
}

$code = 'var a;'
    .'var itemList = [];'
    .'var i = 0;'
    .'var parameter = "";'
    .'var start = 0;'
    .'var targets = Args.userid;'
    .'while(start<targets.length){
     if (targets.substr(start, 1) != " " && start != targets.length){
         parameter = parameter + targets.substr(start, 1);
     }
    else {'
    .'a = API.users.get({"user_ids": parameter , "fields": "counters"});'
    .'itemList.push(a); parameter = ""; }'
    .'start = start + 1;}'
    .'return itemList;';

$api = new Vkapi_Model_Api($token);

if($hid_name=='Меломаны' || 'Видеофилы' || 'Высокого мнения о себе'){
//    echo "ЗАШЛИ СЮДА".'<br>';
    $newusers = $client->run("match (n:User) return n.uid");

    $user_arr = [];
    $indx = 0;
    foreach ($newusers->records() as $record){
//    if (++$indx > 25) break;
//    var_dump($record->get('n.uid'));
        $user_arr[$indx] = $record->get('n.uid');
        $indx++;// = $user_arr.(string)$record->get('n.uid').' ';
    }
    $indx =0;
//    var_dump($user_arr);

    $number = count($user_arr);

    $mer_arr = [];

//    echo '<br><br><br>';
    for ($k = 0; $k < 9 && $number > 0;$k++){ // сделаю 9, окей надо 40, конечно
//while($number > 0){
        $part_25 = '';
//        echo '<br><br>'.$k.' -итерация'.PHP_EOL;
//    if ($k%9==0){
//        echo 'поспим'.PHP_EOL;
//        sleep(4);
//    }


        for ($i = 0; $i< 25 && $number > 0; $i++) {

            $part_25 = $part_25.(string)array_shift($user_arr).' ';
            $number--; //минус 1 элемент
        }

//        echo '<br><br><br>';
//        var_dump($part_25);
//        echo '<br><br>'.'ОТВЕТ'.'<br>';
        $response = $api->api('execute',array('code' => $code,'userid'=>$part_25 )); //$usersid (array)

//
//        $url = "https://api.vk.com/method/execute?code=".urlencode($code)."&userid=".$part_25."&access_token=".$token;//."&usersid=".$usersid;
//        var_dump($url);
//        $response = file_get_contents($url);//json_decode(file_get_contents($url),true);
//        echo "НУ, за старое, нахуй".'<br>';
//        var_dump($response); echo '<br>';
//        echo "НУ, за МОЛОДОЕ НАХУЙ, нахуй".'<br>';

        //мега-костыль
        $new_arr_res = [];
        $remake_res = $response['response'];
        foreach( $remake_res as $user ){
//            var_dump($user); echo '<br>';
            array_push($new_arr_res,$user[0]);
        }
//        echo 'А ТЕПЕРЬ РАЗНИЦА БЛЯЬ'.'<br>';
//        var_dump($new_arr_res);

//        var_dump($response); // ['response']
        $mer_arr = array_merge($mer_arr,$new_arr_res); //попытка ['response']
//        echo '<br><br>'.'НУ ЧЕ ТАМ У ХОХЛОВ ?'.'<br>';
    }

//    echo '<br>'.'вышли'.PHP_EOL;
//    var_dump($mer_arr);
//    echo '<br>'.'ПОПРОБУЕМ'.'<br>';

    /////////////
    /// Запись в БД данных
    ///
    ///
    ///
    ///
    ///
    $query = <<<EOQ
match (n:User) where n.uid = {man}.uid
set n.audios = {man}.counters.audios
set n.videos = {man}.counters.videos
set n.albums = {man}.counters.albums
set n.photos = {man}.counters.photos
set n.friends = {man}.counters.friends
set n.online_friends = {man}.counters.online_friends
set n.followers = {man}.counters.followers
set n.subscriptions = {man}.counters.subscriptions
set n.pages = {man}.counters.pages
set n.groups = {man}.counters.groups
;
EOQ;

    foreach($mer_arr as $user){
//        var_dump($user); echo '<br>';
        $client->run($query, ['man' => $user ]);
   }
//    echo "Получилось ?".'<br>';

}
echo "ну че";

?>

<?php
//echo '<script> location("http://localhost:63342/php3/vkek.php");</script>';
//exit();

header('Location: http://localhost:63342/php3/vkek.php');
exit();
?>



