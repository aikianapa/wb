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
		if ($this->failedEval) return null;

		if ($this->debug) print("##add('". json_encode($a1) ."', '". json_encode($a2) ."') -> ");
		if (is_string($a1)) $res = $a1 . $a2;
		else $res =  $a1 + $a2;
		if ($this->debug) print("$res##\n");

		return $res;
	}

	public function setLet($v) {
		if ($this->failedEval) return null;

		if ($this->debug) print("##setLet('" . json_encode($v) . "')\n");
		$this->let = $v;
	}

	public function getLet() {
		if ($this->failedEval) return null;

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
		if ($this->failedEval) return null;

		if ($this->debug) print("##set_variable($v, $e)\n");
		$this->context[$v] = $e;
	}

	public function get_variable($v) {
		if ($this->failedEval) return null;

		if ($this->debug) print("##get_variable(\n  '".json_encode($v)."'\n) ->\n  ");
		if ((array)$v === $v) {
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
					elseif (isset($_ENV['lang'])) $res = $_ENV["lang"];
					else $res = 'eng';
					$ctx = $this->context;
					if ((array)$ctx === $ctx AND isset($ctx["_global"]) AND $ctx["_global"]==false ) {
						$res = $ctx[$res];
					} else {
						$res = $_ENV["locale"][$res];
					}
					break;
				}
				case '_ENV':		$res = $_ENV;  break;
				case '_REQ':		$res = $_REQUEST; break;
				case '_REQUEST':	$res = $_REQUEST; break;
				case '_GET': 		$res = $_GET; break;
				case '_POST':		$res = $_POST; break;
				case '_CURRENT':	$res = $this->context; break;
				case '_MODE': 		$res = $_ENV["route"]["mode"]; break;
				case '_FORM': 		$res = $_ENV["route"]["form"]; break;
				case '_ITEM': 		$res = $_ENV["route"]["item"]; break;
				default: {
					$this->evalFail("UNKNOWN VARIABLE: '".json_encode($v)."'");
					$res = null;
					break;
				}
			}
		}
		if ($this->debug) print("'" . json_encode($res) . "'##\n");

		return $res;
	}

	public function call_fn($name, $args) {
		if ($this->failedEval) return null;

		if ($this->debug) print("##call_fn($name, '".json_encode($args)."') -> ");
		$res = null;

		switch ($name) {
			// если нужно реализовать некую новую функцию то пишем соответствующую ветку в этом свитче
			case "today": 	$res = date("Y-m-d", time()); break;
			case "myfunc":	$res = "myfunc(" . join($args, ", ") . ")"; break;
			case "id": 		$res = $args[0];break;
			// пытаемся вызвать существующую функцию напрямую, как есть.
			// если не срабатывает - нужно определить ветку в этом свитче с необходимым преобразованием аргументов
			default: {
				try {
					if ((array)$args === $args) {
						if (empty($args)) {
							// есть специальный случай. если мы вызываем функцию, у которой
							$requiredParams = count(array_filter((new ReflectionFunction($name))->getParameters(), function($p) { return !$p->isOptional(); }));

							if ($requiredParams == 1 && isset($this->let)) {
								$res = call_user_func($name, $this->let);
							} elseif ($requiredParams == 0) {
								$res = call_user_func($name);
							} else {
								$this->evalFail("fn '$name': requires '$requiredParams' arguments and @ is not set");
								$res = null;
							}
						} else {
							$res = call_user_func_array($name, array_values($args));
						}
					} else {
						$res = call_user_func($name, $args);
					}
				} catch (ReflectionException $e) {
					$this->evalFail($e->getMessage());
					$res = null;
				}
				break;
			}
		}

		if ($this->debug) print("'".$res."'##\n");
		return $res;
	}

	public function call_index($name, $args) {
		if ($this->failedEval) return null;

		if ($this->debug) print("##call_index(\n  '".json_encode($name)."',\n  '".json_encode($args)."'\n) ->\n  ");
		$var = $this->get_variable($name);
		if ($this->failedEval) return null;

		if (is_null($args) || is_null($var)) {
			$this->evalFail("args: '". json_encode($args) ."', var: '".json_encode($var)."'");
			$var = null;
		} else if ((array)$args === $args) {
			foreach ($args as $idx) {
				if ((array)$var === $var && isset($var[$idx])) {
					$var = $var[$idx];
				} else {
					$this->evalFail("var: '". json_encode($var) ."', idx: '".json_encode($idx)."'");
					$var = null;
					break;
				}
			}
		} else {
			$idx = $args;
			if ((array)$var === $var && isset($var[$idx])) {
				$var = $var[$idx];
			} else {
				$this->evalFail("var: '". json_encode($var) ."', idx: '".json_encode($idx)."'");
				$var = null;
			}
		}
		if ($this->debug) print("'". json_encode($var) ."'##");
		return $var;
	}

	public function call_field($obj, $args) {
		if ($this->failedEval) return null;

		if ($this->debug) print("##call_field('".json_encode($obj)."', '".json_encode($args)."')\n");
		if (is_object($obj)) {
			if ($this->debug) print("OBJECT '". json_encode($obj) ."'\n");
			if ((array)$args === $args) {
				foreach ($args as $idx) {
					if ($this->debug) print("idx: '" . json_encode($idx) . "'\n");
					$obj = $obj->$idx;
				}
			} else {
				if ($this->debug) print("idx: '" . json_encode($args) . "'\n");
				$obj = $obj->$args;
			}
			return $obj;
		} elseif ((array)$obj === $obj) {
			if ($this->debug) print("ARRAY '". json_encode($obj) ."'\n");
			if ((array)$args === $args) {
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
			$this->evalFail("Left must be Array or Object, got '". json_encode($obj) ."' with args='" . json_encode($args) . "'");
			return null;
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
				if ((array)$res === $res) return json_encode($res);
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

	private function evalFail($message = "") {
		if ($this->debug && !$this->failedEval) {
			print "\n###evalFail(\n  '$$message'\n)\n";
			debug_print_backtrace();
		}
		$this->failedEval = true;
	}
	private function evalReset() {
		$this->failedEval = false;
	}

}

?>
