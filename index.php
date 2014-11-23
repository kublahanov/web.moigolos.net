<?

/*
 * Главная страница
 * http://web.moigolos.net/pladm/phpliteadmin.php
 * https://ru.wikipedia.org/wiki/Мнемоники_в_HTML
 */

require($_SERVER['DOCUMENT_ROOT'] . '/libs/global.lib.php');
require($_SERVER['DOCUMENT_ROOT'] . '/libs/template.lib.php');

showHead(
    array(
        'TITLE' => 'Веб-студия «Мой голос» - Главная',
        'HEAD_CONTENT' => <<<HEREDOC
    <script type="text/javascript">
        $(document).ready(function () {
            $("div.container > header > div > figure > img").click(function(){
                window.location = "/allprojects/";
            });
        });
    </script>
HEREDOC
    )
);
?>
<body>

    <div class="container">

        <header class="not-show-in-test">
            <div>
                <figure>
                    <img src="/img/earth_001.jpg" alt="студия &laquo;Мой голос&raquo;">
                </figure>
            </div>
        </header>

        <? showBodyFooter(); ?>

    </div>

</body>
</html>
<?
