<?
/*
 * API для получения данных извне
 * http://web.moigolos.net/pladm/phpliteadmin.php
 */
if (count($_GET) < 1)
{
    // die();
}

/**
 * Класс с полями для БД pr_list
 */
class myProject
{
    public static $sName, $sDesc, $sURL, $sType;

    /**
     * [__construct description]
     * @param [type] $sName [description]
     * @param [type] $sDesc [description]
     * @param [type] $sURL  [description]
     * @param [type] $sType [description]
     */
    public function __construct($sName, $sDesc, $sURL, $sType)
    {
        self::$sName = $sName;
        self::$sDesc = $sDesc;
        self::$sURL = $sURL;
        self::$sType = $sType;
    }

    /**
     * [getArray description]
     * @return [type] [description]
     */
    public static function getArray()
    {
        return array(
            'name' => self::$sName,
            'desc' => self::$sDesc,
            'url' => self::$sURL,
            'type' => self::$sType
        );
    }
}

try
{
    /*
     * Получаем информацию о проектах сохранённую в БД
     */
    $dbFile = dirname(__FILE__) . '/projects.db';
    $dbLink = new PDO('sqlite:' . $dbFile);
    $dbLink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Список проектов
    $sQuery = "SELECT * FROM pr_list";
    $dbStat = $dbLink->query($sQuery);
    $dbStat->setFetchMode(PDO::FETCH_ASSOC);
    $arDBProjects = array();
    while ($arRow = $dbStat->fetch())
    {
        $arRow['checked'] = 0;
        $arDBProjects[$arRow['name']] = $arRow;
    }

    // Список типов проектов
    $sQuery = "SELECT * FROM pr_type ORDER BY sort ASC";
    $dbStat = $dbLink->query($sQuery);
    $dbStat->setFetchMode(PDO::FETCH_ASSOC);
    $arDBTypesProjects = array();
    while ($arRow = $dbStat->fetch())
    {
        $arDBTypes[$arRow['id']] = $arRow;
        $arRow['projects'] = array();
        foreach ($arDBProjects as $iProjectsKey => $arProjectData)
        {
            if ($arRow['id'] == $arProjectData['type'])
            {
                $arDBProjects[$iProjectsKey]['checked'] = 1;
                $arProjectData['checked'] = 1;
                $arRow['projects'][] = $arProjectData;
            }
        }
        $arDBTypesProjects[$arRow['id']] = $arRow;
    }

    // Дополняем список нераспределёнными проектами
    foreach ($arDBProjects as $iProjectsKey => $arProjectData)
    {
        if ($arProjectData['checked'] === 0)
        {
            $arDBTypesProjects[0]['projects'][] = $arProjectData;
            // $arProjectData[$iProjectsKey]['checked'] = 1;
        }
    }

    /*
     * Получаем информацию о проектах из ФС.
     * Сканируем корневую директорию на наличие проектов.
     * Выбираем только папки с .ru и .net.
     */
    $sHomeDir = '/var/www/';
    $arFSProjects = scandir($sHomeDir);
    foreach ($arFSProjects as $iKey => $sDir)
    {
        if ((strpos($sDir, '.ru') === false) && (strpos($sDir, '.net') === false))
        {
            unset($arFSProjects[$iKey]);
            continue;
        }
        $arFSProjects[$iKey] = ((check2LvlDomain($sDir)) ? 'www.' : '') . $sDir;
    }

    /*
     * Сравниваем данные из БД и из ФС
     */
    foreach ($arFSProjects as $sDir)
    {
        if (array_key_exists($sDir, $arDBProjects))
        {
            // echo "- найдено: $sDir<br>";
        }
        else
        {
            // echo "- не найдено: $sDir<br>";
            echo " Добавлено: $sDir<br>";
            $PROJECT = new myProject($sDir, $sDir, 'http://' . $sDir . '/', 0);
            $sQuery = "INSERT INTO pr_list (`name`, `desc`, `url`, `type`) values (:name, :desc, :url, :type)";
            $dbStat = $dbLink->prepare($sQuery);
            $dbStat->execute($PROJECT::getArray());
        }
    }

    $dbLink = null;
}
catch (PDOException $e)
{
    file_put_contents('PDOErrors.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
    echo $e->getMessage();
}

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="author" content="Веб-студия «Мой голос»">
    <meta name="description" content="Веб-студия «Мой голос»">
    <meta name="keywords" content="веб, Веб-студия, разработка, php, html5, css3">
    <meta name='yandex-verification' content='7b06ca2324c21a0d' />
    <link href="/css/style.css" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="/favicon.ico">
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <title>Веб-студия «Мой голос» - Все проекты</title>
</head>
<body>
    <div class="projects-container">
        <h1>Все проекты</h1>
        <?foreach ($arDBTypesProjects as $arType):?>
            <div class="project-type">
                <h2><?=$arType['name']?></h2>
                <?if (count($arType['projects']) < 1):?>
                    <div class="empty">нет</div>
                <?else:?>
                    <ul class="projects-list">
                    <?foreach ($arType['projects'] as $arProject):?>
                        <li>
                            <?=$arProject['name']?> <a href="<?=$arProject['url']?>">&#963;</a>
                        </li>
                    <?endforeach;?>
                    </ul>
                <?endif;?>
            </div>
        <?endforeach;?>

        <footer class="not-show-in-test">
            <div>
                <div class="copyrights centered">
                    &#169; 2007-2014 <a class="comment" href="http://web.moigolos.net">Веб-студия</a> «Мой голос».&nbsp;&nbsp;
                    Поддержка проекта <a class="comment" href="http://www.moigolos.net">"Мой голос"</a>.&nbsp;&nbsp;
                    Верстаем <a href="http://validator.w3.org/check?uri=http://web.moigolos.net/allprojects/">W3C-кошерно</a> :)
                </div>
            </div>

            <!-- Yandex.Metrika informer -->
            <div style="position: absolute; top: 13px; right: 20px;">
                <a href="https://metrika.yandex.ru/stat/?id=27041591&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/27041591/1_0_FFFFFFFF_EFEFEFFF_0_uniques" alt="Яндекс.Метрика" title="Яндекс.Метрика: данные за сегодня (уникальные посетители)" /></a>
            </div>
            <!-- /Yandex.Metrika informer -->

            <!-- Yandex.Metrika counter -->
            <script type="text/javascript">
                (function (d, w, c) {
                    (w[c] = w[c] || []).push(function() {
                        try {
                            w.yaCounter27041591 = new Ya.Metrika({
                                id:27041591,
                                webvisor:true
                            });
                        } catch(e) { }
                    });

                    var n = d.getElementsByTagName("script")[0],
                        s = d.createElement("script"),
                        f = function () { n.parentNode.insertBefore(s, n); };
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

                    if (w.opera == "[object Opera]") {
                        d.addEventListener("DOMContentLoaded", f, false);
                    } else { f(); }
                })(document, window, "yandex_metrika_callbacks");
            </script>
            <noscript><div><img src="//mc.yandex.ru/watch/27041591" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
            <!-- /Yandex.Metrika counter -->

        </footer>

    </div>
</body>
</html>
<?

/**
 * Функция возвращает true, если ей передан домен 2-го уровня
 * @param  [type] $sDomainName [description]
 * @return [type]              [description]
 */
function check2LvlDomain($sDomainName)
{
    $arSplit = explode('.', $sDomainName);
    return (count($arSplit) < 3);
}

// echo '<pre>';
// print_r($arDBTypes);
// print_r($arDBProjects);
// print_r($arFSProjects);
// print_r($arDBTypesProjects);
// echo '</pre>';

// $arData = array(
//     'name' => $sDir,
//     'desc' => $sDir,
//     'url' => 'http://' . $sDir . '/',
//     'type' => 0
// );

// $dbLink->prepare('DELECT name FROM people')->execute();

// $sqlFile = dirname(__FILE__) . '/schema.sqlite.sql';
// @unlink($dbFile);
// var_dump($dbLink);
// echo '<pre>';
// print_r(PDO::getAvailableDrivers());
// echo '</pre>';
// $sqls = file_get_contents($sqlFile);
// foreach (explode(';', $sqls) as $sql)
// {
//     if (trim($sql) !== '')
//     {
//         $db->exec($sql);
//     }
// }
// http://yii.achievment.ru/pladm/phpliteadmin.php?action=row_view&table=tbl_tag
