<?php
/**
 * To install Neo4j-PHP-Client, we use Composer
 *
 * $ curl -sS https://getcomposer.org/installer | php
 * $ php composer.phar require graphaware/neo4j-php-client
 *
 */

require __DIR__.'/vendor/autoload.php';

use GraphAware\Neo4j\Client\ClientBuilder;

// change to your hostname, port, username, password
$neo4j_url = "bolt://neo4j:azizakek@localhost";

// setup connection
$client = ClientBuilder::create()
    ->addConnection('default',  $neo4j_url)
    ->build();
?>

<?php
$permissions = [
'notify','friends','photos','audio','video','docs','pages','status','wall',
'groups','messages','email','stats'

];


$request_params = [
'client_id' => '6199555',
'redirect_url' => 'https://oauth.vk.com/blank.html',
'response_type' => 'token',
'display' => 'page'
//'scope' => implode(',', $permissions)


];
$url = 'https://oauth.vk.com/authorize?' .  http_build_query($request_params);
//$res = json_decode(file_get_contents($url),true);
//echo $res;

//https://oauth.vk.com/authorize?client_id=164283384&redirect_uri=https://oauth.vk.com/blank.html&response_type=token&display=page

//echo $url;

$token = 'ec8cc03d321d73a69a1397f582195de07ab87f3fb8cdc093d02b77ad04dca547a593fa74ef881e672e552';



//НАЧАЛО запросов

//Вставка в БД

$request_par = [
    'group_id'=> 'thebassland', //ru9gag thebassland
     'access_token'=>$token,
    'fields'=>'description,members_count'//нужно обновить
    ];
 $url = "https://api.vk.com/method/groups.getById?" .  http_build_query($request_par); //getById getSettings
 $result = json_decode(file_get_contents($url),true);

echo "<br>";
echo $url.'<br>';

$publos = $result['response'][0];

$pub_name = $publos['name'];


$params = ['gid' => $publos['gid'],'name' => $publos['name'],
    'description' => $publos['description'], 'photo' => $publos['photo_medium'], 'count' => $publos['members_count'] ];


echo $params['description'].'<br>';  //

$quer = <<<EOQ
CREATE (n:Group {gid: {gid},
name: {name} ,description: {description}, photo: {photo} ,
count : {count} } )  ;
EOQ;


$fields = ['connections', 'site','education','contacts','city', 'music' , 'movies' , 'sex' ,'wall_comments', 'relation' , 'schools' , 'personal' , 'tv' , 'bdate'];

 $request_par = [
     'group_id'=> 'thebassland',
     'sort' => 'id_asc',
     'offset' => 0,
     'count' => 10,
     'fields'=>  implode(',', $fields),
     'access_token'=>$token //нужно обновить
 ];
 $url = "https://api.vk.com/method/groups.getMembers?" .  http_build_query($request_par);
 $result = json_decode(file_get_contents($url),true);

echo $url.'<br>';


$users = $result['response']['users'];

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
SET n.sub_gr = {pub_name});
EOQ;

$client->run($quer, $params);
$client->run($rs, ['ats' => $users,'pub_name' => $pub_name ]);

//Запрос на обработку друзей пользователя

$count_us = count($users);
$fields = ['sex', 'bdate'];
for ($indx = 0; $indx < $count_us; $indx++) {
    //echo count($users).'<br>';
    //echo ++$counter;
    $request_par = [
       // 'user_id' => $user['uid'],
        'user_id' => $users[$indx]['uid'],
        'order' => 'name',
        'offset' => 0,
        'count' => 10,
        'fields' => implode(',', $fields),
        'access_token' => $token

   ];

    $url = "https://api.vk.com/method/friends.get?" . http_build_query($request_par);
    $result = json_decode(file_get_contents($url),true);
    echo $url.'<br>';
    echo $result['response'][0]['first_name'].'<br>';
    $query = <<<EOQ
MATCH (n:User {uid: {uid}})
FOREACH ( fr in {new_friends} |
MERGE (a { uid: fr.uid })
MERGE (a)-[:Friends]->(n)
ON CREATE SET a.first_name = fr.first_name
ON CREATE SET a.last_name = fr.last_name
ON CREATE SET a.bdate = fr.bdate
ON CREATE SET a.sex = fr.sex
SET a:Friend );
EOQ;
    $sasat = $result['response'];
    $user = $users[$indx]['uid'];
    $params = ['uid' =>  $user, 'new_friends' => $sasat ]; //тут может не надо 0 ебаный
    $client->run($query,$params);

}


//Запрос на поиск МТС-пользователей

//CREATE (g:HiddenGroup {name: "Пользователи мтс"})
//match (n) where n.phone =~ '^.*[9](([0][248])|([1][0-9])|(78)|(8[0-9])).*$'
//SET n:МТС
//CREATE (n)-[:isProvidedBy]->(g)
//return n

//запрос на билайн-пользователей
//match (n) where n.phone =~ '^.*[9](([0][35689])|([5][13])|(6[0-9])).*$'
//return n.phone;

//запрос на мегафон-пользователей
//match (n) where n.phone =~ '^.*[9](([2][0-9])|([3][0-9])).*$'
//return n.phone;

//
////Запрос на обработку сообществ пользователя
//$fields = ['id', 'name', 'city', 'country', 'place', 'description', 'wiki_page', 'members_count', 'counters', 'start_date', 'finish_date', 'can_post', 'can_see_all_posts', 'activity', 'status', 'contacts', 'links', 'fixed_post', 'verified', 'site', 'can_create_topic'];
//$request_par = [
//    'user_id'=> $test_id[1]['uid'],
//    'offset' => 0,
//    'extended' => 1,
//    'count' => 10,
//    'fields'=>  implode(',', $fields),
//    'access_token'=>'b67f9df284e6a6d059c01f57ffeeae9a55bbe2843847533bd6e57a2d0047139863176f42b93bbdb4a50b0'
//];
//$url = "https://api.vk.com/method/groups.get?" .  http_build_query($request_par);
////$result = json_decode(file_get_contents($url),true);
//echo ($url);
//


//$j = 0;
//  foreach ($result['response'] as $public_id) {
//      $public[$j] = new Node($client);
//      $public[$j]->setProperty('title', $public_id[$j]['title'] )->save();
//      $public[$j]->setProperty('description', $public_id[$j]['description'] )->save();
//      $public[$j]->setProperty('address', $public_id[$j]['address'] )->save();
//      $public[$j]->setProperty('place ', $public_id[$j]['place'] )->save();
//  }
//
// // выше обработка паблика
// // обработка пользователей паблика
//
// $fields = ['connections', 'site','education','contacts','city', 'music' , 'movies' , 'sex' ,'wall_comments', 'relation' , 'schools' , 'personal' , 'tv'];
// $request_par = [
//     'group_id'=> 'ru9gag',
//     'sort' => 'id_asc',
//     'offset' => 0,
//     'count' => 100,
//     'fields'=>  implode(',', $fields),
//     'access_token'=>'b67f9df284e6a6d059c01f57ffeeae9a55bbe2843847533bd6e57a2d0047139863176f42b93bbdb4a50b0' //нужно обновить
// ];
// $url = "https://api.vk.com/method/groups.getMembers?" .  http_build_query($request_par);
// $result = json_decode(file_get_contents($url),true);
// $first_names = array();
// $users_uid = array();
// echo($url);
// //$test_id = $result->response[0]->items[0]->city;
// $i = 0;
// foreach ($result['response'] as $test_id) {
//     $person[$i] = new Node($client);
//     $person[$i]->setProperty('first_name', $test_id[$i]['first_name'] )->save();
//     $person[$i]->setProperty('uid', $test_id[$i]['uid'] )->save();
//     $person[$i]->setProperty('connections', $test_id[$i]['connections'] )->save();
//     $person[$i]->setProperty('site', $test_id[$i]['site'] )->save();
//     $person[$i]->setProperty('education', $test_id[$i]['education'] )->save();
//     $person[$i]->setProperty('contacts', $test_id[$i]['contacts'] )->save();
//     $person[$i]->setProperty('city', $test_id[$i]['city'] )->save();
//     $person[$i]->setProperty('music', $test_id[$i]['music'] )->save();
//     $person[$i]->setProperty('movies', $test_id[$i]['movies'] )->save();
//     $person[$i]->setProperty('sex', $test_id[$i]['sex'] )->save();
//     $person[$i]->setProperty('schools', $test_id[$i]['schools'] )->save();
//     $person[$i]->setProperty('tv', $test_id[$i]['tv'] )->save();
//     $person[$kek]->relateTo($public[$j], 'Following')->save();
//     $i++;
// }



//токен 26fa8e2f14be3ba3389bc7a3d917f4bd4d9c8dd2237a4afe2d99befba5a3a2c804a1a54f845178b0e5e89

?>
