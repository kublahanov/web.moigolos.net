<?

/**
 * [showHead description]
 * @param  array  $arParams [description]
 * @return [type]           [description]
 */
function showHead($arParams = array())
{
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="author" content="Веб-студия «Мой голос»">
    <meta name="description" content="Веб-студия «Мой голос»">
    <meta name="keywords" content="веб, Веб-студия, разработка, php, html5, css3">
    <meta name='yandex-verification' content='7b06ca2324c21a0d' />
    <link rel="shortcut icon" href="/favicon.ico">

    <!-- script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script -->
    <script type="text/javascript" src="http://yastatic.net/jquery/2.1.1/jquery.min.js"></script>
    <script>if (!window.jQuery) { document.write('<script src="http://185.26.114.166/global/js/jquery-2.1.1.min.js"><\/script>'); }</script>
    <script src="http://yastatic.net/modernizr/2.7.1/modernizr.min.js"></script>
    <script>if (!window.Modernizr) { document.write('<script src="http://185.26.114.166/global/js/modernizr-2.8.3.min.js"><\/script>'); }</script>

<?
if (isset($arParams['HEAD_CONTENT']))
{
    echo $arParams['HEAD_CONTENT'];
}
?>

    <link href="/css/style.css" rel="stylesheet" type="text/css">
    <title>
<?
if (isset($arParams['TITLE']))
{
    echo $arParams['TITLE'];
}
?>
    </title>
</head>
<?
}

/**
 * [showBodyFooter description]
 * @return [type] [description]
 */
function showBodyFooter()
{
?>
        <footer class="not-show-in-test">

            <div>
                <div class="copyrights centered">
                    <?
                        showGlobalProjectFooter();
                    ?>
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
<?
}
