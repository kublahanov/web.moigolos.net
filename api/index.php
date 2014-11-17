<?
/*
 * API для получения данных извне
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
    public $sName, $sDesc, $sURL, $sType;

    /**
     * [__construct description]
     * @param [type] $sName [description]
     * @param [type] $sDesc [description]
     * @param [type] $sURL  [description]
     * @param [type] $sType [description]
     */
    public function __construct($sName, $sDesc, $sURL, $sType)
    {
        $this->sName = $sName;
        $this->sDesc = $sDesc;
        $this->sURL = $sURL;
        $this->sType = $sType;
    }

    /**
     * [getArray description]
     * @return [type] [description]
     */
    public static function getArray()
    {
        return array(
            'name' => $this->sName,
            'desc' => $this->sDesc,
            'url' => $this->sURL,
            'type' => $this->sType
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

    $sQuery = "SELECT * FROM pr_list";
    $dbStat = $dbLink->query($sQuery);
    $dbStat->setFetchMode(PDO::FETCH_ASSOC);

    $arDBData = array();
    while ($arRow = $dbStat->fetch())
    {
        $arDBData[$arRow['name']] = $arRow;
    }

    /*
     * Получаем информацию о проектах из ФС.
     * Сканируем корневую директорию на наличие проектов.
     * Выбираем только папки с .ru и .net.
     */
    $sHomeDir = '/var/www/';
    $arFSData = scandir($sHomeDir);
    foreach ($arFSData as $iKey => $sDir)
    {
        if ((strpos($sDir, '.ru') === false) && (strpos($sDir, '.net') === false))
        {
            unset($arFSData[$iKey]);
            continue;
        }
        $arFSData[$iKey] = ((check2LvlDomain($sDir)) ? 'www.' : '') . $sDir;
    }

    /*
     * Сравниваем данные из БД и из ФС
     */
    foreach ($arFSData as $sDir)
    {
        if (array_key_exists($sDir, $arDBData))
        {
            echo "- найдено: $sDir<br>";
        }
        else
        {
            echo "- не найдено: $sDir<br>";
            echo " Добавляем!<br>";
            // $PROJECT = new myProject($sDir, $sDir, 'http://' . $sDir . '/', 0);
            // $sQuery = "INSERT INTO pr_list (`name`, `desc`, `url`, `type`) values (:name, :desc, :url, :type)";
            // $dbStat = $dbLink->prepare($sQuery);
            // $dbStat->execute($PROJECT::getArray());
        }
    }

    $dbLink = null;
}
catch (PDOException $e)
{
    file_put_contents('PDOErrors.log', $e->getMessage(), FILE_APPEND);
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
        <?foreach ($arFSData as $sDir):?>
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

echo '<pre>';
print_r($arDBData);
print_r($arFSData);
echo '</pre>';

/*
 * Функция возвращает true, если ей передан домен 2-го уровня
 */
function check2LvlDomain($sDomainName)
{
    $arSplit = explode('.', $sDomainName);
    return (count($arSplit) < 3);
}

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
