<!DOCTYPE html>
<!--Тестовое задание. Написать веб-приложение поиска ссылок на странице
википедии-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Поиск ссылок</title>
    </head>
    <body>
        <form action="" method="GET">
            <label>Поиск:</label>
            <input type="text" name = 'search'>
            <input type="submit" value="отжечь">
        </form>
        <?php
            require 'phpQuery-onefile.php';
            if(isset($_GET['search'])){
                $search = strip_tags(htmlspecialchars($_GET['search']));                
                $search = str_replace(' ', '_', $search);
                $opts = array(
                    'https'=> array(
                        'method'=>'GET',
                        'header'  => 'Content-type: application/x-www-form-urlencoded'
                        )
                    );
                $context = stream_context_create($opts);
                $contents = file_get_contents("https://ru.wikipedia.org/w/index.php?search=$search",false,$context);
                
                $doc = phpQuery::newDocument($contents);
                
                /**
                 * нахожу элемент класса "mw-headline" содержащий текст "Ссылки" 
                 */             
                
                $mwheadline = $doc->find("h2 .mw-headline:contains('Ссылки')");
                
                /**
                 * получаю родительский элемент, это h2 заголовок
                 */
                $paretHeadline = pq($mwheadline)->parent();
                
                /**
                 * отбираю все элементы следующие за h2 загловком "Ссылки"                 
                 */
                $allNextElemens = pq($paretHeadline)->nextAll();
                echo "<ol>";
                /**
                 * перебираю полеченные элементы
                 */
                foreach($allNextElemens as $element){
                    /**
                     * проверяю если в элементе есть дочернй элемент
                     * класса "mw-headline" значит начался новый блок
                     * прекращаем цикл
                     */
                    $nextHeadline = pq($element)->find(".mw-headline");
                    if(pq($nextHeadline)!='')
                        break;                    
                    
                    /**
                     * ищем в текущем элементе дочерние элементы li
                     */
                    $ul_li =  pq($element)->find('li');
                    
                    /**
                     * перебираем все li элементы
                     */
                    foreach($ul_li as $val){
                        /**
                         * ищим первую внещнюю ссылку в строке списка
                         * если не найдена переходим к следующему элементу
                         */
                        $li_a = pq($val)->find('a[href^="http://"]:first');
                        if(pq($li_a)=='')
                            continue;
                        /**
                         * вывожу найденную ссылку
                         */
                        echo "<li><a href = ".pq($li_a)->attr('href')."> "
                                .pq($li_a)->attr('href')."</a> ".pq($li_a)->text()."</li>";  
                    } 
                }
                echo "</ol>";
            }
        ?>
    </body>
</html>

<!--
<h2>
<a class="mw-headline-anchor" title="Ссылка на этот раздел" aria-hidden="true" href="#.D0.A1.D1.81.D1.8B.D0.BB.D0.BA.D0.B8">§</a>
<span id=".D0.A1.D1.81.D1.8B.D0.BB.D0.BA.D0.B8" class="mw-headline">Ссылки</span>
<span class="mw-editsection">


<h2>
<a class="mw-headline-anchor" title="Ссылка на этот раздел" aria-hidden="true" href="#.D0.A1.D1.81.D1.8B.D0.BB.D0.BA.D0.B8">§</a>
<span id=".D0.A1.D1.81.D1.8B.D0.BB.D0.BA.D0.B8" class="mw-headline">Ссылки</span>



</h2>
                        infobox sisterproject noprint wikiquote-box
<table class="metadata plainlinks navigation-box ruwikiWikimediaNavigation" style="margin:0 0 1em 1em; clear:right; border:solid #aaa 1px; background:#f9f9f9; padding:1ex; font-size:90%; float:right;">
<ul>
<li>
<li>
<li>
<li>
<li>
<li>
<li>
<li>
<li>
</ul>
<table id="collapsibleTable0" class="navbox collapsible autocollapse nowraplinks" style="margin:auto;;">

-->