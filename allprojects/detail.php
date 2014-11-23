<?

/*
 * Страница отображения проекта детально
 */

if (!isset($_GET['project_name']))
{
    header('Location: /allprojects/');
}
else
{
    $sProjectName = trim($_GET['project_name'], '/');
}

require($_SERVER['DOCUMENT_ROOT'] . '/libs/global.lib.php');
require($_SERVER['DOCUMENT_ROOT'] . '/libs/template.lib.php');

define ('PROJECTS_DB_FILE_NAME', $_SERVER['DOCUMENT_ROOT'] . 'db/projects.db');
define ('ALL_PROJECTS_LIST_TABLE_NAME', 'projects_list');
define ('ALL_PROJECTS_TYPE_TABLE_NAME', 'projects_type');
define ('PDO_ERROR_LOG_NAME', $_SERVER['DOCUMENT_ROOT'] . 'log/PDOErrors.log');

$arProject = array();

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
    $sQuery = "
        SELECT L.name AS name, L.desc AS desc, L.url AS url, T.name AS type
        FROM " . ALL_PROJECTS_LIST_TABLE_NAME . " AS L
        INNER JOIN " . ALL_PROJECTS_TYPE_TABLE_NAME . " AS T
        ON L.type = T.id
        WHERE L.name = '" . $sProjectName . "'
        LIMIT 1
    ";
    $dbStat = $dbLink->query($sQuery);
    $dbStat->setFetchMode(PDO::FETCH_ASSOC);
    $arProject = reset($dbStat->fetchAll());

    $dbLink = null;

    if (!$arProject)
    {
        header('Location: /allprojects/');
    }
}
catch (PDOException $e)
{
    file_put_contents(PDO_ERROR_LOG_NAME, $e->getMessage() . PHP_EOL, FILE_APPEND);
    $sMessage = $e->getMessage();
}

showHead(
    array(
        'TITLE' => 'Веб-студия «Мой голос» - Проект «' . $arProject['name'] . '»',
        'HEAD_CONTENT' => <<<HEREDOC
    <script type="text/javascript">
        $(document).ready(function () {
        });
    </script>
HEREDOC
    )
);
?>
<body>

    <div class="container">

        <div class="top-line">

            <h1>Проект «<?=$arProject['name']?>»</h1>

            <ul>
                <li>
                    <a href="/allprojects/">Все проекты</a>
                </li>
            </ul>

        </div>

        <div class="project-type">
            <h2>Тип проекта:</h2>
            <p>
                <?=$arProject['type']?>
            </p>
        </div>

        <div class="project-type">
            <h2>Описание:</h2>
            <div class="description-inner">
                <?=$arProject['desc']?>
            </div>
        </div>

        <div class="project-type">
            <h2>Ссылка:</h2>
            <p>
                <a href="<?=$arProject['url']?>"><?=$arProject['url']?></a>
            </p>
        </div>

        <? showBodyFooter(); ?>

    </div>

</body>
</html>
<?

// echo '<pre>';
// print_r($_SERVER);
// print_r($_GET);
// print_r($arProject);
// echo '</pre>';
