<?

/*
 * Страница отображения всех проектов
 */

require($_SERVER['DOCUMENT_ROOT'] . '/libs/global.lib.php');
require($_SERVER['DOCUMENT_ROOT'] . '/libs/allprojects.lib.php');
require($_SERVER['DOCUMENT_ROOT'] . '/libs/template.lib.php');

define ('ALL_PROJECTS_CONFIG_PATH', $_SERVER['DOCUMENT_ROOT'] . 'ts/');
define ('ALL_PROJECTS_CACHETIMER_NAME', 'allprojects');
define ('ALL_PROJECTS_CACHE_TIME', 60 * 60 * 24);
define ('CLEAR_CACHE_GET_PARAMETER_NAME', 'clear_cache');
define ('PROJECTS_DB_FILE_NAME', $_SERVER['DOCUMENT_ROOT'] . 'db/projects.db');
define ('PROJECTS_HOME_DIR', '/var/www/');
define ('PDO_ERROR_LOG_NAME', $_SERVER['DOCUMENT_ROOT'] . 'log/PDOErrors.log');
define ('ENABLE_AUTO_PROJECT_ACTIVATION', true);
define ('ALL_PROJECTS_LIST_TABLE_NAME', 'projects_list');
define ('ALL_PROJECTS_TYPE_TABLE_NAME', 'projects_type');

/*
 * Устанавливаем время кеширования (сек) для данных о проектах
 * получаемых из ФС
 */
myCacheTimer::setConfig(ALL_PROJECTS_CONFIG_PATH);
myCacheTimer::addCacheTimer(ALL_PROJECTS_CACHETIMER_NAME, ALL_PROJECTS_CACHE_TIME);

/*
 * Системное сообщение
 */
$sMessage = '';

try
{
    /*
     * Получаем информацию о проектах сохранённую в БД
     */
    $dbFile = PROJECTS_DB_FILE_NAME;
    $dbLink = new PDO('sqlite:' . $dbFile);
    $dbLink->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /*
     * Список проектов
     */
    $sQuery = "SELECT * FROM " . ALL_PROJECTS_LIST_TABLE_NAME . " ORDER BY sort ASC";
    $dbStat = $dbLink->query($sQuery);
    $dbStat->setFetchMode(PDO::FETCH_ASSOC);
    $arDBProjects = array();
    while ($arRow = $dbStat->fetch())
    {
        $arRow['checked'] = 0;
        $arDBProjects[$arRow['name']] = $arRow;
    }

    /*
     * Список типов проектов
     */
    $sQuery = "SELECT * FROM " . ALL_PROJECTS_TYPE_TABLE_NAME . " WHERE `active` = 1 ORDER BY sort ASC";
    $dbStat = $dbLink->query($sQuery);
    $dbStat->setFetchMode(PDO::FETCH_ASSOC);
    $arDBTypes = array();
    $arDBTypesProjects = array();
    while ($arRow = $dbStat->fetch())
    {
        $arDBTypes[$arRow['id']] = $arRow;
        $arRow['projects'] = array();
        foreach ($arDBProjects as $iProjectsKey => $arProjectData)
        {
            if (($arRow['id'] == $arProjectData['type']) && ($arProjectData['active'] == 1))
            {
                $arDBProjects[$iProjectsKey]['checked'] = 1;
                $arProjectData['checked'] = 1;
                $arRow['projects'][] = $arProjectData;
            }
        }
        $arDBTypesProjects[$arRow['id']] = $arRow;
    }

    /*
     * Дополняем список нераспределёнными проектами
     */
    foreach ($arDBProjects as $iProjectsKey => $arProjectData)
    {
        if (($arProjectData['checked'] === 0) && ($arProjectData['active'] == 1))
        {
            $arDBTypesProjects[0]['projects'][] = $arProjectData;
        }
    }

    /*
     * Если истекло время кеширования или кеш очищается принудительно -
     * обновляем данные о проектах
     */
    if (myCacheTimer::checkTimeIsOver(ALL_PROJECTS_CACHETIMER_NAME) || isset($_GET[CLEAR_CACHE_GET_PARAMETER_NAME]))
    {
        /*
         * Получаем информацию о проектах из ФС.
         * Сканируем корневую директорию на наличие проектов.
         * Выбираем только папки с .ru и .net.
         */
        $sHomeDir = PROJECTS_HOME_DIR;
        $arFSProjects = scandir($sHomeDir);
        foreach ($arFSProjects as $iKey => $sDir)
        {
            $arMatches = array();
            $sPattern = '/(\.ru$|\.com$|\.net$)/';
            preg_match($sPattern, $sDir, $arMatches);
            if (count($arMatches) < 1)
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
            /*
             * Если проект отсутствует в БД, но есть в ФС -
             * добавляем его в нераспределённые
             */
            if (!array_key_exists($sDir, $arDBProjects))
            {
                $sMessage .= " Добавлено: $sDir<br>";
                $PROJECT = new myProject($sDir, $sDir, 'http://' . $sDir . '/', 0);
                $sQuery = "INSERT INTO " . ALL_PROJECTS_LIST_TABLE_NAME . " (`name`, `desc`, `url`, `type`) values (:name, :desc, :url, :type)";
                $dbStat = $dbLink->prepare($sQuery);
                $dbStat->execute($PROJECT::getArray());
            }

            /*
             * Если проект в БД не активен, но есть в ФС -
             * активируем его
             */
            else
            {
                if (ENABLE_AUTO_PROJECT_ACTIVATION && ($arDBProjects[$sDir]['active'] == 0))
                {
                    $sMessage .= " Активировано: $sDir<br>";
                    $sQuery = "UPDATE " . ALL_PROJECTS_LIST_TABLE_NAME . " SET `active` = 1 WHERE name = '$sDir'";
                    $dbStat = $dbLink->prepare($sQuery);
                    $dbStat->execute();
                }
            }
        }

        /*
         * Если проект есть в БД, но отсутствует в ФС -
         * деактивируем его
         */
        foreach ($arDBProjects as $arDBProject)
        {
            $sDir = $arDBProject['name'];
            if (!in_array($sDir, $arFSProjects))
            {
                $sMessage .= " Деактивировано: $sDir<br>";
                $sQuery = "UPDATE " . ALL_PROJECTS_LIST_TABLE_NAME . " SET `active` = 0 WHERE name = '$sDir'";
                $dbStat = $dbLink->prepare($sQuery);
                $dbStat->execute();
            }
        }
    }

    $dbLink = null;
}
catch (PDOException $e)
{
    file_put_contents(PDO_ERROR_LOG_NAME, $e->getMessage() . PHP_EOL, FILE_APPEND);
    $sMessage = $e->getMessage();
}

$sLinkToRefresh = "<br><a href='/allprojects/'>Обновить</a>";
$sJSQuery = ($sMessage !== '') ?
    '
            $().toasty({
                message: "' . $sMessage . $sLinkToRefresh . '",
                position: "tc",
                autoHide: 10000
            });
' : '';

showHead(
    array(
        'TITLE' => 'Веб-студия «Мой голос» - Все проекты',
        'HEAD_CONTENT' => <<<HEREDOC
    <!-- Toasty - A jQuery plugin for message toasts -->
    <script type="text/javascript" src="/vendor/clifordshelton/toasty/toasty-min.js"></script>
    <link rel="stylesheet" type="text/css" href="/vendor/clifordshelton/toasty/toasty-min.css">
    <script type="text/javascript">
        $(document).ready(function () {
            $sJSQuery
        });
    </script>
HEREDOC
    )
);
?>
<body>

    <div class="container">

        <div class="top-line">

            <h1>Все проекты <sup><a href="http://web.moigolos.net/pladm/phpliteadmin.php">&#920;</a></sup></h1>

            <ul>
                <li>
                    <a href="/portfolio/">Подборка сайтов из портфолио</a>
                </li>
            </ul>

        </div>

        <?foreach ($arDBTypesProjects as $arType):?>
            <div class="project-type">
                <h2><?=$arType['name']?></h2>
                <?if (count($arType['projects']) < 1):?>
                    <div class="empty">нет</div>
                <?else:?>
                    <ul class="projects-list">
                    <?foreach ($arType['projects'] as $arProject):?>
                        <li>
                            <a href="/allprojects/<?=$arProject['name']?>/"><?=$arProject['name']?></a> <a href="<?=$arProject['url']?>">&#963;</a>
                            <div class="project-info-link">
                                &#926;
                                <div class="project-info">
                                    <?=$arProject['desc']?>
                                </div>
                            </div>
                        </li>
                    <?endforeach;?>
                    </ul>
                <?endif;?>
            </div>
        <?endforeach;?>

        <div class="project-type">
            <blockquote>
                <? echo myExtCites::getOneRandomCite(); ?>
            </blockquote>
        </div>

        <? showBodyFooter(); ?>

    </div>

</body>
</html>
<?

// echo '<pre>';
// print_r($arDBTypes);
// print_r($arDBProjects);
// print_r($arFSProjects);
// print_r($arDBTypesProjects);
// echo '</pre>';
