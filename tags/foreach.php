<?php
    function tagForeach(&$dom,$Item=null) {
        if ($dom->hasClass("wb-done")) return $dom;
        $oddeven="even";
        $ndx=0;
        $n=0;
        $page=1;
        $limit = -1;
        $size=false;
        $inner="";
        $empty=$dom->children("empty")->fetch($Item)->html();
        $dom->children("empty")->remove();
        $tpl=$dom->html();
        $dom->clear();
        if ($Item==null && isset($dom->data)) $Item=$dom->data;
        if ($Item==null && !isset($dom->data)) $Item=[];
        if ($dom->params->count) {
            $srcItem = $Item;
            $fcount=wbArrayAttr($dom->params->count);
            $Item=array();
            if (count($fcount) == 1) {
                for($i=1; $i<=$fcount[0]*1; $i++) {
                    $srcItem["_ndx"]=$i;
                    $Item[$i]=$srcItem;
                };
            }
            elseif (count($fcount) == 2) {
                if ($fcount[0]<=$fcount[1]) {
                    for($i=$fcount[0]; $i<=$fcount[1]; $i++) {
                        $srcItem["_id"]=$srcItem["id"]=$i;
                        $Item[$i]=$srcItem;
                    };
                } else {
                    for($i=$fcount[0]; $i>=$fcount[1]; $i--) {
                        $srcItem["_id"]=$srcItem["id"]=$i;
                        $Item[$i]=$srcItem;
                    };
                }
            }
            elseif (count($fcount) > 2) {
                foreach($fcount as $i) {
                    if (is_numeric($i)) {
                        $srcItem["_id"]=$srcItem["id"]=$i;
                        $Item[$i]=$srcItem;
                    }
                }
            }
        }        
        
// get items from table
        if (isset($dom->params->form) AND in_array($dom->params->form,$_ENV["forms"])) {
            $Item=wbItemList($dom->params->form);
        } else if (isset($dom->params->form) AND !in_array($dom->params->form,$_ENV["forms"])) {
            $Item=[];
        }
        if (isset($dom->params->table) AND in_array($dom->params->table,$_ENV["forms"])) {
            $Item=wbItemList($dom->params->table);
        } else if (isset($dom->params->table) AND !in_array($dom->params->table,$_ENV["forms"])) {
            $Item=[];
        }
// get items from array
        if ($dom->params->from AND isset($Item[$dom->params->from])) {
            $Item=$Item[$dom->params->from];
        } else if ($dom->params->from AND !isset($Item[$dom->params->from])) {
            $Item=[];
        }
// sort items
        if ($dom->params->sort) $Item=wbArraySort($Item,$dom->params->sort);
// randomize items
        if ($dom->params->rand AND ($dom->params->rand=="true" OR $dom->params->rand=="1" )) shuffle($Item);
// get page size
        if ($dom->params->size AND intval($dom->params->size) > 0) $size=intval($dom->params->size);
// get page number
        if ($dom->params->page) $page=intval($dom->params->page);
// get list limit
        if ($dom->params->limit) $limit = intval($dom->params->limit);
        $dom->data = $Item;
        foreach($Item as $key => $val) {
            $n++;
            $flag = true;
            if ($size) {
                $minpos=$size*$page-($size*1)+1;
                $maxpos=($size*$page);
            }
            if ($size == false OR ($n>=$minpos AND $n<=$maxpos)) {
                $val["_odd"]=$oddeven;
                $val["_key"]=$key;
                $val["_ndx"]=$ndx+1;
                if ($flag==true AND $dom->params->if) {
                    $flag=wbWhereItem($val,$dom->params->if);
                }
                if ($flag) {
                    if ($oddeven=="even") {
                        $oddeven="odd";
                    } else {
                        $oddeven="even";
                    }
                    $tmptpl=$dom->app->fromString($tpl,false);
                    $tmptpl->parent = $dom;
                    $tmptpl->fetch($val);
                    $inner.=$tmptpl;
                    $ndx++;
                    if ($ndx == $limit) break;
                } else {
                    $n--;
                }
            }
            if (isset($dom->params->limit) AND $n >= $dom->params->limit*1) break;
        }
        $count=$n;
        $dom->attr("data-wb-rows",$count);
        $dom->params->count = $count;
        if ($count==0) $inner= $empty;
        $dom->html(wbClearValues($inner));
        
        if ($dom->tag()=="select") {
            $dom->html($inner);
            if (isset($result) AND !is_array($result)) {
                $dom->outerHtml("");
            }
            if (isset($srcItem[$dom->attr('name')])) $dom->attr('value',$srcItem[$dom->attr('name')]);
            $plhr=$dom->attr("placeholder");
            if ($plhr>"") {
                $dom->prepend("<option value=''>$plhr</option>");
            }
        } else {
            if ($step>0) {
                $dom->replaceWith($steps->html());
                foreach ($dom->find(".{$tplid}") as $tid) {
                    $tid->removeClass($tplid);
                };
                unset($tid);
            } else {
                $dom->html($inner);
            }
            if ($dom->attr("data-wb-group")>"" OR $dom->attr("data-wb-total")>"") {
                //$dom->wbTableProcessor();
                $size=false;
            }
            if ($size!==false AND !$dom->hasClass("pagination")) {
                $cahceId=null;
                $find=null;
                tagPagination($dom,$pages);
            }
        }
        $dom->addClass("wb-done");
        return $dom;
    }
?>