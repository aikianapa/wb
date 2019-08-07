<?php
    function tagForeach(&$dom,$Item=null) {
        $oddeven="even";
        $ndx=0;
        $n=0;
        $page=1;
        $size=false;
        
        $tpl=$dom->html();
        $dom->clear();
        if ($Item==null && isset($dom->data)) $Item=$dom->data;
        if ($Item==null && !isset($dom->data)) $Item=[];
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
        if (isset($dom->params->from) AND isset($Item[$dom->params->from])) {
            $Item=$Item[$dom->params->from];
        } else if (isset($dom->params->from) AND !isset($Item[$dom->params->from])) {
            $Item=[];
        }
// sort items
        if (isset($dom->params->sort)) $Item=wbArraySort($Item,$dom->params->sort);
// randomize items
        if (isset($dom->params->rand) AND ($dom->params->rand=="true" OR $dom->params->rand=="1" )) shuffle($Item);
// get page size
        if (isset($dom->params->size) AND intval($dom->params->size) > 0) $size=intval($dom->params->size);
// get page number
        if (isset($dom->params->page) AND intval($dom->params->page) > 0) $page=intval($dom->params->page);
        $dom->data = $Item;
        foreach($Item as $key => $val) {
            $n++;
            if ($size) {
                $minpos=$size*$page-($size*1)+1;
                $maxpos=($size*$page);
            }
            if ($size == false OR ($n>=$minpos AND $n<=$maxpos)) {
                if ($oddeven=="even") {
                    $oddeven="odd";
                } else {
                    $oddeven="even";
                }
                $val["_odd"]=$oddeven;
                $val["_key"]=$key;
                $val["_ndx"]=$ndx+1;
                $tmptpl=$dom->app->fromString($tpl);
                $tmptpl->parent = $dom;
                $tmptpl->fetch($val);
                $dom->append($tmptpl);
                $ndx++;
            }
            if (isset($dom->params->limit) AND $ndx >= $dom->params->limit*1) break;
        }
        $dom->done = true;
        return $dom;
    }
?>