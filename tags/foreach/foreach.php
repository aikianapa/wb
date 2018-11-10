<?php
class tagForeach extends kiNode  {

    function __construct($that){
        $this->DOM = $that;
    }

    public function __destruct(){
        unset($this->DOM);
    }

	public function tagForeach($Item=array()) {
		$srcItem=$Item;
		$field=""; $sort=""; $add=""; $step=""; $id=""; $limit=""; $table=""; $cacheId=0; $form=""; $where="";

		include($_ENV["path_engine"]."/wbattributes.php");


    if ($form>"" AND $table=="") {$table=$form;}
    if (isset($cache)) {$cacheId=$cache;}
    if (isset($tpl) AND $tpl=="flase") {$tplid=false;} else {
		    if ($tpl=="") {$this->DOM->addTemplate();}
			$tplid=$this->DOM->attr("data-wb-tpl");
		}
		      if (!isset($size) OR $size=="false") {$size=false;}
		        if (!isset($page) OR 1*$page<=0) {
			         if (!isset($_GET["page"]) OR $_GET["page"]=="") {$page=1;} else {$page=$_GET["page"]*1;}
		        } else {$page=$page*1;}
		if ( isset($from) AND $from > "") {
			if (isset($Item[$from]) AND !strpos("[",$from)) {
				//if (isset($Item["form"])) {$table=$Item["form"];} else {$table="";}
				//if (isset($Item["id"])) {$item=$Item["id"];} else {$item="";}
				$Item=wbItemToArray($Item[$from]);
				if (!is_array($Item)) {$Item=json_decode($Item,true);}
				if ($field>"") {
                    $Item=$Item[$field];
                    if (!is_array($Item)) {$Item=json_decode($Item,true);}
                }
			} else {
                $Item=wbGetDataWbFrom($Item,$from);
            }
		}

        if (isset($json) AND $json> "") {
            $Item=json_decode($json,true);
        }

        if (isset($count) AND $count>"") {
            $fcount=wbArrayAttr($count);
            $Item=array();
            if (count($fcount)==1) {
                for($i=1;$i<=$count*1;$i++){$srcItem["_ndx"]=$i;$Item[$i]=$srcItem;};
            } elseif (count($fcount)==2) {
                if ($fcount[0]<=$fcount[1]) {
                    for($i=$fcount[0];$i<=$fcount[1];$i++){$srcItem["_ndx"]=$i;$Item[$i]=$srcItem;};
                } else {
                    for($i=$fcount[0];$i>=$fcount[1];$i--){$srcItem["_ndx"]=$i;$Item[$i]=$srcItem;};
                }
            } elseif (count($fcount)>2) {
                foreach($fcount as $i) {if (is_numeric($i)) {$srcItem["_ndx"]=$i;$Item[$i]=$srcItem;}}
            }
        }

        if (isset($form) AND !isset($table)) {$table=$form;}
        if ($table > "") {
            $table=wbTable($table);
            $itemform=wbTableName($table);
      			if (isset($item) AND $item>"") {
      				$Item[0]=wbItemRead($table,$item);
              if ($field>"") {
      					$Item=$Item[0][$field];
      					if (is_string($Item)) { $Item=json_decode($Item,true); }
      					if (isset($Item[0]["img"]) && isset($Item[0]["visible"])) {
      						$Item=array_filter_value($Item,"visible","1");
      					}
      				}
      			}  else {
                $Item=wbItemList($table,$where);
      			}
        }
                if (isset($call) AND is_callable($call)) {$Item=@$call();}
                if (isset($oconv) AND is_callable($oconv)) {$Item=@$oconv($Item);}

		if (is_string($Item)) $Item=json_decode($Item,true);
		if (!is_array($Item)) $Item=array($Item);
		if ($sort>"") {$Item=wbArraySortMulti($Item,$sort);}
		if (isset($rand) AND $rand!=="false") {shuffle($Item);}

		if ($add=="true" AND $this->DOM->find(":first-child",0)->length) {$this->DOM->find(":first-child",0)->attr("item","{{id}}");}
		$tpl=$this->DOM->innerHtml(); $inner=""; $this->DOM->html("");
		if ($step>0) {$steptpl=$this->DOM->clone(); $stepcount=0; $steps=wbFromString("");}
		if ($tplid=="") $tplid="tpl".wbNewId();
		$ndx=0; $fdx=0; $n=0; $stp=0;
		$count=count($Item);
		$inner="";
		$srcVal=array();
                if (is_array($srcItem)) {
                        foreach($srcItem as $k => $v) {$srcVal["%{$k}"]=$v;}; unset($v); //$srcVal["_parent"]=$srcItem;
                }
		$ndx=0; $n=0; $f=0;
		$tmptpl=wbFromString($tpl);
        if (isset($rand) AND $rand=="true") {shuffle($Item);}
        if (!$this->DOM->hasClass("pagination") AND $form=="" AND $table=="" ) {
            // если список формируется функцией, то используем кэш
            $checkCache=md5($_SESSION["user_id"].$_ENV["lang"].$_SESSION["lang"]).md5($_ENV["route"]["uri"].$sort.$find);
            if ($cacheId==0 OR $cacheId!==$checkCache) {
                $cacheId=$checkCache;
                file_put_contents($_ENV["dbac"]."/".$cacheId,base64_encode(wbJsonEncode($Item)));
            } else {
                $Item=json_decode(base64_decode(file_get_contents($_ENV["dbac"]."/".$cacheId)),true);
            }
        }
        $object = new ArrayObject($Item);
		$iterator = new tagForeachFilter($object->getIterator(),array(
			"id"=>$id,
		));
        $oddeven="even";
		foreach($object as $key => $val) {
				$n++;
				if ($size!==false) $minpos=$size*$page-($size*1)+1; $maxpos=($size*$page);
				if ($size==false OR ($n<=$maxpos AND $n>=$minpos)) {
					if (!isset($itemform)) { if (isset($val["_table"])) {$itemform=$val["_table"];} else {$itemform=$_ENV["route"]["form"]; }}
					$text=$tmptpl->clone();
					$val=(array)$srcVal + (array)$val; // сливаем массивы
                    $text->find(":first")->attr("idx",$key);
                    if ($oddeven=="even") {$oddeven="odd";} else {$oddeven="even";}
                    $val["_odd"]=$oddeven;
					$val["_key"]=$key;
					$val["_idx"]=$ndx;
					if (!isset($val["_ndx"])) $val["_ndx"]=$ndx+1;
                    $val["_step"]=$stp;
					$flag=true;
					if ($flag==true AND isset($where)) {$flag=wbWhereItem($val,$where);}
					if ($flag==true AND $limit>"" AND $ndx>=$limit) {$flag=false;}
						if ($flag==true) {
							$ndx++;
                            $val=wbCallFormFunc("BeforeShowItem",$val,$itemform);
                            $val=wbCallFormFunc("BeforeItemShow",$val,$itemform);
                            $text->wbSetData($val);
							if ($step>0) { // если степ, то работаем с объектом
								if ($stepcount==0) {
									$t_step=$steptpl->clone();
									$t_step->addClass($tplid);
									$steps->append($t_step);
								}
								$steps->find(".{$tplid}:last")->append($text->outerHtml());
								$stepcount++;
								//$stepcount=$this->DOM->find(".{$tplid}:last")->children()->length;
								if ($stepcount==$step) {$stepcount=0; $stp++;}
							} else { // иначе строим строку
								$inner.=wbClearValues($text->outerHtml());
							}
						}

				}
				unset($Item[$key]);
		};
		$count=$n;

			if ($step>0) {
                $this->DOM->replaceWith($steps->html());
				foreach ($this->DOM->find(".{$tplid}") as $tid) {$tid->removeClass($tplid);}; unset($tid);
			} else {
				$this->DOM->html($inner);
			}
			unset($val,$ndx,$t_step,$string,$text,$func,$inner,$tmptpl);

		if ($this->DOM->tag()=="select") {
			if (isset($result) AND !is_array($result)) {$this->DOM->outerHtml("");}
            if (isset($srcItem[$this->DOM->attr('name')])) $this->DOM->attr('value',$srcItem[$this->DOM->attr('name')]);
			$plhr=$this->DOM->attr("placeholder");
			if ($plhr>"") {$this->DOM->prepend("<option value=''>$plhr</option>");}
		} else {
            if ($this->DOM->attr("data-wb-group")>"" OR $this->DOM->attr("data-wb-total")>"") {$this->DOM->wbTableProcessor(); $size=false;}
			if ($size!==false AND !$this->DOM->hasClass("pagination")) {
				$cahceId=null; $find=null;
				$pages=ceil($count/$size);
                $this->DOM->tagPagination($size,$page,$pages,$cacheId,$count,$find);
			}
		}
		gc_collect_cycles();

	}


}