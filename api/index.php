<?
/*
 * API для получения данных извне
 */
if (count($_GET) < 1)
{
    die();
}

/*
 * Сканиреум корневую директорию на наличие проектов.
 * Выбираем только папки с .ru и .net
 */
if ($_GET['projects'] && $_GET['projects'] == 'all')
{
    $sHomeDir = '/var/www/';
    $arDirData = scandir($sHomeDir);
    foreach ($arDirData as $iKey => $sDir)
    {
        if ((strpos($sDir, '.ru') === false) && (strpos($sDir, '.net') === false))
        {
            unset($arDirData[$iKey]);
        }
    }
}

if ($_GET['create_db'] && $_GET['create_db'] == 'sqlite')
{
    try
    {
        $dbFile = dirname(__FILE__) . '/base.db';
        $dbLink = new PDO('sqlite:' . $dbFile);
        $dbLink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $dbLink->prepare('DELECT name FROM people')->execute();

        $dbLink = null;
    }  
    catch (PDOException $e)
    {
        file_put_contents('PDOErrors.txt', $e->getMessage(), FILE_APPEND);
        echo $e->getMessage();
    }
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
    <script type="text/javascript">
        $(document).ready(function () {
            $("div.container > header > div > figure > img").click(function(){
                window.location = "/api/?projects=all";
            });
        });
    </script>
    <title>Веб-студия «Мой голос» - Все проекты</title>
</head>
<body>
    <div class="container">
        <h1>Все проекты</h1>
        <ul class="all-projects">
        <?foreach ($arDirData as $sDir):?>
            <li>
                <? $sDir = ((check2LvlDomain($sDir)) ? 'www.' : '') . $sDir; ?>
                <?=$sDir?>
                <a href="http://<?=$sDir?>/">&#963</a>
            </li>
        <?endforeach;?>
        </ul>
    </div>
</body>
</html>
<?

/*
 * Функция возвращает true, если ей передан домен 2-го уровня
 */
function check2LvlDomain($sDomainName)
{
    $arSplit = explode('.', $sDomainName);
    return (count($arSplit) < 3);
}

// echo '<pre>';
// print_r($arDirData);
// echo '</pre>';

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
