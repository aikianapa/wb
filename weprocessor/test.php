<?php

include_once(__DIR__."/weprocessor.php");

// xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

// Переменная $_SESSION //
$_SESSION["user"]=array("tester");										// добавил array
$_SESSION["user"]["role"]="moder";
$_SESSION["tester"]["phone"][1]="+7 (918)-999-11-11";
$_SESSION["tester"]["phone"][2]="+7 (928)-999-22-22";
$_SESSION["tester"]["phone"][3]="+7 (938)-999-33-33";
$_SESSION["user_id"] = "5643266d200c";
// Переменная $_SESSION //
$_SETT=array();
$_SETT["header"]="Заголовок";
$_SETT["template"]="template.php";
$_SETT["variables"]["var1"]="Значение 1";
$_SETT["variables"]["var2"]="Значение 2";
$_SETT["variables"]["var3"][0]="Значение 3-0";


$_SESSION["settings"]=$_SETT;

// Переменная $_GET //
$_GET["mode"]="getmode";
//$_GET["field"][$Item["sub"]]="Гет1";
//$_GET[$Item["get"]]["sub2"]="Гет2";
$_GET["field"]["sub3"]="Гет3";
$_GET["form"] = "get-form";									// добавил
$_GET["mode"] = "get-mode";									// добавил
$_GET["item"] = "get-item";									// добавил

// Переменная $_POST //
$_POST["mode"]="postmode";
//$_POST["field"][$Item["sub"]]="Пост1";
//$_POST[$Item["post"]]["sub2"]="Пост2";
$_POST["field"]["sub3"]="Пост3";

// Переменная $_COOKIE //
$_COOKIE["mode"]="postmode";
//$_COOKIE["field"][$Item["sub"]]="Кука1";
//$_COOKIE[$Item["cook"]]["sub2"]="Кука2";
$_COOKIE["field"]["sub3"]="Кука33";

$context = array(
	"var42" => 42,
	"text" => 'text1',
	"_ndx" => 1,
	"obj" => (object)array("f1" => "f1val", "o1" => (object)array("f2"=>"f2val")),
	"array" =>	array(1,2,
					array(3,4,5,
						array(6,7,8)))
);

$context["%visits"][1] = "_NDX";

$context["field"]["sub"][0]["sub1"]["sub2"]["value"]="test";
$context["field3"]["sub"]="test";

$context["multifield"] = array("fld1"=>"test1","fld2"=>"test2");
$context["var"][0] = "fld2";
$context[0] = "Zero";


$context["fld10_1"] = 3;
$context["fld10_2"] = 5;
$context["fld10_3"] = "test";

$context["array"][42]["value"] = "text";
$context["array"][42][1][2][3][4][5][6] = "text";
$context["array"]["value"][1][2][3][4][5][6] = "text";

$_SESSION["lang"]='ru';
$context["%lang"]["ru"]["name"]='Russian';
$context["lang"]["ru"]["data"]["text"]='Some Russian conent';
$context["%_table"] = "table";

$context["data"]["lang"]["ru"]["name"]="Russian";

//{{data[lang1][{{_SESS[lang1]}}][name]}}

$_ENV = array("1" => 111111);
$_ENV["variables"]["prop"] = "prop";

$context["%tpllist"]=array("test1","test2","test3");
$context["_idx"]=1;


$exprs = array(
	'<option value="{{%tpllist[{{_idx}}]}}">{{%tpllist[{{_idx}}]}}</option>' => '<option value="test2">test2</option>',
	'<script>$("body").append("<div>");</script>' => '<script>$("body").append("<div>");</script>',
	'{{0}}' => 'Zero',
	'{{var42}}' => '42',
	'{{ 42 + 2 * 3 - 6}}' => '42',
	'{{ unknownFN() }}' => '{{ unknownFN() }}',
	'{{ today() }}' => '[CANT CHECK]',
	'{{ date("Y-m-d") }}' => '[CANT CHECK]',
	'{{ date("d.m.Y",strtotime("now +1 month")) }}' => '[CANT CHECK]',
	'{{ array[1] }}' => '2',
	'{{ array[2][1] }}' => '4',
	'{{ array.1 }}' => '2',
	'{{ array[2][3][1] }}' => '7',
	'{{ array.2.3.1 }}' => '7',
	'{{ array.2.3.1 }}' => '7',
	'{{ obj.f1 }}' => 'f1val',
	'{{%visits[{{_ndx}}]}}' => '_NDX',
	'{{ obj.o1.f2 }}' => 'f2val',
	'{{ myfunc("1", "2", "3", "4", "5") }}' => 'myfunc(1, 2, 3, 4, 5)',
	'{{ myfunc(1+2) }}' => 'myfunc(3)',
	'text|{{var42 + {{var2}}}}|text|{{var42}}|text{not_expr}'
		 => 'text|{{var42 + {{var2}}}}|text|42|text{not_expr}',
	'text|{{var42 + {{var42}}}}|text|{{var42}}|text{not_expr}'
		 => 'text|84|text|42|text{not_expr}',
	'{{array.42}}' => '{"value":"text"}',
	'{{array.42[value]}}' => 'text',
	'{{array.42}}' => '{"value":"text","1":{"2":{"3":{"4":{"5":{"6":"text"}}}}}}',
	'{{array.42.value}}' => 'text',
	'{{array.42[value]}}' => 'text',
	'{{array.value.1.2.3.4.5.6}}' => 'text',
	'{{array.42.value->strlen(@)}}' => '4',
	'{{array.42[value]->strlen(@)}}' => '4',
	'3. записано как {{field3[sub]}} либо {{field3.sub}} и должно отобразить значение test'
		 => '3. записано как test либо test и должно отобразить значение test',
	'4. {{field.sub[0][sub1].sub2.value}} - вернёт test' => '4. test - вернёт test',
	'4. {{field[sub][0].sub1.sub2.value}} - вернёт test' => '4. test - вернёт test',
//	'5. Сессия: {{_SESSION}}'
//		 => '5. Сессия: {"user":{"0":"tester","role":"moder"},"tester":{"phone":{"1":"+7 (918)-999-11-11","2":"+7 (928)-999-22-22","3":"+7 (938)-999-33-33"}},"user_id":"5643266d200c","settings":{"header":"\u0417\u0430\u0433\u043e\u043b\u043e\u0432\u043e\u043a","template":"template.php","variables":{"var1":"\u0417\u043d\u0430\u0447\u0435\u043d\u0438\u0435 1","var2":"\u0417\u043d\u0430\u0447\u0435\u043d\u0438\u0435 2","var3":["\u0417\u043d\u0430\u0447\u0435\u043d\u0438\u0435 3-0","\u0417\u043d\u0430\u0447\u0435\u043d\u0438\u0435 3-1"]}},"lang":"ru"}',
	'5. Окружение: {{_ENV.1}}' => '5. Окружение: 111111',
	'5. Окружение: {{_ENV[1]}}' => '5. Окружение: 111111',
	'5. {{_VAR["prop"]}}' => '5. prop',
	'6. Запись в шаблоне {{multifield[fld1]}} возвращает test1' => '6. Запись в шаблоне test1 возвращает test1',
	'6. Запись в шаблоне {{multifield}} должна вернуть {"fld1":"test1","fld2":"test2"}'
		 => '6. Запись в шаблоне {"fld1":"test1","fld2":"test2"} должна вернуть {"fld1":"test1","fld2":"test2"}',
	'7. {{multifield[{{var.0}}]}} вернёт test2' => '7. test2 вернёт test2',
	'8. {{count({{multifield}})}} вернёт 2' => '8. 2 вернёт 2',
	'9. {{ {{fld10_1}} + {{fld10_2}} }} вернёт 8' => '9. 8 вернёт 8',
	'9. {{ {{fld10_3}} + {{fld10_1}} }} вернёт test3' => '9. test3 вернёт test3',
	'8. {{multifield->count()}} вернёт 2' => '8. 2 вернёт 2',
	'8. {{multifield->count(@)}} вернёт 2' => '8. 2 вернёт 2',
	'8. {{ var42->id(@) + var42->id(@) }}' => '8. 84',
	'9. {{ {{fld10_3->strlen()}} + {{fld10_2}} }} вернёт 9' => '9. 9 вернёт 9',
	'9. {{ ({{fld10_3->strlen()}} + {{fld10_2}})/3 }} вернёт 3' => '9. 3 вернёт 3',
	'9. {{ text->substr(@, 0, 5) + "|Some text|" + multifield.fld2 -> substr(@, 0, 5) }}' => '9. text1|Some text|test2',
	'{{_SESS["lang"]}}' => 'ru',
	'{{%lang[{{_SESS[lang]}}].name}}' => 'Russian',
	'{{lang["ru"]}}' => '{"data":{"text":"Some Russian conent"}}',
	'{{lang[{{_SESS[lang]}}].data.text}}' => 'Some Russian conent',
	'{{%_table}}'=>'table',
	'{{NOT_EXISTENT_VAR}}'=>'{{NOT_EXISTENT_VAR}}',
	'{{var42->id(@)*2}}'=>'84',
	'{{ {{_ENV->count()}} * 2 }}' => '4',
	'{{_ENV->count() * 2 }}' => '4',
	'{{data[lang][{{_SESS[lang]}}][not_exists]}}' => '{{data[lang][{{_SESS[lang]}}][not_exists]}}',
);

$processor = new WEProcessor($context);

// print("Context:\n");
// print_r($context);
// print_r($_ENV);

print("Running tests...\n");
$index = 0;
$failed = 0;
$total = 0;
$passed = 0;
foreach($exprs as $expr=>$expected) {
	$substituted = $processor->substitute($expr);

	$printResult = function($status, $suffix) {
		global $failed;
		global $index;
		global $passed;

		if ($status !== "OK") {
			print("[" . str_pad($index + 1, 2)  . "] " . str_pad($status, 6) . " " . $suffix);
			if ($status === "FAILED") {
				print("\n");
				$failed += 1;
			}
			$index += 1;
		} else {
			$passed += 1;
		}
	};

	if ($substituted == $expected || $expected == '[CANT CHECK]' && $expr != $substituted) {
		$printResult("OK", "'$expr''\n");
	} elseif ($expected != '[CANT CHECK]') {
		$printResult("FAILED", "\n     EXPR:     '$expr'\n     EXPECTED: '$expected'\n     GOT       '$substituted'\n");
	} else {
		$printResult("FAILED", "\n     EXPR:     '$expr'\n     GOT       '$substituted'\n");
	}

	$total += 1;
}
print("Done\n\n");

print("PASSED: " . $passed . "\n");
print("FAILED: $failed\n");
print("TOTAL:  $total\n");
print("\n");

// $xhprof_data = xhprof_disable();

// include_once(__DIR__."/../../xhprof/xhprof_lib/utils/xhprof_lib.php");
// include_once(__DIR__."/../../xhprof/xhprof_lib/utils/xhprof_runs.php");
// $xhprof_runs = new XHProfRuns_Default("/tmp");
// $run_id = $xhprof_runs->save_run($xhprof_data, "weprocessor");
// echo "Report: http://localhost:88/xhprof/xhprof_html/index.php?run=$run_id&source=weprocessor";
// echo "\n";
?>
