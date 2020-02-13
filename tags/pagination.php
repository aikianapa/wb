<?php
function tagPagination(&$dom) {
        ini_set('max_execution_time', 900);
        ini_set('memory_limit', '1024M');
        $pages=ceil($dom->params->count/$dom->params->size);
        $dom->attr("data-wb-pages",$pages);
        $tplId=$dom->attr("data-wb-tpl");
        $class="ajax-".$tplId;

        if (is_object($dom->parent("table")) && $dom->parent("table")->find("thead th [data-wb-sort]")->length) {
            $dom->parent("table")->find("thead")->attr("data-wb-size",$size);
            $dom->parent("table")->find("thead")->attr("data-wb",$class);
        }
        if ($pages>"0" OR $dom->attr("data-wb-sort")>"") {
            $pag=$dom->app->getForm("common","pagination");
            //$pag->wrapInner("<div></div>");
            $step=1;
            $flag=floor($page/10);
            if ($flag<=1) {
                $flag=0;
            }
            else {
                $flag*=10;
            }
            //$inner="";
            $pagination=array("id"=>$class,"size"=>$size,"count"=>$count,"cache"=>$cacheId,"find"=>$find,"pages"=>array());
            if (!isset($_ENV["route"]["params"]["form"]) OR $_ENV["route"]["params"]["form"]=="") {
                $form=$tplId;
            }
            else {
                $form=$_ENV["route"]["params"]["form"];
            }

            $pagarr=wbPagination($page,$pages);
            foreach($pagarr as $i => $p) {

                $pn=$p;
                if ($p=="..." AND $pagarr[$i-1]<$page) {
                    $pn=intval($page/2);
                }
                if ($p=="..." AND $pagarr[$i-1]>=$page) {
                    $pn=intval($page+($pages-$page)/2);
                }


                $href=$_ENV["route"]["controller"]."/".$_ENV["route"]["mode"]."/".$form."/".$pn;
                $pagination["pages"][$i]=array(
                                             "page"=>$p,
                                             "href"=>$href,
                                             "flag"=>$flag,
                                             "data"=>"{$class}-{$pn}"
                                         );
            }
            $pag->fetch($pagination);
            $pag->find("[data-page={$page}]")->addClass("active");
            $pag->find("ul")->attr("data-wb-route",json_encode($_ENV["route"]));
            if ($pages<2) {
                $style=$pag->find("ul")->attr("style");
                $pag->find("ul")->attr("style",$style.";display:none;");
            }
            $dom->after($pag->innerHtml());
        }
        $dom->removeAttr("data-wb-pagination");
        return $dom;
    }
?>