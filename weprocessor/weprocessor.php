<?php

class WEProcessor {
	protected $context;
	protected $parser;
	private $let;
	private $debug=false;
	private $failedEval = false;

	// определяем названия teriminals и их регулярки
	// названия должны совпадать с тем что используем в грамматике (web.lime)
	protected $termsMap = array(
		array('EXT_NUM', '/^[0-9]+(\.[0-9]+)?/'),
		array('EXT_STR', '/^"[^"]*"/'),
		array('EXT_VAR', '/^%*[A-Za-z_]\w*/'),
		array('EXT_START', '/^\{\{/'),
		array('EXT_END', '/^\}\}/'),
		array('EXT_LET', '/^-\>/')
	);

	public function __construct($context) {
		include_once "parse_engine.php";
		include_once "weparser.class";
		$this->context = $context;
		$this->parser = new parse_engine(new weparser($this));
	}

	public function add($a1, $a2) {
		if ($this->debug) print("##add('". json_encode($a1) ."', '". json_encode($a2) ."') -> ");
		if (is_string($a1)) $res = $a1 . $a2;
		else $res =  $a1 + $a2;
		if ($this->debug) print("$res##\n");

		return $res;
	}

	public function setLet($v) {
		if ($this->debug) print("##setLet('" . json_encode($v) . "')\n");
		$this->let = $v;
	}

	public function getLet() {
		if ($this->debug) print("##getLet()='" . json_encode($this->let) . "'\n");
		if (isset($this->let)) {
			$let = $this->let;
			unset($this->let);
		} else {
			$let = null;
		}
		return $let;
	}

	public function set_variable($v, $e) {
		if ($this->debug) print("##set_variable($v, $e)\n");
		$this->context[$v] = $e;
	}

	public function get_variable($v) {
		if ($this->debug) print("##get_variable(\n  '".json_encode($v)."'\n) ->\n  ");
		if (is_array($v)) {
			$res = $v;
		} elseif (@isset($this->context[$v])) {
			$res = $this->context[$v];
		} else {
			switch (strtoupper($v)) {
				case '_SETT': 		$res = $_ENV["settings"]; break;
				case '_SETTINGS': 	$res = $_ENV["settings"]; break;
				case '_SESS': 		$res = $_SESSION; break;
				case '_SESSION': 	$res = $_SESSION; break;
				case '_VAR': 		$res = $_ENV["variables"]; break;
				case '_SRV':		$res = $_SERVER; break;
				case '_SERVER':		$res = $_SERVER; break;
				case '_COOK':		$res = $_COOKIE; break;
				case '_COOKIE':		$res = $_COOKIE; break;
				case '_LANG': {
					if (isset($_SESSION['lang'])) $res = $_SESSION["lang"];
					elseif (isset($_ENVp['lang'])) $res = $_ENV["lang"];
					else $res = 'eng';
					if (is_array($this->context) AND isset($this->context["_global"]) AND $this->context["_global"]==false ) {
						$this->context = $this->context[$res];
					} else {
						$this->context = $_ENV["locale"];
					}
					break;
				}
				case '_ENV':		$res = $_ENV;  break;
				case '_REQ':		$res = $_REQUEST; break;
				case '_REQUEST':	$res = $_REQUEST; break;
				case '_GET': 		$res = $_GET; break;
				case '_POST':		$res = $_POST; break;
				case '_CURRENT':	$res = $this->context; break;
				default: {
					if ($this->debug) print(" UNKNOWN VARIABLE: '".json_encode($v)."'");
					$this->evalFail();
					$res = null;
					break;
				}
			}
		}
		if ($this->debug) print("'" . json_encode($res) . "'##\n");

		return $res;
	}

	public function call_fn($name, $args) {
		if ($this->debug) print("##call_fn($name, '".json_encode($args)."') -> ");

		switch ($name) {
			// если нужно реализовать некую новую функцию то пишем соответствующую ветку в этом свитче
			case "today":  $res = date("Y-m-d", time()); break;
			case "myfunc": $res = "myfunc(" . join($args, ", ") . ")"; break;
			case "id": {
				$res = $args[0];
				break;
			}
			// пытаемся вызвать существующую функцию напрямую, как есть.
			// если не срабатывает - нужно определить ветку в этом свитче с необходимым преобразованием аргументов
			default: {
				if (is_array($args)) {
					if (empty($args)) {
						// есть специальный случай. если мы вызываем функцию, у которой
						$requiredParams = count(array_filter((new ReflectionFunction($name))->getParameters(), function($p) { return !$p->isOptional(); }));

						if ($requiredParams == 1 && isset($this->let)) {
							$res = call_user_func($name, $this->let);
						} elseif ($requiredParams == 0) {
							$res = call_user_func($name);
						} else {
							$this->evalFail();
							$res = "[can't call '$name': requires '$requiredParams' arguments and @ is not set]";
						}
					} else {
						$res = call_user_func_array($name, array_values($args));
					}
				} else {
					$res = call_user_func($name, $args);
				}
				break;
			}
		}

		if ($this->debug) print("'".$res."'##\n");
		return $res;
	}

	public function call_index($name, $args) {
		if ($this->debug) print("##call_index(\n  '".json_encode($name)."',\n  '".json_encode($args)."'\n) ->\n  ");
		$var = $this->get_variable($name);
		if (is_null($args) || is_null($var)) {
			$this->evalFail();
			$var = null;
		} else if (is_array($args)) {
			foreach ($args as $idx) {
				if (is_array($var) && isset($var[$idx])) {
					$var = $var[$idx];
				} else {
					$this->evalFail();
					$var = null;
					break;
				}
			}
		} else {
			if (is_array($var) && isset($var[$args])) {
				$var = $var[$args];
			} else {
				$this->evalFail();
				$var = null;
			}
		}
		if ($this->debug) print("'". json_encode($var) ."'##");
		return $var;
	}

	public function call_field($obj, $args) {
		if ($this->debug) print("##call_field('".json_encode($obj)."', '".json_encode($args)."')\n");
		if (is_object($obj)) {
			if ($this->debug) print("OBJECT '". json_encode($obj) ."'\n");
			if (is_array($args)) {
				foreach ($args as $idx) {
					if ($this->debug) print("idx: '" . json_encode($idx) . "'\n");
					$obj = $obj->$idx;
				}
			} else {
				if ($this->debug) print("idx: '" . json_encode($args) . "'\n");
				$obj = $obj->$args;
			}
			return $obj;
		} elseif (is_array($obj)) {
			if ($this->debug) print("ARRAY '". json_encode($obj) ."'\n");
			if (is_array($args)) {
				foreach ($args as $idx) {
					if ($this->debug) print("idx: '" . json_encode($idx) . "'\n");
					$obj = $obj[$idx];
				}
			} else {
				if ($this->debug) print("\n args: '$args'\n");
				foreach (explode(".", $args) as $idx) {
					if ($this->debug) print("idx: '" . json_encode($idx) . "'\n");
					$obj = $obj[$idx];
				}
			}
			return $obj;
		} else {
			$this->evalFail();
			return "call_field: Left must be Array or Object, got '". json_encode($obj) ."' with args='" . json_encode($args) . "'";
		}
	}

	protected function tokenize($line) {
		$out = array();
		while (strlen($line)) {
			$line = trim($line);
			$found = false;
			foreach ($this->termsMap as list($term, $regExp)) {
				if (preg_match($regExp, $line, $regs)) {
					$reg = trim($regs[0], '"');
					$out[] = array($reg, $term);
					$line = substr($line, strlen($regs[0]));
					$found = true;
					break;
				}
			}

			if (!$found) {
				$out[] = array($line[0], "");
				$line = substr($line, 1);
			}
		}
		return $out;
	}

	function calculate($expr) {
		unset($let);
		$this->evalReset();

		if (!strlen($expr)) return;
		try {
			$this->parser->reset();
			foreach($this->tokenize($expr) as list($t, $type)) {
				if ($this->debug) print("tokenize: '$t' : '$type'\n");
				if ($type == "") $this->parser->eat("'$t'", null);
				else $this->parser->eat($type, $t);
			}
			$this->parser->eat_eof();
			$res = $this->parser->semantic;
			if ($this->failedEval || !isset($res)) {
				return $expr;
			} else {
				if (is_array($res)) return json_encode($res);
				else return $res;
			}
		} catch (parse_error $e) {
			$this->evalReset();
			if ($this->debug) print($e->getMessage()."\n");
			return $expr;
		}
	}

	function substitute($text) {
		$parts = preg_split('/\{\{(?>[^\{\{\}\}]|(?R))*\}\}/', $text, -1, PREG_SPLIT_OFFSET_CAPTURE);

		$partsN = count($parts);
		$substituted = '';
		for ($i = 0; $i < $partsN; $i++) {
			$txt = $parts[$i][0];
			$start = $parts[$i][1] + strlen($txt);
			if ($i < $partsN - 1) {
				$end = $parts[$i+1][1];
			} else {
				$end = strlen($text);
			}

			$substituted = $substituted.$txt;
			$expr = substr($text, $start, $end - $start);
			if (strlen($expr) > 0) {
				$substituted = $substituted.$this->calculate($expr);
			}
		}

		return $substituted;
	}

	private function evalFail() {
		if ($this->debug) {
			print "evalFail()\n";
			debug_print_backtrace();
		}
		$this->failedEval = true;
	}
	private function evalReset() {
		$this->failedEval = false;
	}
}

?>
