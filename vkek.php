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
////////ЗАПРОСЫ
/// МЕЛОМАНЫ
/// match (n:User) where n.audios >200 return n;
/// ВИДЕОСОСЫ
/// match (n:User) where n.videos >100 return n;
/// ВЫСОКОГО МНЕНИЯ О СЕБЕ
/// match (n:User) where n.friends is not null and n.followers / n.friends> 0.8 return n
///



                                session_start();
//                                echo $_SESSION['myj'];
//                                echo "<script>console.log('АЗИЗААА');</script>";
                                if ($_SESSION['h'] == 1 ){
                                    echo "<script>console.log('сюда-то зашли ?');</script>";
//                                    echo $_SESSION['hid_group'].'<br>';
                                    if($_SESSION['hid_group'] == "Абоненты Мегафон") {
                                        $query = <<<EOQ
match (n:User) where n.phone =~ '^.*[9](([2][0-9])|([3][0-9])).*$'
Merge (g:HiddenGroup {name: "Пользователи мегафон"})
SET n:Мегафон
CREATE (n)-[r:isProvidedBy]->(g)
return n as id, TYPE(r), g as group;
EOQ;
                                    }
                                    else if($_SESSION['hid_group'] == "Пользователи Android"){
//                                        echo "ЗДЕСЬ".'<br>';
                                        $query = <<<EOQ
match (n:User) where n.device = 4
Merge (g:HiddenGroup {name: "Android"})
SET n:Android
CREATE (n)-[r:Uses]->(g)
return n as id, TYPE(r), g as group;
EOQ;
                                    }
                                    else if ($_SESSION['hid_group'] == "Пользователи Windows"){
                                        $query = <<<EOQ
match (n:User) where n.device in [5,6]
Merge (g:HiddenGroup {name: "Windows"})
SET n:Windows
CREATE (n)-[r:Uses]->(g)
return n as id, TYPE(r), g as group;
EOQ;
                                    }
                                    else if($_SESSION['hid_group'] == "Пользователи iOS"){
//                                        echo "АЙОС";
                                        $query = <<<EOQ
match (n:User) where n.device in [2,3]
Merge (g:HiddenGroup {name: "iOS"})
SET n:iOS
CREATE (n)-[r:Uses]->(g)
return n as id, TYPE(r), g as group;
EOQ;
                                    }
                                    else if($_SESSION['hid_group'] == "Меломаны"){
                                        $query = <<<EOQ
match (n:User) where n.audios >200
Merge (g:HiddenGroup {name: "Меломаны"})
SET n:Music
CREATE (n)-[r:In]->(g)
return n as id, TYPE(r), g as group;
EOQ;
                                    }
                                    else if($_SESSION['hid_group'] == "Видеофилы"){
                                        $query = <<<EOQ
match (n:User) where n.videos >100
Merge (g:HiddenGroup {name: "Видеофилы"})
SET n:Video
CREATE (n)-[r:In]->(g)
return n as id, TYPE(r), g as group;
EOQ;
                                    }
                                    else if($_SESSION['hid_group'] == "Высокого мнения о себе"){
                                        $query = <<<EOQ
match (n:User) where n.friends <> 0 and n.followers / n.friends> 0.8
Merge (g:HiddenGroup {name: "Зазнавшиеся"})
SET n:ЧСВ
CREATE (n)-[r:In]->(g)
return n as id, TYPE(r), g as group;
EOQ;
                                    }
                                    //, count(n) as number


                                    $hidgroup =  $client->run($query); //(array)
//                                    echo "<script>console.log('\" . $hidgroup . \"');</script>";

                                    echo "<script>console.log('что ?');</script>";

                                    $count = $client->run("match (n:User)-[r]->(g:HiddenGroup) return count(n) as number;");

                                    echo "<script>console.log('ВАТ ?');</script>";
//                                        echo var_dump($count).'СЮДА СМОТРЕТЬ'.'<br>';
//                                        echo "ТЕПЕРЬ ЗДЕСЬ".'<br>';
//                                        var_dump($hidgroup);


                                    $graph_array = ['nodes' => [], 'edges' => []];
                                    $i = 0;
                                    foreach ($hidgroup->records() as $record) {
                                            if ($i == 50) break;

                                        //добавление узла
//                                            echo "сука".'<br>';
                                        $graph_array['nodes'][$i]['last_name'] = $record->getByIndex(0)->get('last_name');
//                                            echo $graph_array['nodes'][$i]['last_name'].'<br>';

                                        $graph_array['nodes'][$i]['phone'] = $record->getByIndex(0)->get('phone');
//                                            echo $graph_array['nodes'][$i]['phone'].'<br>';

                                        $graph_array['nodes'][$i]['first_name'] = $record->getByIndex(0)->get('first_name');
//                                            echo $graph_array['nodes'][$i]['first_name'] .'<br>';

//                                            echo "KEK";
//                                            $arr_eu = (array)($record->getByIndex(0));
//                                            var_dump($arr_eu["properties"]["bdate"]);
//                                            var_dump(array_key_exists('bdate', $arr_eu['properties']));
//                                            echo "САНЫЧ";
//                                            var_dump($record->getByIndex(0)->get('bdate'));
//                                            var_dump(property_exists($record->getByIndex(0),'bdate'));
//                                        var_dump(isset($record->getByIndex(0)->get('bdate')));
//                                            echo "САСАНЫЧ";

                                        $graph_array['nodes'][$i]['bdate'] = $record->getByIndex(0)->get('bdate');
//                                            echo $graph_array['nodes'][$i]['bdate'].'<br>';

                                        $graph_array['nodes'][$i]['id'] = $record->getByIndex(0)->get('uid');
//                                        echo $graph_array['nodes'][$i]['id'].'<br>';

                                        //НОВОЕ
                                        $graph_array['nodes'][$i]['screen_name'] = $record->getByIndex(0)->get('screen_name');
//                                        echo $graph_array['nodes'][$i]['id'].'<br>';

                                        $graph_array['nodes'][$i]['caption'] = $graph_array['nodes'][$i]['last_name'];
//                                        echo $graph_array['nodes'][$i]['caption'].'<br>';

                                        $graph_array['nodes'][$i]['role'] = 'user';
//                                        echo $graph_array['nodes'][$i]['caption'].'<br>';

                                        //Добавление связей
                                        $graph_array['edges'][$i]['source'] = $graph_array['nodes'][$i]['id'];
                                        //echo $graph_array['edges'][$i]['source'].'<br>';

                                        $graph_array['edges'][$i]['target'] = 0;//можно написать id == 0, если нужна цифра
                                        //echo $graph_array['edges'][$i]['target'].'<br>';

                                        $graph_array['edges'][$i]['caption'] = $record->getByIndex(1);
                                        //echo $graph_array['edges'][$i]['caption'].'<br>';

                                        $i++;

//                                                        $str = $username['bdate'];
                                        echo "<script>console.log('\".$i.\"');</script>";
                                    }

                                    if($count->firstRecord()->get('number') != 0){

                                        //добавление группы как узла
                                        $graph_array['nodes'][$i]['id'] = 0;
                                        // echo $graph_array['nodes'][$i]['id'].'<br>';
                                        $graph_array['nodes'][$i]['name'] = $record->getByIndex(2)->get('name');
                                        //echo $graph_array['nodes'][$i]['name'].'<br>';

//                                        echo $count->firstRecord()->get('number').'<br>';

                                        $graph_array['nodes'][$i]['caption'] = $graph_array['nodes'][$i]['name'];
                                        //echo $graph_array['nodes'][$i]['caption'].'<br>';

                                        $graph_array['nodes'][$i]['role'] = 'hGroup';

//                                        echo $count->firstRecord()->get('number');
                                        $graph_array['nodes'][$i]['membNumb'] = $count->firstRecord()->get('number');
                                        //echo $graph_array['nodes'][$i]['caption'].'<br>';
                                        $i = 0;

                                        //echo "Весь ассоциативный массив в json".'<br>';
                                        //var_dump($graph_array);

                                        $json_graph_array = json_encode($graph_array, JSON_UNESCAPED_UNICODE);


                                    }
//                                        echo $json_graph_array.'<br>';

                                    $_SESSION['h'] = 0;
                                }

?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>

    <!-- Basic Page Needs
  ================================================== -->
    <meta charset="utf-8">
    <title>zVintauge - Free Html5 Templates</title>

    <!-- Mobile Specific Metas
	================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
	================================================== -->
    <link rel="stylesheet" type="text/css" href="dependencies/styles/vendor.css">
    <link rel="stylesheet" href="dependencies/alchemy.css">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="css/zerogrid.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/menu.css">

    <script type="text/javascript" src="//vk.com/js/api/openapi.js?150"></script>


    <script type="text/javascript" src="dependencies/scripts/vendor.js"></script>
    <script type="text/javascript" src="dependencies/alchemy.js"></script>

</head>

<body class="home-page">


<div class="wrap-body">
    <header class="">
        <div class="logo">
            <a href="#">Hidden group detector</a>
            <span>VKeK app</span>
        </div>
    </header>
    <!--////////////////////////////////////Container-->
    <section id="container">
        <div class="wrap-container">
            <!-----------------content-box-1-------------------->
            <section class="content-box box-1">
                <div class="zerogrid">
                    <div class="wrap-box"><!--Start Box-->
                        <div class="box-header">
                            <h2>Ввод пользователем данных</h2>
                        </div>
                        <!--<div >-->
                            <!--<form name="test" method="post" action="input1.php">-->

                        <div id="container_new">
                            <div id="sidebar">
                                <div class="logo">
                                <span>Ввод id человека или группы</span><br>
                                </div>
                                <form name="test" method="post" action="index.php">
                                <div >

                                        <input type="text" name="vkid" size="10">

                                </div>
                                <div>
                                    <div class="logo">
                                    <span>Выбор конкретной скрытой группы</span> <br>
                                    </div>
                                    <select name="hidden_name">
                                        <option><span class="cool_words">Меломаны</span></option>
                                        <option><span class="cool_words">Видеофилы</span></option>
                                        <option><span class="cool_words">Высокого мнения о себе</span></option>
                                        <option><span class="cool_words">Абоненты Мегафон</span></option>
                                        <option><span class="cool_words">Пользователи Android</span></option>
                                        <option><span class="cool_words">Пользователи Windows</span></option>
                                        <option><span class="cool_words">Пользователи iOS</span></option>
                                    </select>
                                </div>

<!--                                    <br>-->
<!--                                    <div class="logo">-->
<!--                                        <span>Выбор параметров пользователем</span>-->
<!--                                    </div>-->
<!--                                    <div class="cool_words" id="no-center">-->
<!--                                     <label>-->
<!--                                        <input class="checkbox" type="checkbox" name="checkbox-test1">-->
<!--                                        <span class="checkbox-custom"></span>-->
<!--                                        <span class="label">Пол</span>-->
<!--                                     </label>-->
<!--                                    <label>-->
<!--                                        <input class="checkbox" type="checkbox" name="checkbox-test2">-->
<!--                                        <span class="checkbox-custom"></span>-->
<!--                                        <span class="label">Возраст</span>-->
<!--                                    </label>-->
<!--                                    <label>-->
<!--                                        <input class="checkbox" type="checkbox" name="checkbox-test3">-->
<!--                                        <span class="checkbox-custom"></span>-->
<!--                                        <span class="label">Сообщества?</span>-->
<!--                                    </label>-->
<!--                                    <label>-->
<!--                                        <input class="checkbox" type="checkbox" name="checkbox-test4">-->
<!--                                        <span class="checkbox-custom"></span>-->
<!--                                        <span class="label">Аудио?</span>-->
<!--                                    </label>-->
<!--                                    </div>-->
                                <div>
                                    <button > <span class="cool_words">VKeK</span></button>
                                </div>
                                </form>
                            </div>
                            <div id="content">
                                <div class="logo">
                                <span>Вывод скрытой группы</span>
                                </div>

                                <!--<script type="text/javascript">-->
                                    <!--VK.init({apiId: 6199555});-->
                                <!--</script>-->

                                <!--&lt;!&ndash; VK Widget &ndash;&gt;-->
                                <!--<div id="vk_auth"></div>-->
                                <!--<script type="text/javascript">-->
                                    <!--VK.Widgets.Auth("vk_auth", {"authUrl":"/dev/Login"});-->
                                <!--</script>-->
                                <div class="alchemy" id="alchemy"></div>

                                <script type="text/javascript" src="dependencies/alchemy.min.js"></script>
                                <script type="text/javascript">

                                    // graph configuration
                                    var config = {

                                        dataSource: <?php echo $json_graph_array?>,

                                        forceLocked: false,
                                        nodeCaption: "caption",
                                        nodeCaptionsOnByDefault: true,
                                        backgroundColor: "#ffffff",
                                        zoomControls: true,

                                        graphHeight: function(){ return 600; },
                                        graphWidth: function(){ return 600; },

                                        nodeTypes: {"role": ["hGroup", "user"]},

                                        nodeStyle: {  // СТИЛИЗАЦИЯ УЗЛОВ
                                            "hGroup": {
                                                color: "#00ff00",
                                                radius: 20,
                                                borderWidth: 2
                                            },
                                            "user":{
                                                color:"#551a8b",
                                                radius: 10,
                                                borderWidth: 1
                                            }
                                        },

                                        "nodeClick": function(node) {
                                            if (node._nodeType == "user") {
                                                document.getElementById('userName').innerHTML = node._properties.first_name + " " + node._properties.last_name;
                                                document.getElementById('bdate').innerHTML = node._properties.bdate;
                                                document.getElementById('phone').innerHTML = node._properties.phone;
                                                document.getElementById('vk_add').href = "https://vk.com/" + node._properties.screen_name; //+ node._properties.screen_name;
                                                document.getElementById('vk_add').innerHTML = node._properties.first_name;

                                                document.getElementById('innerUserInfo').style.display = "inline-block";
                                                document.getElementById('innerGroupInfo').style.display = "none";
                                                document.getElementById('nodeNoneInfo').style.display = "none";

                                            } else {
                                                document.getElementById('groupName').innerHTML = node._properties.name;
                                                document.getElementById('membNumb').innerHTML = node._properties.membNumb;

                                                document.getElementById('innerUserInfo').style.display = "none";
                                                document.getElementById('innerGroupInfo').style.display = "inline-block";
                                                document.getElementById('nodeNoneInfo').style.display = "none";
                                            }

                                        }
                                    }
                                    alchemy = new Alchemy(config)
                                </script>

                            </div>

                        </div>
                        <!--</div>-->
                        <div class="box-content">
                            <div id="nodeInfo">
                                <h3><i>Дополнительные сведения</i> </h3>
                                <font id="nodeNoneInfo">Выберите узел, чтобы отобразить информацию</font>
                                <div id="innerUserInfo">
                                    <font class="infoUnits">Пользователь: </font><font id="userName" class="infoContain"></font><br>
                                    <font class="infoUnits">Дата рождения: </font><font id="bdate"class="infoContain"></font><br>
                                    <font class="infoUnits">Телефон: </font><font id="phone"class="infoContain"></font><br>
                                    <font class="infoUnits">Vk: </font><a id="vk_add"class="infoContain"></a><br>
                                </div>
                                <div id="innerGroupInfo">
                                    <font class="infoUnits">Скрытая группа: </font><font id="groupName" class="infoContain"></font><br>
                                    <font class="infoUnits">Количество членов: </font><font id="membNumb" class="infoContain"></font><br>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </section>
</div>
</body>
</html>