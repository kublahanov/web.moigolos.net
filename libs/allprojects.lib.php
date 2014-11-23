<?

/**
 * Класс с полями для БД pr_list
 * Предназначен для правильного добавления записи в БД (INSERT)
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

/**
 * Класс для контроля времени кеширования
 * через файловый дескриптор
 */
class myCacheTimer
{
    public static $arCacheTimers = array();
    protected static $sPath = '', $sTimerExt = 'ts';

    /**
     * [__construct description]
     */
    function __construct()
    {
    }

    /**
     * [setConfig description]
     * @param string $sPath     [description]
     * @param string $sTimerExt [description]
     */
    public static function setConfig($sPath = '', $sTimerExt = 'ts')
    {
        if ($sPath)
        {
            static::$sPath = $sPath;
        }
        if ($sTimerExt)
        {
            static::$sTimerExt = $sTimerExt;
        }
    }

    /**
     * [addCacheTimer description]
     * @param [type] $sTimerName  [description]
     * @param [type] $iCachedTime [description]
     */
    public static function addCacheTimer($sTimerName, $iCachedTime)
    {
        if (strlen(trim($sTimerName)) && (intval($iCachedTime) > 0))
        {
            static::$arCacheTimers[$sTimerName] = $iCachedTime;
        }
    }

    public static function checkTimeIsOver($sTimerName)
    {
        if ($sTimerName && isset(static::$arCacheTimers[$sTimerName]))
        {
            $sFileName = static::$sPath . $sTimerName . '.' . static::$sTimerExt;
            if (file_exists($sFileName))
            {
                if (fileatime($sFileName) + static::$arCacheTimers[$sTimerName] < time())
                {
                    return touch($sFileName);
                }
                return false;
            }
            else
            {
                return touch($sFileName);
            }
        }
    }
}
