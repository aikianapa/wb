<?php
function sitemap__init() {
	ini_set('max_execution_time', 9999);
    if (isset($_ENV["route"]["params"][0])) {
        $mode=$_ENV["route"]["params"][0];
        $call="sitemap__{$mode}";
        if (is_callable($call)) {
            $out=@$call();
        }
        echo $out;
        die;
    } else {
        unset($_SESSION["moduleSitemap"]);
        $out=wbFromFile(__DIR__ ."/sitemap_ui.php");
        $out->wbSetData();
        return $out->outerHtml();
    }
}

function sitemap__ajax() {
	if (isset($_POST["link"])) {
		if (isset($_POST["link"]) AND $_POST["link"]=="__finish__") {
			sitemap__generate_finish();
		} else {
			echo sitemap__generate($_POST["link"]);
		}
	} else {
		echo sitemap__generate();
	}

}

function sitemap__check_show($form) {
	$formfile="{$_ENV["path_app"]}{$form["dir"]}/{$form["name"]}.php";

	if (is_file($formfile)) {$formfile=file_get_contents($formfile);}
	if (strpos($formfile,"{$form["form"]}_show") OR strpos($formfile,"{$form["form"]}__show")) {$func=true;} else {$func=false;}

		if (($form["mode"]=="show" OR $func==true) && !in_array($form["form"],$forms)) {
			return true;
		}
		return false;
}

function sitemap__generate($href=null) {
	if (!isset($_SESSION["moduleSitemap"]) OR $href==null) {
        if (isset($_POST["sub"]) AND $_POST["sub"]>"" AND $_POST["sub"]!==".") {$sub=$_POST["sub"]."."; $sub=str_replace("..",".",$sub);} else {$sub="";}
        if ($sub>"") {$host=str_replace("://","://".$sub,$_ENV["route"]["hostp"]);} else {$host=$_ENV["route"]["hostp"];}
		$_SESSION["moduleSitemap"]=array("sitemap"=>"","level"=>0,"list"=>array(),"data"=>array(),"sub"=>$sub,"host"=>$host);
        $href="";
	}
    $list=array();
    $l=parse_url($href);
    $sub=$_SESSION["moduleSitemap"]["sub"];
    if (!strpos($href,"://")) {$href=$_SESSION["moduleSitemap"]["host"];}
    if (!isset($l["host"]) OR $l["host"]=="" OR $l["host"]==$sub.$_ENV["route"]["host"]) {
				$count++;
				$content=file_get_contents($href);
				if ($content) {
					$out=wbFromString($content);
                    if ($out->find("head meta[name=lastmod]:last")->length) {$lastmod=$out->find("head meta[name=lastmod]:last",0)->attr("content");} else {$lastmod=time();}
                    if ($out->find("head meta[name=priority]:last")->length) {$priority=$out->find("head meta[name=priority]:last",0)->attr("content");} else {$priority="0.5";}
                    if ($out->find("head meta[name=changefreq]:last")->length) {$freq=$out->find("head meta[name=changefreq]:last",0)->attr("content");} else {$freq="weekly";}
                    $lastmod=date('c',strtotime($lastmod));
                    $_SESSION["moduleSitemap"]["data"][$href]=array("freq"=>$freq,"lastmod"=>$lastmod,"priority"=>$priority);
				    sitemap__node($href);
					$oLinks=$out->find("a[href]");
					foreach($oLinks as $oLink) {
						$link=$oLink->attr("href");
                        if (!strpos("://",$link)) {$link=$_SESSION["moduleSitemap"]["host"].$link;}
                        if (strpos($link,"#")) {$link=explode("#",$link); $link=$link[0];}
						$l=parse_url($link);
						if (
                            !in_array($link,$_SESSION["moduleSitemap"]["list"])
                            AND !in_array($link,$list)
                            AND ($l["host"]==$_ENV["route"]["domain"] OR $l["host"]==$sub.$_ENV["route"]["host"])
                            AND $l["path"]!=="void(0)"
                            AND !strpos($link,"}}")
                        ) {
							$list[]=$link;
						}
					}
                    unlink($out,$content,$oLinks);
				}

    }
	//echo $out->outerHtml();
	if (count($list)==0) {
		return json_encode(false);

	} else {
		return json_encode($list);
	}
}

function sitemap__generate_finish() {
$sitemap='<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
'.$_SESSION["moduleSitemap"]["sitemap"].'
</urlset>';
$fname="sitemap";
if ($_SESSION["moduleSitemap"]["sub"]>"") {$sub=str_replace(".","",$_SESSION["moduleSitemap"]["sub"]);} else {$sub="";}
if ($sub>"") {$fname.="_".$sub;}
$fname.=".xml";
file_put_contents("{$_ENV["path_app"]}/".$fname,$sitemap);
unset($_SESSION["moduleSitemap"]);
return json_encode(false);
}


function sitemap__node($link) {
$lastmod=date('c',time());
$priority="0.5";
$freq="weekly";
if (isset($_SESSION["moduleSitemap"]["data"][$link]) AND is_array($_SESSION["moduleSitemap"]["data"][$link])) {
    foreach($_SESSION["moduleSitemap"]["data"][$link] as $key => $val) {
        $$key=$val;
    }
}

$date=date('c',time());
$node='<url>
	<loc>'.$link.'</loc>
	<lastmod>'.$lastmod.'</lastmod>
    <priority>'.$priority.'</priority>
	<changefreq>'.$freq.'</changefreq>
</url>
';
$_SESSION["moduleSitemap"]["sitemap"].=$node;
$_SESSION["moduleSitemap"]["list"][]=$link;
}

?>
