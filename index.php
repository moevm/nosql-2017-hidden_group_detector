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


//token 62730d1ed3005f721d56bb05bb234b6fc9e45dcd66acb7c45f47a6f0a3396a7e8322ca0b0ffe9f2a9aaf4
// начало запроса по группам сообщества
 
 
 $fields = ['connections', 'site','education','contacts','photo_max','status','city'];
 $request_par = [
     'group_id'=> 'eltech4383',
     'sort' => 'id_asc',
     'offset' => 0,
     'count' => 10,
     'fields'=>  implode(',', $fields),
     'access_token'=>'62730d1ed3005f721d56bb05bb234b6fc9e45dcd66acb7c45f47a6f0a3396a7e8322ca0b0ffe9f2a9aaf4'
    ];
 $url = "https://api.vk.com/method/groups.getMembers?" .  http_build_query($request_par);
 $result = json_decode(file_get_contents($url),true);
 $first_names = array();
 //echo($result);
 //$test_id = $result->response[0]->items[0]->city;
 foreach ($result['response'] as $test_id) {
    // foreach ($result['users'] as $test_user) {
     $kek = 0;
     while($kek < 10) {
        $first_names[$kek] = $test_id[$kek]['first_name'];
       
        print_r($test_id[$kek]['first_name'].'<br>');
        $kek++;
     }
     //}
 }
 print_r($first_names);
?>




<?php
require('vendor/autoload.php');

use Everyman\Neo4j\Client,
    Everyman\Neo4j\Transport,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Relationship;

// Connecting to a different port or host

$client = new Client();

// Connecting using HTTPS and Basic Auth

//$client->getTransport()
  //->useHttps()
  //->setAuth('Kek', 'Kek');
//print_r($client->getServerInfo());
//$client->getTransport()->useHttps(FALSE);
 $person = array();
while ($kek >= 0) {
   $person[$kek] = new Node($client);
   $person[$kek]->setProperty('first_name', $first_names[$kek] )->save(); 
   $kek--;  
}
$kek = 0;
while ($kek <= 10) {
   $person[$kek]->relateTo($person[$kek+1], 'KNOWS')->save(); 
   $kek++;
}   
    
//$keanu = new Node($client);
//$keanu->setProperty('name', 'Keanu Reeves')->save();
//$laurence = new Node($client);
//$laurence->setProperty('name', 'Laurence Fishburne')->save();
//$jennifer = new Node($client);
//$jennifer->setProperty('name', 'Jennifer Connelly')->save();
//$kevin = new Node($client);
//$kevin->setProperty('name', 'Kevin Bacon')->save();

//$matrix = new Node($client);
//$matrix->setProperty('title', 'The Matrix')->save();
//$higherLearning = new Node($client);
//$higherLearning->setProperty('title', 'Higher Learning')->save();
//$mysticRiver = new Node($client);
//$mysticRiver->setProperty('title', 'Mystic River')->save();

?>

</body>
</html>