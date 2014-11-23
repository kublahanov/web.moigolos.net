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

/**
 * [getActualLink description]
 * @return [type] [description]
 */
function getActualLink($bTypeFlag = true)
{
    if ($bTypeFlag)
    {
        return 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    }
    else
    {
        return ('http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME'])) . ((dirname($_SERVER['SCRIPT_NAME']) == '/') ? '' : '/');
    }
}

/**
 * [showGlobalProjectFooter description]
 * @return [type] [description]
 */
function showGlobalProjectFooter()
{
?>
    <label class="global-project-date">&#169; 2007-<?=date('Y')?></label>
    <a class="global-project-wmglink" href="http://web.moigolos.net">Веб-студия</a>
    <label class="global-project-wmgname">«Мой голос».</label>
    <label class="global-project-support-pretext">Поддержка проекта</label>
    <a class="global-project-mglink" href="http://www.moigolos.net">"Мой голос"</a>.
    <label class="global-project-makeup-pretext">Верстаем</label>
    <a class="global-project-w3clink" href="http://validator.w3.org/check?uri=<? if (function_exists('getActualLink')) echo getActualLink(); ?>">W3C-кошерно</a>
    <label class="global-project-smile">:)</label>
<?
}

/**
 * Цитаты
 */
class myExtCites
{
    /**
     * [getOneRandomCite description]
     * @param  integer $iProjectID [description]
     * @return [type]              [description]
     */
    public static function getOneRandomCite($iProjectID = 0)
    {
        $dbFile = '/var/www/global/db/sqlite/global_v1.db';
        $dbLink = new PDO('sqlite:' . $dbFile);

        $sQuery = '
            SELECT text FROM ext_cites
            WHERE
                _ROWID_ >= (abs(random()) % (SELECT max(_ROWID_) + 1 FROM ext_cites))
        ';
        if ($iProjectID > 0)
        {
            $sQuery .= '
                AND project_id = ' . $iProjectID . '
            ';
        }
        $sQuery .= '
            LIMIT 1
        ';

        $dbStat = $dbLink->query($sQuery);
        $dbStat->setFetchMode(PDO::FETCH_ASSOC);
        $arResult = reset($dbStat->fetchAll());

        $dbLink = null;

        return $arResult['text'];
    }
}
