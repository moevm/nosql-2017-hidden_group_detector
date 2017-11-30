<!DOCTYPE html>
<html>
<body>

<h1>Neo4JKEK</h1>

<?php
//запрос по API к vk
$permissions = [
  'notify','friends','photos','audio','video','docs','pages','status','wall',
    'groups','messages','email','stats'
    
];
$request_params = [
    'client_id' => '6199555',
    'redirect_url' => 'https://oauth.vk.com/blank.html',
    'response_type' => 'token',
    'display' => 'page',
    'scope' => implode(',', $permissions)

    
];
 $url = 'https://oauth.vk.com/authorize?' .  http_build_query($request_params);  

 //echo $url;
//b67f9df284e6a6d059c01f57ffeeae9a55bbe2843847533bd6e57a2d0047139863176f42b93bbdb4a50b0

//token 62730d1ed3005f721d56bb05bb234b6fc9e45dcd66acb7c45f47a6f0a3396a7e8322ca0b0ffe9f2a9aaf4
//token db9ae4150e539bf7c93d9e613c7d31b53bad93528b7effea6acf74d5d905fada0271b76da711e551a01e3

// начало запроса по группам сообщества
 
 
 $fields = ['connections', 'site','education','contacts','photo_max','status','city', 'music' , 'movies' , 'sex' ,'wall_comments', 'relation' , 'schools' , 'personal' , 'tv'];
 $request_par = [
     'group_id'=> 'eltech4383',
     'sort' => 'id_asc',
     'offset' => 0,
     'count' => 12,
     'fields'=>  implode(',', $fields),
     'access_token'=>'b67f9df284e6a6d059c01f57ffeeae9a55bbe2843847533bd6e57a2d0047139863176f42b93bbdb4a50b0'
    ];
 $url = "https://api.vk.com/method/groups.getMembers?" .  http_build_query($request_par);
 $result = json_decode(file_get_contents($url),true);
 $first_names = array();
 $users_uid = array();
 echo($url);
 //$test_id = $result->response[0]->items[0]->city;

 foreach ($result['response'] as $test_id) {
    // foreach ($result['users'] as $test_user) {
     $kek = 0;
     while($kek < 12) {
        $first_names[$kek] = $test_id[$kek]['first_name'];
        $users_uid[$kek] = $test_id[$kek]['uid'];
        print_r($test_id[$kek]['first_name'].'<br>');
        $kek++;
     }
     //}
 }
 
 print_r($first_names);
 
 
 
 
 //Запрос на обработку друзей пользователя
 
 $fields = ['connections', 'site','education','contacts','photo_max','status','city', 'music' , 'movies' , 'sex' ,'wall_comments', 'relation' , 'schools' , 'personal' , 'tv'];
 $request_par = [
     'user_id'=> $test_id[1]['uid'],
     'order' =>  'name',
     'offset' => 0,
     'count' => 10,
     'fields'=>  implode(',', $fields),
     'access_token'=>'b67f9df284e6a6d059c01f57ffeeae9a55bbe2843847533bd6e57a2d0047139863176f42b93bbdb4a50b0'
    ];
 $url = "https://api.vk.com/method/friends.get?" .  http_build_query($request_par);
 //$result = json_decode(file_get_contents($url),true);
 //echo ($url);
 
 //Запрос на обработку сообществ пользователя
 $fields = ['id', 'name', 'city', 'country', 'place', 'description', 'wiki_page', 'members_count', 'counters', 'start_date', 'finish_date', 'can_post', 'can_see_all_posts', 'activity', 'status', 'contacts', 'links', 'fixed_post', 'verified', 'site', 'can_create_topic'];
 $request_par = [
     'user_id'=> $test_id[1]['uid'],
     'offset' => 0,
     'extended' => 1,
     'count' => 10,
     'fields'=>  implode(',', $fields),
     'access_token'=>'b67f9df284e6a6d059c01f57ffeeae9a55bbe2843847533bd6e57a2d0047139863176f42b93bbdb4a50b0'
    ];
 $url = "https://api.vk.com/method/groups.get?" .  http_build_query($request_par);
 //$result = json_decode(file_get_contents($url),true);
 echo ($url);
 
 
 
 
//Тут пытаемся найти связи между пользователями:
 
 $fields = ['connections', 'site','education','contacts','photo_max','status','city'];
 $request_par = [
     'user_id'=> '9762442',
     'order' => 'name',
     'count' => 500,
     'offset' => 0,
     'fields'=> 'city',
     //implode(',', $fields)
     //сервисный ключ доступа 84585b7184585b7184585b710a8406c2728845884585b71dd938add4bfce82a9edeca42
     'service_token'=>'84585b7184585b7184585b710a8406c2728845884585b71dd938add4bfce82a9edeca42' //Возможно не обязательно
    ];
 $url = "https://api.vk.com/method/friends.get?" .  http_build_query($request_par);
 $result = json_decode(file_get_contents($url),true);
 //echo($url);
 echo('WORK MAZAFAKA');
 
 // foreach ($result['response'] as $test_uid) {
    // foreach ($result['users'] as $test_user) {
     $kei = 0;
     while($kei < $request_par['count']) {
      //  $first_names[$kek] = $test_uid[$kek]['first_name'];
        //$users_uid[$kek] = $test_uid[$kek]['user_id'];
         $killa = 0;
         while ($killa < 12) {
            if ($result['response'][$kei]['user_id'] == $users_uid[$killa])
             {echo ($result['response'][$kei]['user_id'].'<br>');}
          $killa++;
         }
        $kei++;
     }
 // }
  //echo($users_uid[1]);
//////////////// До этого момента это были тесты, теперь пойдёт Жара
require('vendor/autoload.php');

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Transport,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Relationship;

// Connecting to a different port or host

$client = new Client();


 $person = array();
 $public = array();
 $request_par = [
     'group_id'=> 'ru9gag'
     'access_token'=>'b67f9df284e6a6d059c01f57ffeeae9a55bbe2843847533bd6e57a2d0047139863176f42b93bbdb4a50b0' //нужно обновить
    ];
 $url = "https://api.vk.com/method/groups.getSettings?" .  http_build_query($request_par);
 $result = json_decode(file_get_contents($url),true);
 
 $j = 0;
  foreach ($result['response'] as $public_id) {
     $public[$j] = new Node($client);
     $public[$j]->setProperty('title', $public_id[$j]['title'] )->save(); 
     $public[$j]->setProperty('description', $public_id[$j]['description'] )->save();
	 $public[$j]->setProperty('address', $public_id[$j]['address'] )->save();
	 $public[$j]->setProperty('place ', $public_id[$j]['place'] )->save();
  }
 
 // выше обработка паблика
 // обработка пользователей паблика 
 
 $fields = ['connections', 'site','education','contacts','city', 'music' , 'movies' , 'sex' ,'wall_comments', 'relation' , 'schools' , 'personal' , 'tv'];
 $request_par = [
     'group_id'=> 'ru9gag',
     'sort' => 'id_asc',
     'offset' => 0,
     'count' => 100,
     'fields'=>  implode(',', $fields),
     'access_token'=>'b67f9df284e6a6d059c01f57ffeeae9a55bbe2843847533bd6e57a2d0047139863176f42b93bbdb4a50b0' //нужно обновить
    ];
 $url = "https://api.vk.com/method/groups.getMembers?" .  http_build_query($request_par);
 $result = json_decode(file_get_contents($url),true);
 $first_names = array();
 $users_uid = array();
 echo($url);
 //$test_id = $result->response[0]->items[0]->city;
 $i = 0;
 foreach ($result['response'] as $test_id) {
	    $person[$i] = new Node($client);
		$person[$i]->setProperty('first_name', $test_id[$i]['first_name'] )->save(); 
		$person[$i]->setProperty('uid', $test_id[$i]['uid'] )->save();
		$person[$i]->setProperty('connections', $test_id[$i]['connections'] )->save();
		$person[$i]->setProperty('site', $test_id[$i]['site'] )->save();
		$person[$i]->setProperty('education', $test_id[$i]['education'] )->save();
		$person[$i]->setProperty('contacts', $test_id[$i]['contacts'] )->save();
	    $person[$i]->setProperty('city', $test_id[$i]['city'] )->save();
		$person[$i]->setProperty('music', $test_id[$i]['music'] )->save();
		$person[$i]->setProperty('movies', $test_id[$i]['movies'] )->save();
		$person[$i]->setProperty('sex', $test_id[$i]['sex'] )->save();
		$person[$i]->setProperty('schools', $test_id[$i]['schools'] )->save();
		$person[$i]->setProperty('tv', $test_id[$i]['tv'] )->save();
		$person[$kek]->relateTo($public[$j], 'Following')->save(); 
        $i++;
 }
 /*
while ($kek >= 0) {
   $person[$kek] = new Node($client);
   $person[$kek]->setProperty('first_name', $first_names[$kek] )->save(); 
   $kek--;  
}
$kek = 0;

while ($kek <= 12) {
   $person[$kek]->relateTo($person[$kek+1], 'FRIENDS')->save(); 
   $kek++;
}   
    */

?>

</body>
</html>