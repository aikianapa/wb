<?php
/**
 * ki is a simple HTML/BBCode/XML DOM component.
 * Based on CDom parser
 * Based on PHP Simple HTML DOM Parser.
 * Licensed under the MIT License.
 *
 * @link https://github.com/amal/CDom/
 * @link http://simplehtmldom.sourceforge.net/
 *
 * @modifier Oleg Frolov <oleg_frolov at mail.ru>
 * @license MIT
 */


// Steart Router Class

final class wbRouter {

/*
$routes = array(
  // 'url' => 'контроллер/действие/параметр1/параметр2/параметр3'
  '/' => 'MainController/index', // главная страница
  '/(p1:str)(p2:num)unit(p3:any).htm' => '/show/page/$1/$2/$3', // главная страница
  '/contacts' => 'MainController/contacts', // страница контактов
  '/blog' => 'BlogController/index', // список постов блога
  '/blog/(:num)' => 'BlogController/viewPost/$1', // просмотр отдельного поста, например, /blog/123
  '/blog/(:any)/(:num)' => 'BlogController/$1/$2', // действия над постом, например, /blog/edit/123 или /blog/dеlete/123
  '/(:any)' => 'MainController/anyAction' // все остальные запросы обрабатываются здесь
);

// добавляем все маршруты за раз
wbRouter::addRoute($routes);

// а можно добавлять по одному
wbRouter::addRoute('/about', 'MainController/about');
echo "<br><br>";
// непосредственно запуск обработки
print_r(wbRouter::getRoute());
*/

  public static $routes = array();
  private static $params = array();
  private static $names = array();
  public static $requestedUrl = '';


   // Добавить маршрут
  public static function addRoute($route, $destination=null) {
    if ($destination != null && !is_array($route)) {
      $route = array($route => $destination);
    }
    self::$routes = array_merge(self::$routes, $route);
  }

   // Разделить переданный URL на компоненты
  public static function splitUrl($url) {
    return preg_split('/\//', $url, -1, PREG_SPLIT_NO_EMPTY);
  }

   // Текущий обработанный URL
  public static function getCurrentUrl() {
    return (self::$requestedUrl?:'/');
  }

   // Обработка переданного URL
  public static function getRoute($requestedUrl = null) {
		// Если URL не передан, берем его из REQUEST_URI
		if ($requestedUrl === null) {
			$request=explode('?', $_SERVER["REQUEST_URI"]);
			$uri = reset($request);
			$requestedUrl = urldecode(rtrim($uri, '/'));
		}
		self::$requestedUrl = $requestedUrl;
      // если URL и маршрут полностью совпадают
      if (isset(self::$routes[$requestedUrl])) {
        self::$params = self::splitUrl(self::$routes[$requestedUrl]);
        self::$names[] = "";
        return self::returnRoute();
      }
      foreach (self::$routes as $route => $uri) {
        // Заменяем wildcards на рег. выражения
        $name=null;		self::$names=array();
        $route=str_replace(" ","",$route);
        if (strpos($route, ':') !== false) {
			// Именование параметров
			preg_match_all("'\((\w+):(\w+)\)'",$route,$matches);
			if (isset($matches[1])) {
				foreach($matches[1] as $name) {
					$route=str_replace("(".$name.":","(:",$route);
					self::$names[] = $name;
				}
			}
			$route = str_replace('(:any)', '(.+)', str_replace('(:num)', '([0-9]+)', str_replace('(:str)', '(.[a-zA-Z]+)', $route)));
        }
        if (preg_match('#^'.$route.'$#', $requestedUrl)) {
          if (strpos($uri, '$') !== false && strpos($route, '(') !== false) {
            $uri = preg_replace('#^'.$route.'$#', $uri, $requestedUrl);
          }
          self::$params = self::splitUrl($uri);
          break; // URL обработан!
        }
      }
		return self::returnRoute();
  }

	// Сборка ответа
  public static function returnRoute() {
	$_GET=array();
	$_ENV["route"]=array();
    $_ENV["route"]["uri"]=$_SERVER["REQUEST_URI"];
    $controller="form"; $action="mode";

    $form = isset(self::$params[0]) ? self::$params[0]: 'default_form';
    $mode = isset(self::$params[1]) ? self::$params[1]: 'default_mode';
	foreach(self::$params as $i => $param) {
		if (strpos($param, ':')) {
			$tmp=explode(":",$param);
			$_ENV["route"][$tmp[0]]=$tmp[1];
		} else {
			if ($i==0) {$_ENV["route"]["controller"]=$param;}
			if ($i==1) {$_ENV["route"]["mode"]=$param;}
			if ($i>1) {$_ENV["route"]["params"][]=$param;}
			if (isset(self::$names[$i])) {
				$_ENV["route"]["params"][self::$names[$i]]=$param;
			}
		}


	}
	$tmp=explode("?",$_SERVER["REQUEST_URI"]);
	if (isset($tmp[1])) {
    parse_str($tmp[1],$get);
    if (!isset($_ENV["route"]["params"])) {$_ENV["route"]["params"]=array();}
    $_ENV["route"]["params"]=(array)$_ENV["route"]["params"]+(array)$get;
  }
	$_GET=array_merge($_GET,$_ENV["route"]);
	if (isset($_GET["engine"]) && $_GET["engine"]=="true") {$_SERVER["SCRIPT_NAME"]="/engine".$_SERVER["SCRIPT_NAME"];}
	if (isset($_SERVER["SCHEME"]) && $_SERVER["SCHEME"]>"") {$scheme=$_SERVER["SCHEME"];} else {$scheme="http";}
	$_ENV["route"]["scheme"]=$scheme;
	$_ENV["route"]["host"]=$_SERVER["HTTP_HOST"];

    if ($form=='default_form' && $mode='default_mode' && $_SERVER["QUERY_STRING"]>"") {
		parse_str($_SERVER["QUERY_STRING"],$_GET);
		$_ENV["route"]=array("scheme"=>$scheme,"host"=>$_SERVER["HTTP_HOST"],"controller"=>$controller,$controller=>$_GET["form"], $action=>$_GET["mode"] , "params"=>$_GET);
	}

    return $_ENV["route"];
  }

}

// End Router Class


// Basic lexer functionality
abstract class CLexer
{
	const CHARSET = 'UTF-8';
	const CHARS_SPACE = " \n\t";
	protected $pos = 0;
	protected $chr;
	protected $length = 0;
	protected $string;

	protected function cleanString(&$string) {
		$string = trim($string);
		if (strpos($string, "\r") !== false) {
			$string = str_replace(array("\r\n", "\r"), "\n", $string);
		}
		$string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+|%0[0-8bcef]|%1[0-9a-f]/SXu', '', $string);
	}

	protected function movePos($n = 1)	{
		return $this->chr = (($this->pos += $n) < $this->length) ? $this->string[$this->pos] : null;
	}

	protected function skipChars($mask)	{
		$this->pos += strspn($this->string, $mask, $this->pos);
		$this->chr = ($this->pos < $this->length) ? $this->string[$this->pos] : null;
	}

	protected function getChars($mask)	{
		$pos = $this->pos;
		$len = strspn($this->string, $mask, $pos);
		$this->pos += $len;
		$this->chr = ($this->pos < $this->length) ? $this->string[$this->pos] : null;
		if ($len === 0) {
			return '';
		}
		return substr($this->string, $pos, $len);
	}

	protected function getUntilChars($mask)	{
		$pos = $this->pos;
		$len = strcspn($this->string, $mask, $pos);
		$this->pos += $len;
		$this->chr = ($this->pos < $this->length) ? $this->string[$this->pos] : null;
		if ($len === 0) {
			return '';
		}
		return substr($this->string, $pos, $len);
	}

	protected function getUntilString($string, &$res = false, $skipIfNotFound = false)	{
		// End of input
		if ($this->chr === null) {
			$res = false;
			return '';
		}

		// Not found, end of input
		if (($pos = strpos($this->string, $string, $this->pos)) === false) {
			$res = false;
			if ($skipIfNotFound) {
				return '';
			}
			$ret = substr($this->string, $this->pos);
			$this->chr = null;
			$this->pos = $this->length;
			return $ret;
		}

		$res = true;

		// Found in current position
		if ($pos === $this->pos) {
			return '';
		}

		// Found
		$pos_old = $this->pos;
		$this->chr = $this->string[$pos];
		$this->pos = $pos;

		return substr($this->string, $pos_old, $pos - $pos_old);
	}

	protected function getUntilCharEscape($char, &$res = false, $skipIfNotFound = false)	{
		$res = false;
		// End of input
		if ($this->chr === null) {
			return '';
		}
		$start = $this->pos;
		$unescape = false;
		do {
			if (($pos = strpos($this->string, $char, $start)) === false) {
				if ($skipIfNotFound) {
					return '';
				}
				$ret = substr($this->string, $this->pos, $this->length - $this->pos);
				$this->chr = null;
				$this->pos = $this->length;
				return $ret;
			}

			// Found in current position
			if ($pos === $this->pos) {
				$res = true;
				$this->chr = (($this->pos = $pos+1) < $this->length) ? $this->string[$this->pos] : null;
				return '';
			}

			// Escaping
			if ($this->string[$pos - 1] === '\\') {
				$start = $pos + 1;
				$unescape = true;
				continue;
			}

			$res = true;
			$pos_old = $this->pos;
			$str = substr($this->string, $pos_old, $pos - $pos_old);

			$this->chr = (($this->pos = $pos+1) < $this->length) ? $this->string[$this->pos] : null;

			// Unescape and return
			return $unescape ? str_replace("\\$char", $char, $str) : $str;
		} while (true);
	}
}

class ki extends CLexer
{
	const NODE_ELEMENT	 = 1;	// XML_ELEMENT_NODE
	const NODE_ATTRIBUTE = 2;	// XML_ATTRIBUTE_NODE
	const NODE_TEXT 	 = 3;	// XML_TEXT_NODE
	const NODE_CDATA	 = 4;	// XML_CDATA_SECTION_NODE
	const NODE_COMMENT	 = 8;	// XML_COMMENT_NODE
	const NODE_DOCUMENT	 = 9;	// XML_DOCUMENT_NODE
	const NODE_DOCTYPE	 = 14;	// XML_DTD_NODE
	const NODE_XML_DECL	 = 30;

	public static $selfClosingTags = array(
		'area'     => true,
		'base'     => true,
		'basefont' => true,
		'br'       => true,
		'embed'    => true,
		'hr'       => true,
		'image'    => true,
		'img'      => true,
		'input'    => true,
		'link'     => true,
		'meta'     => true,
		'param'    => true,
	);

	public static $inlineTags = array(
		'abbr'     => true,
		'acronym'  => true,
		'basefont' => true,
		'bdo'      => true,
		'big'      => true,
		'i'        => true,
		'br'       => true,
		'cite'     => true,
		'dfn'      => true,
		'em'       => true,
		'font'     => true,
		'input'    => true,
		'kbd'      => true,
		'q'        => true,
		's'        => true,
		'samp'     => true,
		'select'   => true,
		'small'    => true,
		'strike'   => true,
		'strong'   => true,
		'sub'      => true,
		'sup'      => true,
		'textarea' => true,
		'tt'       => true,
		'u'        => true,
		'var'      => true,
		'del'      => true,
		'ins'      => true,
	);

	public static $blockTags = array(
		'document'   => true,
		'address'    => true,
		'blockquote' => true,
		'center'     => true,
		'b'          => true,
		'a'        	 => true,
		'span'       => true,
		'div'        => true,
		'section'    => true,
		'label'      => true,
		'code'       => true,
		'fieldset'   => true,
		'form'       => true,
		'h1'         => true,
		'h2'         => true,
		'h3'         => true,
		'h4'         => true,
		'h5'         => true,
		'h6'         => true,
		'menu'       => true,
		'p'          => true,
		'pre'        => true,
		'table'      => true,
		'ol'         => true,
		'ul'         => true,
		'li'         => true,
		'applet'     => true,
		'button'     => true,
		'iframe'     => true,
		'object'     => true,
	);

	public static $optionalClosingTags = array(
		'tr'   => array(
			'tr' => true,
			'td' => true,
			'th' => true,
		),
		'th'   => array(
			'th' => true,
		),
		'td'   => array(
			'td' => true,
		),
		'li'   => array(
			'li' => true,
		),
		'dt'   => array(
			'dt' => true,
			'dd' => true,
		),
		'dd'   => array(
			'dd' => true,
			'dt' => true,
		),
		'dl'   => array(
			'dd' => true,
			'dt' => true,
		),
		'p'    => array(
			'p' => true,
		),
		'nobr' => array(
			'nobr' => true,
		),
		'h1'   => array(
			'h2' => true,
			'h3' => true,
			'h4' => true,
			'h5' => true,
			'h6' => true,
		),
		'h2'   => array(
			'h1' => true,
			'h3' => true,
			'h4' => true,
			'h5' => true,
			'h6' => true,
		),
		'h3'   => array(
			'h1' => true,
			'h2' => true,
			'h4' => true,
			'h5' => true,
			'h6' => true,
		),
		'h4'   => array(
			'h1' => true,
			'h2' => true,
			'h3' => true,
			'h5' => true,
			'h6' => true,
		),
		'h5'   => array(
			'h1' => true,
			'h2' => true,
			'h3' => true,
			'h4' => true,
			'h6' => true,
		),
		'h6'   => array(
			'h1' => true,
			'h2' => true,
			'h3' => true,
			'h4' => true,
			'h5' => true,
		),
	);

	public static $skipContents = array(
		//'script'=> true,
		//'style' => true,
	);

	public static $bracketOpen  = '<';
	public static $bracketClose = '>';
	public static $skipWhitespaces = true;
	public static $skipComments = false;
	public static $charsetDetection = true;
	public static $debug = false;

	protected $root;
	protected $parent;
	protected $last;

	public static function fromString($markup = '', $parent = null)
	{
		$lexer = new self($markup, $parent);
		return $lexer->root;
	}

	protected function __construct($markup = '', $parent = null)
	{
		$this->debug('Loading markup');

		if (self::$charsetDetection) {
			$tmp=substr($markup,0,1000);
			$this->detectCharset($tmp);
		}

		$this->cleanString($markup);

		if ($parent === null) {
			$parent = new kiDocument;
			$parent->ownerDocument = $parent;
		}
		$this->root = $parent;

		if ($markup === '') {
			return;
		}

		$this->string = &$markup;
		if (($this->length = strlen($markup)) > 0) {
			$this->chr = $this->string[0];
		}

		$this->debug('LEXER START => String (' . $this->length . ')');

		$this->parent = $parent;

		$this->parse();

		$this->parent = null;
	}

	public function fromFile($file="") {
		if ($file=="") {
			return ki::fromString("");
		} else {
			$context = stream_context_create(array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
					'content' => http_build_query($_POST),
				)
			));
            $fp = fopen ($file,"r");
            flock ($fp, LOCK_SH);
            $res=file_get_contents($file,false,$context);
            flock ($fp, LOCK_UN);
            fclose ($fp);
			return ki::fromString($res);
		}
	}

	public function file_get_html($file="") {
		return ki::fromString(wb_file_get_contents($file));
	}

	public function str_get_html($str="") {
		return ki::fromString($str);
	}

	protected function detectCharset(&$markup)
	{
		$requestedCharset = strtolower(self::CHARSET);

		$regex = '/<(?:meta\s+.*?charset|\?xml\s+.*?encoding)=(?:"|\')?\s*([a-z0-9 _-]+)(?<!\s)/SXsi';
		if (preg_match_all($regex, $markup, $match, PREG_OFFSET_CAPTURE)) {
			/** @var $replaceDocCharset array */
			$replaceDocCharset = $match[1];
			$docCharsets = array();
			foreach ($match[1] as $ch) {
				$ch = strtolower($ch[0]);
				$docCharsets[$ch] = $ch;
			}; unset($ch);
			$this->debug('Detecting charset settings in document: ' . join(', ', $docCharsets));
		} else {
			// is it UTF-8 ?
			// @link http://w3.org/International/questions/qa-forms-utf-8.html
			if (preg_match('~^(?:
			   [\x09\x0A\x0D\x20-\x7E]            # ASCII
			 | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
			 | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
			 | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
			 | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
			 | \xF0[\x90-\xBF][\x80-\xBF]{2}      # planes 1-3
			 | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
			 | \xF4[\x80-\x8F][\x80-\xBF]{2}      # plane 16
			)*$~DSXx', $markup)) {
				return;
			}
			$docCharsets = array();
			$replaceDocCharset = false;
		}

		if (!isset($docCharsets[$requestedCharset])) {
			$this->debug('Charset detection');
			// Document Encoding Conversion
			$docEncoding = false;
			$possibleCharsets = $docCharsets;
			$documentCharset = reset($docCharsets);
			if ($possibleCharsets && function_exists('mb_detect_encoding')) {
				$docEncoding = mb_detect_encoding($markup, $possibleCharsets);
				if ($docEncoding) {
					$docEncoding = strtolower($docEncoding);
				}
			}
			if (!$docEncoding) {
				array_unshift($possibleCharsets, $requestedCharset);
				$possibleCharsets[] = 'windows-1251';
				$possibleCharsets[] = 'koi8-r';
				$possibleCharsets[] = 'iso-8859-1';
				$possibleCharsets = array_unique($possibleCharsets);
				foreach ($possibleCharsets as $charset) {
					if ($markup === $this->convertCharset($markup, $charset, $charset)) {
						$docEncoding = $charset;
						break;
					}
				}; unset($charset);
				if (!$docEncoding) {
					if ($documentCharset) {
						// Ok trust the document
						$docEncoding = $documentCharset;
					} else {
						$this->debug("Can't detect document charset");
						return;
					}
				}
			}
			if ($docEncoding == 'ascii') {
				$docEncoding = $requestedCharset;
			}
			$this->debug("DETECTED '$docEncoding'");
			if ($documentCharset && $docEncoding !== $documentCharset) {
				$this->debug("Detected does not match what document says: $documentCharset");
			}
			if ($docEncoding !== $requestedCharset) {
				$this->debug("CONVERT $docEncoding => $requestedCharset");
				$markup = self::convertCharset($markup, $docEncoding, $requestedCharset);
			}
			if ($replaceDocCharset) {
				$offset = 0;
				$charsetLength = strlen($requestedCharset);
				foreach ($replaceDocCharset as $val) {
					$chLength  = strlen($val[0]);
					$beginning = substr($markup, 0, $val[1]+$offset);
					$ending    = substr($markup, $val[1] + $chLength+$offset);
					$markup = $beginning . $requestedCharset . $ending;
					$offset += $charsetLength-$chLength;
				}; unset($val);
			}
		}
	}

	public static function convertCharset($markup, $in_charset, $out_charset)
	{
//		if (function_exists('iconv')) {
//			$markup = iconv($in_charset, $out_charset.'//IGNORE', $markup);
//		} else {
			// @codeCoverageIgnoreStart
			$markup = mb_convert_encoding($markup, $out_charset, $in_charset);
			// @codeCoverageIgnoreEnd
//		}
		return $markup;
	}


	protected function parse()
	{
		$debug  = self::$debug;
		$curChr = &$this->chr;
		$curPos = &$this->pos;
		$last   = &$this->last;
		$parent = &$this->parent;
		$bo     = self::$bracketOpen;
		$prefix = '';

		do {
			// Text
			if ($curChr !== $bo && $curChr !== null) {
				$str = $prefix.$this->getUntilString($bo, $res);
			} else if ($prefix !== '') {
				$str = $prefix;
			} else {
				$str = '';
			}
			if ($str !== '') {
				if (self::$skipWhitespaces && trim($str) === '') {
					$str = ' ';
				}
				$debug && $this->debug('Parser: Text node ['.strlen($str).']');
				// Do not create consecutive text nodes
				if ($last instanceof kiNodeText && $last->parent->uniqId === $parent->uniqId) {
					$last->value .= $str;
				} else {
					$this->linkNodes(new kiNodeText($str));
				}
				$prefix = '';
			}

			// End of input
			if ($curChr === null) {
				$debug && $this->debug('Lexer: end of input');
				break;
			}

			$startPos = $curPos;
			$nextChar = $this->movePos();

			// Closing tag
			if ($nextChar === '/') {
				$debug && $this->debug('Lexer: Closing tag');
				if ($this->parseTagClose()) {
					continue;
				}
			}

			// DOCTYPE | Comment | CDATA
			else if ($nextChar === '!') {
				// Comment
				if (($nextCh = $this->movePos()) === '-') {
					$debug && $this->debug('Lexer: Comment');
					if ($this->parseComment()) {
						continue;
					}
				}
				// CDATA
				else if ($nextCh === '[') {
					$debug && $this->debug('Lexer: CDATA section');
					if ($this->parseCDATA()) {
						continue;
					}
				}
				// DOCTYPE
				else if ($nextCh === 'D' || $nextCh === 'd') {
					$debug && $this->debug('Lexer: DOCTYPE');
					if ($this->parseDoctype()) {
						continue;
					}
				}
			}

			// XML heading
			else if ($nextChar === '?') {
				$debug && $this->debug('Lexer: XML heading');
				if ($this->parseXMLDeclaration()) {
					continue;
				}
			}

			// Opening tag
			else {
				$debug && $this->debug('Lexer: Opening tag');
				if ($this->parseTag()) {
					// Skip contents as text
					if (isset(self::$skipContents[$parent->name])) {
						if (!$last->selfClosed) {
							$last->value = $this->getUntilString("$bo/$parent->name");
						}
					}
					continue;
				}
			}

			// Fail
			$debug && $this->debug('Lexer WARNING: Processing failed, fallback to text');
			$prefix = substr($this->string, $startPos, $curPos-$startPos);
		} while (true);
	}

	protected function linkNodes($node, $isChild = false)
	{
		$parent = $this->parent;

		$node->parent = $parent;
		$node->ownerDocument = $parent->ownerDocument;

		if ($isChild) {
			$chid = count($parent->children);
			$node->chid = $chid;
			if ($chid === 0) {
				$parent->firstChild = $node;
			} else {
				$prev = $parent->lastChild;
				$prev->next = $node;
				$node->prev = $prev;
			}
			$parent->children[$chid] = $node;
			$parent->lastChild = $node;
		}

		$cnid = count($parent->nodes);
		$node->cnid = $cnid;
		$parent->nodes[$cnid] = $node;

		$this->last = $node;
	}

	protected function parseXMLDeclaration()
	{
		// Check
		$pos = &$this->pos;
		if (substr_compare($this->string, 'xml', $pos+1, 3, true)) {
			$this->debug('Lexer ERROR: Incorrect XML declaration start');
			return false;
		}

		// Get content
		$this->movePos(4);
		$str = $this->getUntilString('?'.self::$bracketClose, $res, true);
		if (!$res) {
			$this->debug('Lexer ERROR: XML declaration ended incorrectly');
			return false;
		}

		// Node
		$this->debug('Parser: XML declaration node');

		$this->root->isXml = true;

		$this->linkNodes(new kiNodeXmlDeclaration($str));

		$this->movePos(2);

		return true;
	}

	protected function parseDoctype()
	{
		// Check
		$pos = &$this->pos;
		if (substr_compare($this->string, 'OCTYPE', $pos+1, 6, true)) {
			$this->debug('Lexer ERROR: Incorrect DOCTYPE start');
			return false;
		}
		// Get content
		$this->movePos(7);
		$str = $this->getUntilString(self::$bracketClose, $res, true);
		if (!$res) {
			$this->debug('Lexer ERROR: DOCTYPE ended incorrectly');
			return false;
		}
		// Node
		$this->debug('Parser: DOCTYPE node');

		if (stripos($str, 'xhtml') !== false) {
			$this->root->isXml = true;
		}
		$this->linkNodes(new kiNodeDoctype($str));
		$this->movePos();
		return true;
	}

	protected function parseTag()
	{
		$chr = &$this->chr;
		// Name
		if (strpos($chars_space = self::CHARS_SPACE, $chr) !== false) {
			$this->skipChars($chars_space);
		}
		$bc  = self::$bracketClose;
		$tag = $this->getUntilChars($bc.'/'.$chars_space);
		if ($tag == '') {
			$this->debug('Lexer ERROR: Tag name not found');
			return false;
		}

		// Attributes
		$attributes = $this->parseParameters();

		// We can get self-closing tag here
		if ($chr === '/') {
			$closed = true;
			$this->movePos();
			if (strpos($chars_space, $chr) !== false) {
				$this->skipChars($chars_space);
			}
		} else {
			$closed = false;
		}

		// Here we should get a closing parenthesis
		if ($chr !== $bc) {
			$this->debug('Lexer ERROR: Tag ended incorrectly');
			return false;
		}

		$node = new kiNodeTag($tag, $closed);
		$node->bracketOpen  = self::$bracketOpen;
		$node->bracketClose = $bc;
		if ($attributes) {
			$attributes->node = $node;
			$node->attributes = $attributes;
		}

		// Handle optional closing tags
		$tag_l = $node->name;
		$parent = &$this->parent;
		if (isset(self::$optionalClosingTags[$tag_l])) {
			while (isset(self::$optionalClosingTags[$tag_l][$parent->name])) {
				$parent = $parent->parent;
			}
		}

		// Block tags closes not closed inline tags.
		if (isset(self::$blockTags[$tag_l])) {
			while (isset(self::$inlineTags[$parent->name])) {
				$parent = $parent->parent;
			}
		}

		$this->debug('Parser: Tag node');

		$this->linkNodes($node, true);

		if (!$node->selfClosed) {
			$parent = $node;
		}

		$this->movePos();

		return true;
	}

	protected function parseTagClose()
	{
		$this->movePos();
		$chr = &$this->chr;
		if (strpos($chars_space = self::CHARS_SPACE, $chr) !== false) {
			$this->skipChars($chars_space);
		}

		// Name & check
		$tag = $this->getUntilString(self::$bracketClose, $res);
		if (!$res || $tag === '') {
			$this->debug('Lexer ERROR: Closing tag not found');
			return false;
		} else if (($pos = strpos($tag, self::$bracketOpen)) !== false) {
			$this->debug('Lexer ERROR: Malformed closing tag');
			$this->movePos(-(strlen($tag)-$pos));
			return false;
		}

		// Skip trash
		if (($pos = strcspn($tag, self::CHARS_SPACE)) !== strlen($tag)) {
			$tag = substr($tag, 0, $pos);
		}

		$parentName = $this->parent->name;
		$tagName = strtolower($tag);

		// Search for closing tag
		$skipping = false;
		if ($parentName !== $tagName) {
			if (isset(self::$optionalClosingTags[$parentName]) && isset(self::$blockTags[$tagName])) {
				$org_parent = $this->parent;
				while ($this->parent->parent && $this->parent->name !== $tagName) {
					$this->parent = $this->parent->parent;
				}
				if ($this->parent->name !== $tagName) {
					// restore original parent
					$this->parent = $org_parent;
					if ($this->parent->parent) {
						$this->parent = $this->parent->parent;
					}
					// Unexpected closing tag. Skipping.
					$skipping = true;
				}
			} else if ($this->parent->parent && isset(self::$blockTags[$tagName])) {
				$org_parent = $this->parent;
				while ($this->parent->parent && $this->parent->name !== $tagName) {
					$this->parent = $this->parent->parent;
				}
				if ($this->parent->name !== $tagName) {
					// restore original parent
					$this->parent = $org_parent;
					// Unexpected closing tag. Skipping.
					$skipping = true;
				}
			} else if ($this->parent->parent && $this->parent->parent->name === $tagName) {
				$this->parent = $this->parent->parent;
			} else {
				// Unexpected closing tag. Skipping.
				$skipping = true;
			}
		}

		if (!$skipping && $this->parent->parent) {
			$this->parent = $this->parent->parent;
		}

		$this->movePos();

		return true;
	}

	protected function parseComment()
	{
		// <!--   -->

		if ($this->movePos() !== '-' || !$this->movePos()) {
			$this->debug('Lexer ERROR: Incorrect comment start');
			return false;
		}
		// Get content
		$str = $this->getUntilString('--'.self::$bracketClose, $res);
		if (!$res) {
			$this->debug('Lexer ERROR: Comment ended incorrectly');
			return false;
		}
		$this->movePos(3);
		// Node
		if (!self::$skipComments) {
			$this->debug('Parser: Comment node');
			$this->linkNodes(new kiNodeCommment($str));
		}
		return true;
	}

	protected function parseCDATA()
	{
		// Check
		$pos = &$this->pos;
		if (substr_compare($this->string, 'CDATA[', $pos+1, 6)) {
			$this->debug('Lexer ERROR: Incorrect CDATA start');
			return false;
		}
		// Get content
		$this->movePos(7);
		$str = $this->getUntilString(']]' . self::$bracketClose, $res, true);
		if (!$res) {
			$this->debug('Lexer ERROR: CDATA ended incorrectly');
			return false;
		}
		// Node
		$this->debug('Parser: CDATA node');
		$this->linkNodes(new kiNodeCdata($str));
		$this->movePos(3);
		return true;
	}

	protected function parseParameters()
	{
		$attributes = null;
		$chr = &$this->chr;
		$bc = self::$bracketClose;
		$chars_space = self::CHARS_SPACE;
		$chars_end = '/' . $chars_space . $bc;
		$chars_eq = '=' . $chars_end;
		if (strpos($chars_space, $chr) !== false) {
			$this->skipChars($chars_space);
		}
		do {
			// Name
			if (($name = $this->getUntilChars($chars_eq)) === '') {	break;}
			if (strpos($chars_space, $chr) !== false) {	$this->skipChars($chars_space);	}
			if ($chr !== '=') {
				if ($attributes === null) {
					$attributes = new kiAttributesList;
				}
				$attributes->set($name, true);
				if ($chr === $bc || $chr === '/') {	break;}
				continue;
			}

			$this->movePos();
			if (strpos($chars_space, $chr) !== false) {	$this->skipChars($chars_space);	}

			// Value
			if ($chr === "'" || $chr === '"') {
				$quote = $chr;
				$this->movePos();
			} else {$quote = false;	}
			if ($quote) {
				// Quoted parameter
				$value = $this->getUntilCharEscape($quote);
			} else {
				// Simple parameter
				$value = $this->getUntilChars($chars_end);
			}
			if ($attributes === null) {
				$attributes = new kiAttributesList;
			}
			$attributes->set($name, $value);
			if (strpos($chars_space, $chr) !== false) {
				$this->skipChars($chars_space);
			}
		} while ($chr !== null);

		return $attributes;
	}

	protected function debug()
	{
		if (!self::$debug) {
			return;
		}
		$args = func_get_args();
		if (count($args) === 1) {
			$args = $args[0];
			if (is_string($args) || is_numeric($args)) {
				echo "$args\n";
				return;
			}
		}
		var_dump($args);
	}
}

class tagForeachFilter extends FilterIterator {
    private $data;
    public function __construct(Iterator $iterator , $data ) {
        parent::__construct($iterator);
        $this->data = $data;
    }

    public function accept() {
        $data = $this->getInnerIterator()->current();
        if ( 0!== 0 ) {
            return false;
        }
        return true;
    }
}

/**
 * Base ki node
 *
 * @property string           $nodeName               Alias of "name"
 * @property int              $nodeType               Alias of "type"
 * @property string           $nodeValue              Alias of "value"
 * @property int              $childElementCount      Count of child elements
 * @property kiNode[]       $childNodes             Alias of "nodes"
 * @property kiNodeTag|null $nextElementSibling     Alias of "next"
 * @property kiNodeTag|null $previousElementSibling Alias of "prev"
 * @property kiNode|null    $nextSibling            Next sibling node
 * @property kiNode|null    $previousSibling        Previous sibling node
 * @property kiNode|null    $firstNode              First child node
 * @property kiNode|null    $lastNode               Last child node
 * @property string           $textContent          Alias of "text()"
 * @property string           $text                 Alias of "text()"
 * @property string           $html                 Alias of "html()"
 * @property string           $outerHtml            Alias of "outerHtml()"
 *
 * @method kiNode parent()    Returns the parent of this node.
 * @method kiNode clone()     Create a deep copy of this node.
 * @method kiNode empty()     Remove all child nodes of this element from the DOM. Alias of cleanChildren().
 * @method string   innerHtml() Returns inner html of node (Alias of html()).
 */
abstract class kiNode
{
	protected static $counter = 1;
	public $name;
	public $nameReal;
	public $value;
	public $attributes;
	public $type = 0;
	public $chid = -1;
	public $cnid = -1;
	public $nodes = array();
	public $children = array();
	public $parent;
	public $firstChild;
	public $lastChild;
	public $prev;
	public $next;
	public $ownerDocument;
	public $uniqId = 0;

	public function __construct() {
		$this->uniqId = self::$counter++;
	}

	public function clear()	{
		$this->attributes    = null;
		$this->next          = null;
		$this->prev          = null;
		$this->parent        = null;
		$this->ownerDocument = null;
		$this->clearChildren();
	}

	public function clearChildren()
	{
		foreach ($this->nodes as $node) {
			$node->clear();
		}; unset($node);
		$this->firstChild = null;
		$this->lastChild  = null;
		$this->children   = array();
		$this->nodes      = array();
		return $this;
	}

//======================================================================//
//======================================================================//

	public function saveCache() {
		$cachename=wbGetCacheName();
		$expired=$this->find("meta[name=cache]")->attr("content")+time();
		$this->find("meta[name=cache]")->attr('expired',$expired);
		$content=$this->outerHtml();
		return file_put_contents($cachename,$content, LOCK_EX);
	}

	public function beautyHtml($step=0) {
		return $this->wbHtmlFormat($step);
	}

	public function wbHtmlFormat($step=0) {
		$this->children()->after("{{_line_}}");
		$this->children()->before("{{_line_}}".str_repeat("{{_tab_}}",$step));
		$childs=$this->children();
		foreach($childs as $child) {
			if ($child->find("*")->length) $child->append(str_repeat("{{_tab_}}",$step));
			$step++;
			$child->wbHtmlFormat($step);
			$step--;
			$str=trim($child->outerHtml());
			$child->after($str);
			$child->remove();
		}

		if ($step==0) {
			$result=trim($this->outerHtml());
			$result=trim(str_replace(array(" {{_tab_}}","{{_tab_}}","{{_line_}}"),array("{{_tab_}}","    ","\n"),$result));
			return $result;
		}
	}

	public function wbBaseHref() {
		wbBaseHref($this);
		return $this;
	}


	public function wbSetData($Item=array()) {
			if (!isset($_ENV["ta_save"])) {$_ENV["ta_save"]=array();}
			$this->wbSetAttributes($Item);
			$this->wbUserAllow();
			$nodes=new IteratorIterator($this->find("*"));
			foreach($nodes as $inc) {
				if (!$inc->parents("[type=text/template]")->length) {
				$inc->wbUserAllow();
        $inc->wbWhere($Item);
				$tag=$inc->wbCheckTag();
				if (!$tag==FALSE && !$inc->hasClass("wb-done")) {
					if ($inc->has("[data-wb-json]")) {$inc->json=wbSetValuesStr($inc->json,$Item);}
					if ($inc->hasRole("variable")) {$Item=$inc->tagVariable($Item);} else {
						if ($inc->is("[data-wb-tpl=true]")) {$inc->addTemplate();}
						$inc->wbProcess($Item,$tag);
                        if ($inc->is("[data-wb-hide=true]"))	{
                            $tmp=$inc->innerHtml();
                            $inc->replaceWith($tmp);
                        }
						//if (isset($_SESSION["itemAfterWhere"])) {$Item=$_SESSION["itemAfterWhere"]; unset($_SESSION["itemAfterWhere"]);}
					}
				}
				}
			}; unset($inc);
            $this->includeTag($Item);
			$this->wbSetValues($Item);
			$this->contentLoop($Item);
			$this->wbPlugins($Item);
			$this->wbTargeter($Item);
      gc_collect_cycles();
	}

    public function wbWhere($Item){
        $where=$this->attr("data-wb-where");
        if ($where=="") $where=$this->attr("where");
        if ($where>"" AND $this->hasRole("foreach")) {
          return;
        } elseif ($where>"" AND !wbWhereItem($Item,$where)) {
			       $this->remove();
		    }
    }


	public function wbPlugins(){
		$script=$this->find("script[data-wb-src]:not(.wb-done)");
		foreach($script as $sc) {
            if ($sc->attr("data-wb-src")=="jquery") {
					        $sc->after('<script src="/engine/js/jquery.min.js" type="text/javascript" charset="UTF-8"></script>');
                  $sc->remove();
            }
            if ($sc->attr("data-wb-src")=="bootstrap3") {
					         $sc->before('<link href="/engine/js/bootstrap3/bootstrap.min.css" rel="stylesheet">');
					         $sc->after('<script src="/engine/js/bootstrap3/bootstrap.min.js" type="text/javascript" charset="UTF-8"></script>');
                   $sc->remove();
            }
            if ($sc->attr("data-wb-src")=="bootstrap") {
 					     $sc->before('<link href="/engine/js/bootstrap/bootstrap.min.css" rel="stylesheet">');
					     $sc->after('<script src="/engine/js/bootstrap/bootstrap.min.js" type="text/javascript" charset="UTF-8"></script>');
               $sc->remove();
            }


			if ($sc->attr("data-wb-src")=="datepicker") {
          $lang="ru"; if ($sc->attr("data-wb-lang")>"") {$lang=$sc->attr("data-wb-lang");}
					$sc->before('<link href="/engine/js/datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">');
					if ($lang>"") $sc->after("<script src='/engine/js/datetimepicker/locales/bootstrap-datetimepicker.{$lang}.js' type='text/javascript' charset='UTF-8'></script>");
					$sc->after('<script src="/engine/js/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript" charset="UTF-8"></script>');
					$sc->remove();
			}
      if ($sc->attr("data-wb-src")=="plugins") {
          $sc->before('<link href="/engine/js/plugins/plugins.css" rel="stylesheet">');
          $sc->after('<script src="/engine/js/plugins/plugins.js" type="text/javascript"></script>');
          $sc->remove();
      }
      if ($sc->attr("data-wb-src")=="ckeditor") {
					//$sc->before('<link href="/engine/js/ckeditor/style.css" rel="stylesheet">');
					$sc->after('<script src="/engine/js/ckeditor/adapters/jquery.js" type="text/javascript"></script>');
                    $sc->after('<script src="/engine/js/ckeditor/bootstrap-ckeditor-fix.js" type="text/javascript"></script>');
                    $sc->after('<script src="/engine/js/ckeditor/ckeditor.js" type="text/javascript"></script>');
                    $sc->remove();
			}
      if ($sc->attr("data-wb-src")=="source") {
				$sc->after('<script language="javascript" src="/engine/js/ace/ace.js"></script>');
        $sc->remove();
			}
		if ($sc->attr("data-wb-src")=="uploader") {
					$sc->after(wb_file_get_contents(__DIR__ ."/js/uploader/uploader.php"));
          $sc->remove();
		}

    if ($sc->attr("data-wb-src")=="imgviewer") {
					$sc->after(wb_file_get_contents(__DIR__ ."/js/photoswipe/photoswipe.php"));
          $sc->remove();
		}


		if ($sc->attr("data-wb-src")=="engine") {
				$sc->after('<script src="/engine/js/wbengine.js" type="text/javascript" charset="UTF-8"></script>');
				$sc->remove();
		}


		}
	}

	public function wbCheckAllow() {
		foreach($this->find(wbControls("allow")) as $inc) {$inc->wbUserAllow();}
	}

	public function wbUserAllow() {
		if (isset($_SESSION["user_role"])) {
			$role=$_SESSION["user_role"];
		} else {$role="noname";}
		$allow=trim($this->attr("data-wb-allow")); if ($allow>"") {
			$allow=str_replace(" ",",",trim($allow));
			$allow=explode(",",$allow);
			$allow = array_map('trim', $allow);
		}
		$disallow=trim($this->attr("data-wb-disallow")); if ($disallow>"") {
			$disallow=str_replace(" ",",",trim($disallow));
			$disallow=explode(",",$disallow);
			$disallow = array_map('trim', $disallow);
		}
		$disabled=trim($this->attr("data-wb-disabled")); if ($disabled>"") {
			$disabled=str_replace(" ",",",trim($disabled));
			$disabled=explode(",",$disabled);
			$disabled = array_map('trim', $disabled);
		}
		$enabled=trim($this->attr("data-wb-enabled")); if ($enabled>"") {
			$enabled=str_replace(" ",",",trim($enabled));
			$enabled=explode(",",$enabled);
			$enabled = array_map('trim', $enabled);
		}
		$readonly=trim($this->attr("data-wb-readonly")); if ($readonly>"") {
			$readonly=str_replace(" ",",",trim($readonly));
			$readonly=explode(",",$readonly);
			$readonly = array_map('trim', $readonly);
		}
		$writable=trim($this->attr("data-wb-writable")); if ($writable>"") {
			$writable=str_replace(" ",",",trim($writable));
			$writable=explode(",",$writable);
			$writable = array_map('trim', $writable);
		}
		if ($disallow>""	&&  in_array($role,$disallow)) {$this->remove();}
		if ($allow>"" 		&& !in_array($role,$allow)) {$this->remove();}
		if ($disabled>"" 	&&  in_array($role,$disabled)) {$this->attr("disabled",true);}
		if ($enabled>"" 	&& !in_array($role,$enabled)) {$this->attr("disabled",true);}
		if ($readonly>"" 	&&  in_array($role,$readonly)) {$this->attr("readonly",true);}
		if ($writable>"" 	&& !in_array($role,$writable)) {$this->attr("readonly",true);}
	}


	public function wbCheckTag() {
		$res=FALSE;
		$tags=wbControls("tags");
		foreach($tags as $tag) {
			if ($this->hasRole($tag)) {$res=$tag; return $res;}
		}; unset($tag);
		return $res;
	}


	public function wbProcess($Item,$tag) {
				if (!$this->parents("script")->length) {
						$this->addClass("wb-done");
						$func="tag".$tag;
						$this->wbSetAttributes($Item);
						if ($this->attr("src")>"") {$this->attr("src",wbNormalizePath($this->attr("src")));}
						if ($tag>"") {$this->$func($Item);}
						$this->tagHideAttrs($Item);
				}
	}

	function wbTargeter($Item=array()) {
		$tar=$this->find(wbControls("target"));
		$attr=(explode(",",str_replace(array("[","]"," "),array("","",""),wbControls("target"))));
		foreach($tar as $inc) {
			if (!$inc->hasAttribute("data-wb-ajax")) {
				foreach ($attr as $key => $attribute) {
					$$attribute=$inc->attr($attribute);
					if ($$attribute>"" ) {
						if ($this->find($$attribute)->length) {
							//if ($this->find($$attribute)->hasAttribute("data-role") AND !$this->find($$attribute)->hasClass("wb-done")) {} else {
								$inc->removeAttr($attribute);
								$com=str_replace("data-wb-","",$attribute);
								if ($com=="replace") {$attribute="replaceWidth";}
								$this->find($$attribute)->$com($inc);
							//}
						}
					}
				}; unset($attribute);
			}
		}; unset($inc);
		$this->find("[data-wb-remove]")->remove();
		//$this->find("input[data-wb-role=tree].wb-done")->remove();
		//$this->find("input[role=tree].wb-done")->remove();
	}

	public function excludeTextarea($Item=array()) {
		$list=$this->find("textarea,[type=text/template],pre,.nowb,[data-role=module]");
		$_ENV["ta_save"]=array();
		foreach ($list as $ta) {
			$id=wbNewId();
			$ta->attr("taid",$id);
			if ($ta->is("textarea[value]")) {
				$_ENV["ta_save"][$id]=wbSetValuesStr($ta->attr("value"),$Item);
			} else {
				$_ENV["ta_save"][$id]=$ta->html();
			}
			$ta->html("");
		}; unset($ta,$list);
	}
	function includeTextarea($Item=array()) {
		$list=$this->find("textarea,[type=text/template],pre[taid],.nowb[taid],[data-role=module][taid]");
		foreach ($list as $ta) {
			$id=$ta->attr("taid"); $name=$ta->attr("name");
			if (isset($_ENV["ta_save"][$id])) $ta->html($_ENV["ta_save"][$id]);
			//if ($name>"" && isset($Item[$name]) && !is_array($Item[$name]) && $_GET["mode"]=="edit") {
			if ($name>"" && isset($Item[$name]) && !is_array($Item[$name])) {
				$ta->html(htmlspecialchars($Item[$name]));
			} else {
				if (isset($_ENV["ta_save"][$id])) $ta->html($_ENV["ta_save"][$id]);
			}
			unset($_ENV["ta_save"][$id]);
			$ta->removeAttr("taid");
		}; unset($ta,$list);
	}

	public function contentLoop($Item) {
		$res=0; $list=new IteratorIterator($this->find("*"));
		foreach($list as $inc) {
			$tag=$inc->wbCheckTag();
			if (!$tag==FALSE && !$inc->hasClass("wb-done")) {$inc->wbProcess($Item,$tag); }
		}; unset($inc,$list);
	}

	public function wbSetValues($Item=array(),$obj=TRUE) {
		$this->excludeTextarea($Item);
		$this->wbSetAttributes($Item);
		$text=$this->html();
			if (isset($Item["form"])) {
                $Item=wbTrigger("form",__FUNCTION__,"BeforeSetValues",func_get_args(),$Item);
			}
			$list=$this->find("input,select");
			foreach($list as $inp) {
				$from=$inp->attr("data-wb-from");
				$name=$inp->attr("name");	$def=$inp->attr("value");
				if (substr($name,-2)=="[]") {$name=substr($name,0,-2);}
				if (substr($def,0,3)=="{{_") {$def="";}
                if (isset($Item[$name]) AND $inp->attr("value")=="") {
                    if (is_array($Item[$name])) {$Item[$name]=wbJsonEncode($Item[$name]);}
                    $value=$Item[$name];
                } else {$value=$def;}

                if ($value!=="") {$inp->attr("value",$value);} else {
                    if (!$inp->hasAttr("value") AND isset($Item[$name])) {
                        if (is_array($Item[$name])) {$Item[$name]=wbJsonEncode($Item[$name]);}
                        $inp->attr("value",$Item[$name]);
                    }
                }

				if ($inp->attr("type")=="checkbox") {
					if ($inp->attr("value")=="on" OR $inp->attr("value")=="1") {$inp->checked="checked";}
				}

				if ($inp->is("select") AND $inp->attr("value")>"") {
					$value=$inp->attr("value");
					if (is_array($value)) {
						foreach($value as $val) {
							$inp->find("option[value=".$val."]")->selected="selected";
						}
						$value=$value[0];
					} else {
						$inp->find("option[value=".$value."]")->selected="selected";
					}
				}
				$inp->wbSetMultiValue($Item);
			};

			$list=$this->find("textarea");
			foreach($list as $inp) {
				$name=$inp->attr("name");	$def=$inp->attr("value");
				if (isset($Item[$name])) {$inp->html(htmlspecialchars($Item[$name]));} else {$inp->html(htmlspecialchars($def));}
			}
			unset($inp,$list);
			if (!is_array($Item)) {$Item=array($Item);}
			$this->html(wbSetValuesStr($this->html(),$Item));

			$this->includeTextarea($Item);
		if ($obj==FALSE) {return $this->outerHtml();}
	}

	public function wbDatePickerPrep() {
		// тут что-то криво работает - нужно переделывать
		// вызывается из wbSetValues
		$type=$this->attr("type");
		$oconv="";
		/*if ($_SESSION["settings"]["appuiplugins"]=="on") {
			if ($type=="date") {$this->attr("type","datepicker");}
			if ($type=="datetime") {$this->attr("type","datetimepicker");}
		}*/
		$arr=array(	"datepicker"=>"d.m.Y",
					"datetimepicker"=>"d.m.Y H:i",
					"timepicker"=>"H:i");
		foreach($arr as $t => $val) {
			if ($type==$t) {$oconv=$val;}
		}
		if ($this->attr("date-oconv")>"") {$oconv=$this->attr("date-oconv");}
		if ($oconv>"" && $this->attr("value")>"") $this->attr("value",date($oconv,strtotime($this->attr("value"))));
	}


	public function wbSetMultiValue($Item) {
		$name=$this->attr("name");
		preg_match_all('/(.*)\[.*\]/',$name,$fld);
		if (isset($fld[1][0])) {
			$fldname=$fld[1][0];
			if (isset($Item[$fldname])) {
				preg_match_all('/\[(.*)\]/',$name,$sub); $sub=$sub[1][0];
				$value=$Item[$fldname][$sub];
				if ($this->attr("type")=="checkbox") {
					if ($value=="on" OR $this->attr("value")=="1") {$this->attr("checked","checked");}
				} else {
					$this->attr("value",$value);
				}
				$this->wbDatePickerPrep();
			}
		}
		preg_match_all('/\[(.*)\]/',$name,$matches);
		if (count($matches[1])) {
		foreach($matches[1] as $sub) {
			$fld=$fld[1][0];
		}; unset($sub);
		}
	}

	public function tagVariable($Item) {
		include("wbattributes.php");
		if (!isset($_ENV["var"])) {$_ENV["var"]=array();}
		$this->wbSetAttributes($Item);
		$var=$this->attr("var");
		if (!isset($where)) $where=$this->attr("where");
		if (($where>"" AND wbWhereItem($Item,$where)) OR $where=="") {
			if ($var>"") $_ENV["variables"]["{$var}"]=wbSetValuesStr($this->attr("value"),$Item);
		}
		return $Item;
	}

	public function tagUploader($Item) {
		if ($this->attr("id")==NULL) {$uid=wbNewId();$this->attr("id",$uid);} else {$uid=$this->attr("id");}
		if ($this->hasAttr("name")) {$fldname=$this->attr("name");} else {$fldname="images";}
		if (!$this->hasAttr("data-wb-form") AND isset($_ENV["route"]["form"])) {$this->attr("data-wb-form",$_ENV["route"]["form"]);}
		if (!$this->hasAttr("data-wb-item") AND isset($_ENV["route"]["item"])) {$this->attr("data-wb-item",$_ENV["route"]["item"]);}
		if ($this->hasAttr("multiple")) {$out=wbGetForm("common","upl_multiple");} else {$out=wbGetForm("common","upl_single");}
		$out->find(".wb-uploader")->attr("id",$uid);
		$attrs=new IteratorIterator($this->attributes());

		foreach ($attrs as $attr) {
			$tmp=$attr->name;
			if (strpos($tmp,"ata-wb-")) {$tmp=str_replace("data-wb-","",$tmp); $$tmp=$attr."";}
		}; unset($attrs);

		if (!$this->hasAttr("data-wb-path")) {
			$Item["path"]=wbFormUploadPath();
			$out->find(".wb-uploader")->attr("data-wb-path",$Item["path"]);
			$out->find(".wb-uploader")->attr("data-wb-form",$_ENV["route"]["form"]);
			$out->find(".wb-uploader")->attr("data-wb-item",$_ENV["route"]["item"]);
		} else {
			$Item["path"]=$this->attr("data-wb-path");
		}
		$out->find(".wb-uploader")->children("input[name]")->attr("name",$fldname);
		$out->find(".wb-uploader .gallery[data-wb-from]")->attr("data-wb-from",$fldname);
		$out->wbSetData($Item);
		$this->replaceWith($out);
	}

	public function tagMultiInput($Item) {
		$len=count($this->find("input,select,textarea"));
		if ($len==0) {$len=1;}
		include("wbattributes.php");
		if ($this->attr("name") AND !isset($name)) {$name=$this->attr("name");} else {$this->attr("name");}
		$tags=array("input","select","textarea");

		$tpl=wbFromString($this->html());
		$template=$this->innerHtml();

		$tplId=wbNewId();
		$this->after("<script type='text/template' id='{$tplId}'>".$template."</script>");
		$this->attr("data-tpl","#".$tplId);
		if (isset($Item[$name]) AND is_array($Item[$name])) {
			$this->tagMultiInputSetData($Item[$name]);
		} else {
			$this->tagMultiInputSetData();
		}

	}

	function tagMultiInputSetData($Data=array(0=>array())) {
		$tpl=$this->html();
		$this->html("");
		$name=$this->attr("name");
		foreach($Data as $i => $item) {
			$line=wbFromString($tpl);
			$line->wbSetData($item);
			$inp=$line->find("input,select,textarea");
			foreach($inp as $tag) {
				$tname=$tag->attr("name");
				if ($tag->attr("name")>"") {$tag->attr("name",$name."[{$i}][".$tname."]");}
				$tag->attr("data-wb-field",$tname);
			}
			$row=wbGetForm("common","multiinput_row")->outerHtml();
			$this->append(str_replace("{{template}}",$line->outerHtml(),$row));
			unset($line);
		}
	}


	public function getAttrVars() {
		return $tag->attr("vars");
	}

	public function wbSetAttributes($Item=array()) {
		$attributes=$this->attributes();
		if (is_object($attributes) && (strpos($attributes,"}}") OR strpos($attributes,"%"))) {
			foreach($attributes as $at) {
				$atname=$at->name;
				$atval=$this->attr($atname);
				if ($atval>"" && strpos($atval,"}}")) {
					$atval=wbSetValuesStr($atval,$Item);
					$this->attr($atname,$atval);
				};
				if ($atval>"" && substr($atval,0,1)=="%") {
					$ev=substr($atval,1);
					eval('$tmp = '.$ev.';');
					$this->attr($atname,$tmp);
				}

				unset($atname,$atval,$at);
			}; unset($attributes);
		}
	}

	public function tagHideAttrs() {
		$hide=trim($this->attr("data-wb-hide"));
		$hide=trim(str_replace(","," ",$hide));
        if ($hide=="wb") {
            $list=$this->attributes(); $hide="";
            foreach($list as $attr) {
                if (substr($attr->name,0,8)=="data-wb-") {
                    $hide.=" ".$attr->name;
                    if (!$this->hasAttr("role")) {$this->removeClass("wb-done");}
                }
            }
        } elseif ($hide=="*") {
            $this->after($this->innerHtml()); $this->remove();
        }
        $list=explode(" ",trim($hide));
		foreach($list as $attr) {
			$this->removeAttr($attr);
		}


        		$this->removeAttr("data-wb-hide");
	}

	public function tagCart() {
		if ($this->find(".cart-item")->length) {
			$Item=wbItemRead("orders",$_SESSION["order_id"]);
			$tplid=uniqId();
			$this->attr("data-template",$tplid);
			$this->addTemplate($this->innerHtml());
			$this->wbSetData($Item);
            $items=$this->find(".cart-item");
            $idx=0;
            foreach($items as $i) {
                if ($i->attr("idx")=="") {$i->attr("idx",$idx); $idx++;}
            }
		}
	}

	public function tagTree($Item=array()) {
		include("wbattributes.php");
		$this->wbSetAttributes($Item);
		$name=$this->attr("name"); if (isset($from)) {$name=$from;}
		if ($name=="" AND isset($item)) {$name=$item;}
		$type=$this->attr("type");
		if (isset($dict)) {
			$dictdata=wbItemRead("tree",$dict);
			$Item["_{$name}__dict_"]=$dictdata["_tree__dict_"];
			if (!isset($Item[$name])) {$Item[$name]=$dictdata["tree"];}
			unset($dictdata);
        }
		if (($this->hasAttr("name") OR $this->is("input")) AND !$this->is("select") ) {
			$tree=wbGetForm("common","tree_ol");
			$this->append("<input type='hidden' name='{$name}'><input type='hidden' name='_{$name}__dict_' data-name='dict'>");
			if (isset($Item[$name]) && $Item[$name]!=="[]" && $Item[$name]!=="") {$tree->tagTreeData($Item[$name]);} else {$tree->find("ol")->append(wbGetForm("common","tree_row"));}
			$this->prepend($tree);
			$this->addClass("wb-tree dd");
      $this->wbSetData($Item);
		} elseif ($type=="select") {
				$this->tagTreeUl($Item);
		} else {
			if ($item>"") {$tree=wbTreeRead($item); $Item[$name]=$tree["tree"]; $Item["_{$name}"]=$tree["dict"];}
			$this->tagTreeUl($Item);
		}
    }

	public function tagTreeData($data=array()) {
		$tree=wbGetForm("common","tree_ol");
		$tree->find("input[name]")->remove();
		$rowtpl=wbGetForm("common","tree_row");
		$name=$this->find("input[name]")->attr("name");
		if (!is_array($data)) {$data=json_decode($data,true);}
		foreach($data as $item) {
			$tpl=$rowtpl->clone();
			$tpl->wbSetData($item);
			if (isset($item["children"]) AND $item["children"]!==false AND $item["children"]!==array()) {
				if (isset($item["open"]) AND $item["open"]==false) {$tpl->find(".wb-tree-item")->addClass("dd-collapsed");}
				$tch=$tree->clone();
				$tch->tagTreeData($item["children"]);
				$tpl->find(".dd-item")->append($tch);
			}
			$this->children("ol")->append($tpl);
		}
	}

	public function tagTreeUl($Item=array(),$param=null) {
		$limit=-1; $lvl=0; $idx=0; $tree=$Item; $branch=0; $parent=1; $pardis=0; $children=1;
		if ($param==null) {
			include("wbattributes.php");
			$name=$this->attr("name");
      if (isset($from)) {$name=$from;}
			if ($name=="" AND isset($item)) {$name=$item;}
			if (!is_array($Item[$name])) {$tree=json_decode($Item[$name],true);} else {$tree=$Item[$name];}
			$tag=$this->tag();
			if (!isset($limit) OR $limit=="false" OR $limit*1<0) {$limit=-1;} else {$limit=$limit*1;}
      if ($parent=="disabled") {$pardis=1;$parent=1;} else {$pardis=0;}
      if ($parent=="false" OR $parent=="0") {$parent=0;} elseif ($parent=="true") {$parent=1;}
      if ($children=="false" OR $children*1==0) {$children=0;} elseif ($children=="true" OR $children=="1") {$children=1;}
		} else {
			foreach($param as $k =>$val) {$$k=$val;}
			$tree=$Item;
		}
    if (!isset($level)) {$level="";}
    $tpl=$this->html();
		$this->html("");
		if ($branch!==0) {
            $br=explode("->",$branch);
            foreach($br as $b) {
                $tree=array(wbTreeFindBranchById($tree,trim($b)));
            }
        }
    		if ($this->hasAttr("placeholder") AND $this->is("select")) {
    			$this->prepend("<option value='' class='placeholder'>".$this->attr("placeholder")."</option>");
    		}
        foreach($tree as $i => $item) {

                  $lvl++;
                  $item["_idx"]=$idx; $idx++;
                  $line=wbFromString($tpl);
                  $line->wbSetData($item);

                  if (isset($item["children"]) AND is_array($item["children"]) AND count($item["children"])  AND $children!==0) {
                    if ($pardis==1 AND ($limit!==$lvl-1)) {$line->children()->attr("disabled",true);}
                    $child=wbFromString($tpl);
                    $child->tagTreeUl($item["children"],array("name"=>$name,"tag"=>$tag,"lvl"=>$lvl,"idx"=>$idx,"level"=>$level,"pardis"=>$pardis,"parent"=>$parent,"children"=>$children,"limit"=>$limit));
                    if (($limit==-1 OR $lvl<=$limit)) {
              	          if ($tag=="select") {
                                 if ($parent!==1) {$lvl--;}
                                 $child->children("option")->prepend(str_repeat("<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>",$lvl));
                                 $child->wbSetValues($item);
                                 if ($parent!==1) {
                                   $line->html($child);
                                 } else {
                                   $line->append($child);
                                 }

                          } else {
                               if ($parent!==1) {
                                   $line->html($child);
                               } else {
                                   if ($children==1) $line->children(":first-child")->append("<{$tag}>".$child->outerHtml()."</{$tag}>");
                               }
                          }
                    }
                }
                $lvl--;

            if (isset($line)) $this->append($line);
        }
    }

	public function tagImageLoader($Item) {
			if ($this->attr("load")!==1) {
				$form=$_GET["form"];
				$item=$Item["id"];
				if ($this->attr("data-form")>"") {$form=$this->attr("data-form");}
				if ($this->attr("data-item")>"") {$item=$this->attr("data-item");}
				$path=formPathGet($form,$item);
				$img=formGetForm("form","comImages");
				$ext=$this->attr("data-ext");
				$max=$this->attr("data-max");
				$name=$this->attr("data-name");
				if ($this->attr("data-prop")=="false") {$img->find(".form-group.prop")->remove();}
				$img->wbSetValues($Item);
				$this->attr("path",$path["uplitem"]);
				$this->html($img->outerHtml());
				$this->attr("load",1);
				$this->addClass("wb-done");
				if ($name>"") $this->find("[data-role=imagestore]")->attr("name",$name);
				if ($ext>"") $ext=implode(" ",wbAttrToArray($ext)); $this->find("[data-role=imagestore]")->attr("data-ext",$ext);
				if ($max>"") $this->find("[data-role=imagestore]")->attr("data-max",$max);
			}
	}

	public function tagWhere($Item=array()) {
		include("wbattributes.php");
		if ($this->attr("data") > "") {$where=$this->attr("data");}
		$res=wbWhereItem($Item,$where);
		if ($res==0) {$this->remove();} else {
			$vars=$this->find("[data-wb-role=variable]");
			foreach($vars as $v => $var) {$Item=$var->tagVariable($Item);}
			$this->wbSetData($Item);
		}
	}


	public function tagForeach($Item=array()) {
		$srcItem=$Item;
		$field=""; $sort=""; $add=""; $step=""; $id=""; $limit=""; $table=""; $cacheId=0;
		include("wbattributes.php");
		if (!isset($tpl) OR $tpl!=="false") {
			$tplid=$this->attr("data-wb-tpl");
			if ($tplid=="") {$this->addTemplate();}
		} else {$tplid=false;}
		if (!isset($size) OR $size=="false") {$size=false;}
		if (!isset($page) OR 1*$page<=0) {
			if (!isset($_GET["page"]) OR $_GET["page"]=="") {$page=1;} else {$page=$_GET["page"]*1;}
		} else {$page=$page*1;}
		if ( isset($from) AND $from > "") {
			if (isset($Item[$from]) AND !strpos("[",$from)) {
				if (isset($Item["form"])) {$table=$Item["form"];} else {$table="";}
				if (isset($Item["id"])) {$item=$Item["id"];} else {$item="";}
				$Item=$Item[$from];
				if (!is_array($Item)) {$Item=json_decode($Item,true);}
				if ($field>"") {$Item=$Item[$field];}
				if (!is_array($Item)) {$Item=json_decode($Item,true);}
			} else {
                $Item=wbGetDataWbFrom($Item,$from);
            }
		}
        if (isset($json) AND $json> "") {
            $Item=json_decode($json,true);
        }

        if (isset($count) AND $count>"") {
            $fcount=$count;
            $Item=array();
            $count=$count*1;
            for($i=1;$i<=$count;$i++){$Item[$i]=$srcItem;};
        }

		if ($table > "") {
            $table=wbTable($table);
			if ($item>"") {
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
		if (is_string($Item)) $Item=json_decode($Item,true);
		if (!is_array($Item)) $Item=array($Item);
		if ($sort>"") {$Item=wbArraySort($Item,$sort);}
		if (isset($rand) AND $rand!=="false") {shuffle($Item);}
		if (isset($call) AND is_callable($call)) {$Item=$call($Item);}

		if ($add=="true") {$this->find(":first-child",0)->attr("item","{{id}}");}
		$tpl=$this->innerHtml(); $inner=""; $this->html("");
		if ($step>0) {$steptpl=$this->clone(); $stepcount=0; $steps=wbFromString("");}
		if ($tplid=="") $tplid="tpl".wbNewId();
		$ndx=0; $fdx=0; $n=0; $stp=0;
		$count=count($Item);
		$inner="";
		$srcVal=array(); foreach($srcItem as $k => $v) {$srcVal["%{$k}"]=$v;}; unset($v); $srcVal["_parent"]=$srcItem;

		$ndx=0; $n=0; $f=0;
		$tmptpl=wbFromString($tpl);
        if (isset($rand) AND $rand=="true") {shuffle($Item);}
        $object = new ArrayObject($Item);
		$iterator = new tagForeachFilter($object->getIterator(),array(
			"id"=>$id,
		));
		foreach($object as $key => $val) {
            $val=wbItemToArray($val);
				$n++;
				if ($size!==false) $minpos=$size*$page-($size*1)+1; $maxpos=($size*$page);
				if ($size==false OR ($n<=$maxpos AND $n>=$minpos)) {
					$itemform=""; if (isset($val["_table"])) {$itemform=$val["_table"];}
					$text=$tmptpl->clone();
					$val=(array)$srcVal + (array)$val; // сливаем массивы
                    $text->find(":first")->attr("idx",$key);
					$val["_key"]=$key;
					$val["_idx"]=$ndx;
					$val["_ndx"]=$ndx+1;
                    $val["_step"]=$stp;
                    $val=wbCallFormFunc("BeforeShowItem",$val,$itemform);
                    $val=wbCallFormFunc("BeforeItemShow",$val,$itemform);
					$flag=true;
					if ($flag==true AND $where>"") {$flag=wbWhereItem($val,$where);}
					if ($flag==true AND $limit>"" AND $ndx>=$limit) {$flag=false;}
						if ($flag==true) {
							$ndx++;
                            $text->wbSetData($val);
							if ($step>0) { // если степ, то работаем с объектом
								if ($stepcount==0) {
									$t_step=$steptpl->clone();
									$t_step->addClass($tplid);
									$steps->append($t_step);
								}
								$steps->find(".{$tplid}:last")->append($text->outerHtml());
								$stepcount++;
								//$stepcount=$this->find(".{$tplid}:last")->children()->length;
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
        $this->replaceWith($steps->html());
				foreach ($this->find(".{$tplid}") as $tid) {$tid->removeClass($tplid);}; unset($tid);
			} else {
				$this->html($inner);
			}
			unset($val,$ndx,$t_step,$string,$text,$func,$inner,$tmptpl);

		if ($this->tag()=="select") {
			if (isset($result) AND !is_array($result)) {$this->outerHtml("");}
            if (isset($srcItem[$this->attr('name')])) $this->attr('value',$srcItem[$this->attr('name')]);
			$plhr=$this->attr("placeholder");
			if ($plhr>"") {$this->prepend("<option value=''>$plhr</option>");}
		} else {
			if ($size!==false AND !$this->hasClass("pagination")) {
				$cahceId=null; $find=null;
				$pages=ceil($count/$size);
				$this->attr("data-wb-pages",$pages);
				$this->tagPagination($size,$page,$pages,$cacheId,$count,$find);
			}
		}
		gc_collect_cycles();

	}

public function tagModule($Item=array()) {
	$src=$this->attr("src");
  $e=$_ENV["path_engine"]."/modules/{$src}/{$src}.php";
  $a=$_ENV["path_app"]."/modules/{$src}/{$src}.php";
  if (is_file($e)) {require_once($e);}
  if (is_file($a)) {require_once($a);}
  $attributes=array();
  $exclude=array("role","data-wb-role","class","src");
	$module=$_ENV["route"]["scheme"]."://".$_ENV["route"]["host"]."/module/{$src}/";
	$out=wbFromFile($module);
  $func=$src."_afterRead"; $_func=$src."__afterRead";
    // функция вызывается после получения шаблона модуля
		if (is_callable($func)) {$func($out,$Item);} else {
		if (is_callable($_func)) {$_func($out,$Item);}
	}
  $out->wbSetData($Item);
  $ats=$this->attributes();
  foreach($ats as $an => $av) {
    if (!in_array($an,$exclude)) {$out->find(":first:not(style,script,br)")->attr($an,$av);}
  }
  $out->find(":first:not(style,script,br)")->append($this->html());
  $func=$src."_beforeShow"; $_func=$src."__beforeShow";
    // функция вызывается перед выдачей модуля во внешний шаблон
	if (is_callable($func)) {$func($out,$Item);} else {
		if (is_callable($_func)) {$_func($out,$Item);}
	}
  $out->wbSetData($Item);
	$this->replaceWith($out);
}

public function tagInclude($Item=array()) {
		$src=$ssrc=$this->attr("src"); $res=0;
		$did=$this->attr("data-wb-id");
		$dad=$this->attr("data-wb-add");
		$header=$this->attr("data-wb-header"); if ($header>"") {$Item["header"]=$header;}
		$footer=$this->attr("data-wb-footer"); if ($footer>"") {$Item["footer"]=$footer;}
		$vars=$this->attr("data-wb-vars"); 	if ($vars>"") {$Item=wbAttrAddData($vars,$Item);}
		$json=$this->attr("data-wb-json"); 	if ($json>"") {$Item=json_decode($json,true);}
		$dfs=$this->attr("data-wb-formsave");
		$class=$this->attr("data-wb-class");
		$name=$this->attr("data-wb-name");
    if ($name=="") {$name=$this->attr("name");}
        switch($src) {
            case "modal":
                $this_content=wbGetForm("common",$src);
                break;
            case "imgviewer":
                $src="/engine/js/imgviewer.php";
                break;
            case "uploader":
                $this_content=wbGetForm("common",$src);
                break;
            case "editor":
                if ($name=="") {$name="text";}
                $this_content=wbGetForm("common",$src);
                break;
            case "source":
              if ($name=="") {$name="text";}
                $this_content=wbGetForm("common",$src);
                break;
            case "seo":
                $this_content=wbGetForm("common",$src);
                break;
            case "form":
                $form=""; $mode="";
                $arr=explode("_",$name);
                if (isset($arr[0])) {$form=$arr[0];}
                if (isset($arr[1])) {unset($arr[0]); $mode=implode("_",$arr);}
                $this_content=wbGetForm($form,$mode);
                break;
            case "template":
                $this_content=wbGetTpl($name);
                break;
            case "module":
                $_SESSION["module"]=array($name=>$this->attributes());
                $module=$_ENV["route"]["scheme"]."://".$_ENV["route"]["host"]."/module/{$name}/";
                unset($_SESSION["module"]);
                $this_content=wbFromFile($module);
                break;
        }
		$vars=$this->attr("data-wb-vars");	if ($vars>"") {$Item=wbAttrAddData($vars,$Item);}
    if (!isset($this_content)) {
		if ($src=="") {$src=$this->html(); $this_content=ki::fromString($src);} else {
			$tplpath=explode("/",$src);
			$tplpath=array_slice($tplpath,0,-1);
			$tplpath=implode("/",$tplpath)."/";
			$src=wbSetValuesStr($src,$Item);
			$file=$_ENV["path_app"].$src;
			if (is_file($_SERVER["DOCUMENT_ROOT"].$file)) {
				$src=$_SERVER["DOCUMENT_ROOT"].$file;
			} else {
				if (substr($src,0,7)!=="http://") { if (substr($src,0,1)!="/") {$src="/".$src;} $src="http://".$_SERVER['HTTP_HOST'].$src;}
			}
			$this_content=ki::fromFile($src);
		}
    }
		if ($did>"") {$this_content->find(":first")->attr("id",$did);}
		if ($dad=="false") {$this_content->find("[data-wb-formsave]")->attr("data-add",$dad);}
		if ($dfs>"") {$this_content->find("[data-wb-formsave]")->attr("data-formsave",$dfs);}
		if ($dfs=="false") {$this_content->find("[data-wb-formsave]")->remove();}
		if ($class>"") {$this_content->find(":first")->addClass($class);}

		if (($ssrc=="editor" OR $ssrc=="source") && !$this_content->find("textarea.{$ssrc}").length) {
			if ($did>"") {
				$this_content->find(":first")->removeAttr("id");
				$this_content->find("textarea.{$ssrc}")->attr("id",$did);
			}
			foreach($this_content->find("textarea.{$ssrc}:not(.wb-done)") as $editor) {
				if ($editor->attr("id")=="") {
					$sid=wbNewId();
					$editor->attr("id","{$ssrc}-{$sid}");
					$editor->parent("div")->find(".source-toolbar")->attr("id","{$ssrc}-toolbar-{$sid}");;
				}
				if ($editor->attr("name")=="") {$editor->attr("name",$name);}
			}
			$this_content->find("textarea.{$ssrc}")->attr("name",$name);
      if ($ssrc=="source" AND !$this_content->parents("form")->find("[name={$name}]")->length) {
          $this_content->append("<textarea class='hidden wb-done' name='$name'></textarea>");
      }

		}
        if ($this->find("include")->length) {
            $this->includeTag($Item);
        } else {
			$this->append($this_content);
		}
		$this->wbSetData($Item);
	}

    public function includeTag($Item) {
		 if ($this->find("include")->length) {
			$this->append("<div id='___include___' style='display:none;'>{$this_content}</div>");
            $Item=wbItemToArray($Item);
             foreach($this->find("include") as $inc) {
                $inc->wbSetAttributes($Item);
                $from=$inc->attr("data-wb-from");
                if ($from=="") {$from="text";}
                $this->find("#___include___")->html(wbGetDataWbFrom($Item,$from));
				$attr=array("html","outer","htmlOuter","outerHtml","innerHtml","htmlInner","text","value");
				foreach ($attr as $key => $attribute) {
					$find=$inc->attr($attribute);
					if ($attribute=="outer" OR $attribute=="htmlOuter") {$attribute="outerHtml";}
					if ($attribute=="inner" OR $attribute=="html" OR $attribute=="innerHtml" OR $attribute=="htmlInner") {$attribute="html";}
					if ($find>"" ) {
						foreach($this->find("#___include___")->find($find) as $text) {
							$inc->after($text->$attribute());
						}
					}
				}; unset($attribute);
				$inc->remove();
			}; unset($inc);
			$this->find("#___include___")->remove();
         }
    }

	public function tagFormData($Item=array()) {
		$srcItem=$Item;
		include("wbattributes.php");
		if (isset($form) AND !isset($table)) {$table=$form;}
		if (isset($vars) AND $vars>"") {$Item=wbAttrAddData($vars,$Item);}
		if (isset($from) AND $from>"") {$Item=wbGetDataWbFrom($Item,$from);}
		if (isset($json) AND $json>"") {$Item=json_decode($json,true);}
		if (!isset($mode) OR $mode=="") {$mode="show";}
		if (isset($table) AND $table>"") {
			if (isset($item) AND $item>"") {$Item=wbItemRead(wbTable($table),$item);}
		}
		if (isset($field) AND $field>"") {
            $Item=wbGetDataWbFrom($Item,$field);
		}
        if (isset($vars) AND $vars>"") {$Item=wbAttrAddData($vars,$Item);}
		if (isset($call) AND is_callable($call)) {$Item=$call($Item);}
		if (is_array($srcItem)) {foreach($srcItem as $k => $v) {$Item["%{$k}"]=$v;}; unset($v);}
    $this->includeTag($Item);
		$Item=wbCallFormFunc("BeforeShowItem",$Item,$table,$mode);
    $Item=wbCallFormFunc("BeforeItemShow",$Item,$table,$mode);
		$this->wbSetData($Item);
		//$this->html(clearValueTags($this->html()));
    }

public function tagThumbnail($Item=array()) {
	$bkg=false; $img="";
	$src=$this->attr("src"); if ($src=="") {$src="0";}
	$noimg=$this->attr("data-wb-noimg");
	$form=$this->attr("data-wb-form"); if ($form=="" && isset($Item["form"])) {$form=$Item["form"];}
    $from=$this->attr("data-wb-from"); if ($from=="") {$from="images";}
    $item=$this->attr("data-wb-item"); if ($item=="" && isset($Item["id"])) {$item=$Item["id"];}
	$show=$this->attr("data-wb-show");
	$class=$this->attr("class");
	$style=$this->attr("style");
	$width=$this->attr("width"); if ($width=="") {$width="160px";}
	$height=$this->attr("height"); if ($height=="") {$height="120px";}
	$offset=$this->attr("offset");
	$contain=$this->attr("contain");

	if ($form>"" && $item>"") {$Item=wbItemRead($form,$item); }
	$json=$this->attr('json'); 	if ($json>"") {$images=json_decode($json,true); } else {
        if (isset($Item[$from])) {
            if (is_array($Item[$from])) {$images=$Item[$from];} else {$images=json_decode($Item[$from],true);}
        } else {$images="";}
	}
	if (is_array($images) AND is_numeric($src)) {
		if (isset($images[$idx])) {$img=$images[$idx]["img"];} else {$img="";}
		$src=wbGetItemImg($Item,$idx,$noimg,$from,true);
        $src=str_replace($_ENV["path_app"],"",$src);
		$img=explode($src,"/"); $img=$img[count($img)-1];
		$this->attr("src",$src);
	}

	$srcSrc=$src;
	$srcImg=explode("/",trim($src)); $srcImg=$srcImg[count($srcImg)-1];
	$srcExt=explode(".",strtolower(trim($srcImg))); $srcExt=$srcExt[count($srcExt)-1];
	$exts=array("jpg","gif","png","svg","pdf");

	if (!in_array($srcExt,$exts)) {$src="/engine/uploads/__system/filetypes/{$srcExt}.png"; $img="{$srcExt}.png"; $ext="png";}



	if (is_numeric($this->attr("src"))) {$idx=$this->attr("src"); $this->removeAttr("src"); $num=true;} else {
		$idx=$this->parents("[idx]")->attr("idx"); if ($idx>"" && $src=="") {$num=true;} else {$idx=0;}
	}
    if ($this->attr("data-wb-size")>"") {$size=$this->attr("data-wb-size");}
    if (!isset($size) OR $size=="") {$size=$this->attr("size");}
    if ($size=="" AND isset($Item["intext_position"])) {
        if (isset($Item["intext_position"]["width"]) AND $Item["intext_position"]["width"]>"") {$width=$Item["intext_position"]["width"];} else {$width="200";}
        if (isset($Item["intext_position"]["height"]) AND $Item["intext_position"]["height"]>"") {$height=$Item["intext_position"]["height"];} else {$height="160";}
        $size="{$width}px;{$height}px;src";
    }
			if ($size>"") {
				$size=explode(";",$size);
				if (count($size)==1) {$size[1]=$size[0];}
				$width=$size[0]; $height=$size[1];
				if (isset ($size[2]) && $size[2]=="src") {$bkg=false;} else {$bkg=true;}
			}
			if ($offset>"") {
				$offset=explode(";",$offset);
				if (count($offset)==1) {$offset[1]=$offset[0];}
				$top=$offset[1]; $left=$offset[0];
			} else {
				$top="15%"; $left="50%";
			}

	if (!is_file($_ENV["path_app"].$src) && !is_file($_SERVER["DOCUMENT_ROOT"].$src)) {
		if (isset($Item["img"]) && !isset($Item[$from]) && isset($Item["%{$from}"]) && !is_file($src)) {
			$tmpItem=array();
			$tmpItem[$from]=$Item["%{$from}"];
			$tmpItem["form"]=$Item["%form"];
			$tmpItem["id"]=$Item["%id"];
			$src=wbGetItemImg($tmpItem,$idx,$noimg);
		} else {
			if ($noimg>"") {
				$src=$noimg;
			} else {
				if ($bkg==true) {$src="/engine/uploads/__system/image.svg"; $img="image.svg"; $ext="svg";}
				if ($bkg==false) {$src="/engine/uploads/__system/image.jpg"; $img="image.jpg"; $ext="jpg";}
			}
		}
	}

	$img=explode("/",trim($src)); $img=$img[count($img)-1];
	$ext=explode(".",trim($src)); $ext=$ext[count($ext)-1];
    $this->src=$src;

	if ($src==array()) {$src="";}
	if ($img=="" AND $bkg==true) {$src="/engine/uploads/__system/image.svg"; $img="image.svg"; $ext="svg";}
	if ($src=="" AND $bkg==true) {$src="/engine/uploads/__system/image.svg"; $img="image.svg"; $ext="svg";} else {
		if ($src=="" AND $bkg==false) {$src="/engine/uploads/__system/image.jpg"; $img="image.jpg"; $ext="jpg";}
		$img=explode("/",$src); $img=$img[count($img)-1];
		$this->attr("img",$img);
		$ext=substr($img,-3);
	}

	if ($ext!=="svg") {
		if ($contain=="true") {$thumb="thumbc";} else {$thumb="thumb";}
		$src=urldecode($src);
		list( $w, $h, $t ) = getimagesize($_SERVER["DOCUMENT_ROOT"].$src);
		if (substr($width,-2)=="px") {$width=substr($width,0,-2)*1;}
		if (substr($height,-2)=="px") {$height=substr($height,0,-2)*1;}
		if (substr($width,-1)=="%") {
			$w=ceil($w/100*(substr($width,0,-1)*1));
		} else {$w=$width;}

		if (substr($height,-1)=="%" ) {
			$h=ceil($h/100*(substr($height,0,-1)*1));
		} else {$h=$height;}

		$src="/{$thumb}/{$w}x{$h}/src{$src}";
	}

	if ($bkg==true) {
		if (!in_array($srcExt,$exts)) {$bSize="contain";} else {$bSize="cover";}
		if (is_numeric($width)) {$width.="px";}
		if (is_numeric($height)) {$height.="px";}
		$style="width:{$width}; height: {$height}; background: url('{$src}') {$left} {$top} no-repeat; display:inline-block; background-size: {$bSize}; background-clip: content-box;".$style;
		$this->attr("src","/engine/uploads/__system/transparent.png");
		$this->attr("width",$width);
		$this->attr("height",$height);
	} else {
		if ($ext!=="svg") {
//			$src.="&h={$height}&zc=1";
			$this->attr("src",$src);
		}
	}

	$this->attr("data-src",$srcSrc);
	$this->attr("data-ext",$srcExt);
	$this->attr("class",$class);
	$this->attr("noimg",$noimg);
	$this->attr("style",$style);
	$this->removeAttr('json');
}

	public function tagPagination($size=10,$page=1,$pages=1,$cacheId,$count=0,$find="") {
		$this->attr("data-wb-pages",$pages);
		$tplId=$this->attr("data-wb-tpl");
		$class="ajax-".$tplId;
        if (!isset($_SESSION["temp"])) {$_SESSION["temp"]=array();}
        $_SESSION["temp"][$class]=$_ENV;
        unset($_SESSION["temp"][$class]["DOM"],
              $_SESSION["temp"][$class]["cache"],
              $_SESSION["temp"][$class]["error"],
              $_SESSION["temp"][$class]["errors"]
             );
		if (is_object($this->parent("table")) && $this->parent("table")->find("thead th[data-wb-sort]")->length) {
			$this->parent("table")->find("thead")->attr("data-wb-cache",$cacheId);
			$this->parent("table")->find("thead")->attr("data-wb-size",$size);
			$this->parent("table")->find("thead")->attr("data-wb",$class);
		}
		if ($pages>0) {
			$find=urlencode($find);
			$pag=wbGetForm("common","pagination");
			$pag->wrapInner("<div></div>");
			$step=1;
			$flag=floor($page/10); if ($flag<=1) {$flag=0;} else {$flag*=10;}
			$inner="";
			$pagination=array("id"=>$class,"size"=>$size,"count"=>$count,"cache"=>$cacheId,"find"=>$find,"pages"=>array());
            if (!isset($_ENV["route"]["params"]["form"]) OR $_ENV["route"]["params"]["form"]=="") {$form=$tplId;} else {$form=$_ENV["route"]["params"]["form"];}
			for($i=1; $i<=$pages; $i+=$step) {
				$href=$_ENV["route"]["controller"]."/".$_ENV["route"]["mode"]."/".$form."/".$i;
                $pagination["pages"][$i]=array(
					"page"=>$i,
					"href"=>$href,
					"flag"=>$flag,
					"data"=>"{$class}-{$i}"
				);
				$inner.="<li data-page='{$i}'><a flag='{$flag}' data-wb-ajaxpage='/{$href}/' data='{$class}-{$i}'>{$i}</a></li>";
				if ($i>=10 ) {$step=10;}
				if ($page>=10 && $i<10) {$i=10-1; $step=1;}
				if ($flag>0 && $i>=$flag && $i<=$flag+9) {$step=1;}
				if ($page>=10 && $page<20 && $i<20) {$step=1;}
			}
			$pag->wbSetData($pagination);
			$pag->find("[data-page={$page}]")->addClass("active");
			if ($pages==1) {
				$style=$pag->find("ul")->attr("style");
				$pag->find("ul")->attr("style",$style.";display:none;");
			}
			$this->after($pag->innerHtml());
		}
		$this->removeAttr("data-wb-pagination");
	}

	public function addTemplate($type="innerHtml") {
			$tplid=wbNewId();
			$this->attr("data-wb-tpl",$tplid);
			$that=$this->clone();
			$that->removeClass("wb-done");
			if ($type=="innerHtml") {$tpl=$that->innerHtml();} else {$tpl=$that->outerHtml();}
			$this->after("<script type='text/template' id='{$tplid}' style='display:none;'>\n{$tpl}\n</script>");
	}


	public function tagGallery($Item) {
		$vars=$this->attr("vars");	$src=$this->attr("src"); 	$id=$this->attr("id");
		if ($vars>"") {$Item=wbAttrAddData($vars,$Item);}
		$inner=ki::fromFile($_SESSION['engine_path']."/tpl/gallery.php");
		if ($src=="") {
			if (trim($this->html())>"<p>&nbsp;</p>") {
				$inner->find(".comGallery")->html($this->html());
			}
		} else {
			$file=$_SESSION["prj_path"].$src;
			if (is_file($_SERVER["DOCUMENT_ROOT"].$file)) {$src=$file;}
			if (substr($src,0,7)!="http://") { if (substr($src,0,1)!="/") {$src="/".$src;} $src=$_SERVER['DOCUMEN_ROOT'].$src; }
			$inner->find(".comGallery")->html(ki::fromFile($src));
		}
		$this->html($inner);
		if ($id>"") {$this->find("#comGallery")->attr("id",$id);}
		$this->wbSetData($Item);
		$this->find(".comGallery")->removeAttr("vars");
		$this->find(".comGallery")->removeAttr("from");
		$this->find(".comGallery img[visible!=1]")->parents(".thumbnail")->remove();
		if (count($this->find(".comGallery")->children())>0) {$this->after($this->html());}
		$this->remove();
	}

	public function contentAppends() {
		contentAppends($this);
	}

	public function hasRole($role) {
		$tl=array();
		if 		($this->hasAttr("data-wb-role")) {$tl=wbAttrToArray($this->attr("data-wb-role"));}
		elseif 	($this->hasAttr("role")) 		{$tl=wbAttrToArray($this->attr("role"));}
		if (in_array($role,$tl)) {return true;} else {return false;}
	}

	public function hasAttr($attr) {
		if ($this->attr($attr)==NULL) {return FALSE;} else {return TRUE;}
	}

	public function hasClass($class) {
		$res=false;
		$list=explode(" ",trim($this->class));
		foreach($list as $k => $val) {
			if ($val==$class) {$res=true;}
		}; unset($val,$list);
		return $res;
	}

	public function removeClass($class) {
		$res=false;
		$classes=explode(" ",trim($this->class));
		foreach($classes as $k => $val) {
			if ($val==$class) {unset($classes[$k]);}
		}; unset($val);
		$this->class=trim(implode(" ",$classes));
		if ($this->class=="") {$this->removeAttr("class");}
	}

	public function addClass($class) {
		$res=false;
		$list=explode(" ",trim($this->class));
		foreach($list as $k => $val) {
			if ($val==$class) {$res=true;}
		}; unset($val,$list);
		if ($res==false) {$this->class=trim($this->class." ".$class);}
	}

//======================================================================//
//======================================================================//

	protected function updateOwnerDocument($document) {
		if (!$od = &$this->ownerDocument || !$document || $od->uniqId !== $document->uniqId) {
			$od = $document;
			foreach ($this->nodes as $node) {
				$node->updateOwnerDocument($document);
			}; unset($node);
		}
	}


	public function __call($name, $arguments) {
		$name_l = strtolower($name);
		// clone
		if ($name_l === 'clone') {
			return clone $this;
		}
		// empty
		else if ($name_l === 'empty') {
			return $this->clearChildren();
		}
		// innerHtml
		else if ($name_l === 'innerhtml') {
			return $this->html();
		}
		// property
		else if (property_exists($this, $name)) {
			return $this->$name;
		}

		throw new BadMethodCallException('Method "'.get_class($this).'.'.$name.'" is not defined.');
	}

	public function &__get($name)
	{
		$name_l = strtolower($name);

		// firstNode
		if ($name_l === 'firstnode') {
			$res = reset($this->nodes);
			$res !== false || $res = null;
		}
		// lastNode
		else if ($name_l === 'lastnode') {
			$res = end($this->nodes);
			$res !== false || $res = null;
		}
		// nodeName
		else if ($name_l === 'nodename') {
			$res = $this->name;
		}
		// nodeType
		else if ($name_l === 'nodetype') {
			$res = $this->type;
		}
		// nodeValue
		else if ($name_l === 'nodevalue') {
			$res = $this->value;
		}
		// text, textContent
		else if ($name_l === 'text' || $name_l === 'textcontent') {
			$res = $this->text();
		}
		// html
		else if ($name_l === 'html') {
			$res = $this->html();
		}
		// outerHtml
		else if ($name_l === 'outerhtml') {
			$res = $this->outerHtml();
		}
		// childElementCount
		else if ($name_l === 'childelementcount') {
			$res = count($this->children);
		}
		// childNodes
		else if ($name_l === 'childnodes') {
			$res = $this->nodes;
		}
		// nextElementSibling
		else if ($name_l === 'nextelementsibling') {
			$res = $this->next;
		}
		// previousElementSibling
		else if ($name_l === 'previouselementsibling') {
			$res = $this->prev;
		}
		// nextSibling
		else if ($name_l === 'nextsibling') {
			$res = null;
			if ($parent = $this->parent) {
				$cnid = $this->cnid+1;
				$nodes = &$parent->nodes;
				$res = isset($nodes[$cnid]) ? $nodes[$cnid] : null;
			}
		}
		// previousSibling
		else if ($name_l === 'previoussibling') {
			$res = null;
			if ($parent = $this->parent) {
				$cnid = $this->cnid-1;
				$nodes = &$parent->nodes;
				$res = isset($nodes[$cnid]) ? $nodes[$cnid] : null;
			}
		}
		// Node attribute
		else {
			$res =  ($this->attributes && $a = $this->attributes->get($name)) ? $a->value() : null;
		}

		return $res;
	}

	public function __set($name, $value) {
		$this->attr($name, $value);
	}

	public function __clone()
	{
		self::__construct();
		$this->chid = -1;
		$this->cnid = -1;
		$this->parent = null;
		$this->prev = null;
		$this->next = null;
		$this->firstChild = null;
		$this->lastChild = null;
		if ($attrs = $this->attributes) {
			$attrs = clone $attrs;
			$attrs->node = $this;
			$this->attributes = $attrs;
		}
		if ($nodes = $this->nodes) {
			$this->nodes = array();
			$this->children = array();
			$chid = 0;
			$cnid = 0;
			$prev = null;
			foreach ($nodes as $node) {
				$node = clone $node;
				$node->parent = $this;
				if ($node instanceof kiNodeTag) {
					$node->prev = $prev;
					if ($prev) {
						$prev->next = $node;
					} else {
						$this->firstChild = $node;
					}
					$prev = $node;
					$node->chid = $chid;
					$this->children[$chid] = $node;
					$chid++;
				}
				$node->cnid = $cnid;
				$this->nodes[$cnid] = $node;
				$cnid++;
			}; unset($node);
			if ($prev) {
				$this->lastChild = $prev;
			}
		}
		if ($this instanceof kiDocument) {
			/** @noinspection PhpParamsInspection */
			$this->updateOwnerDocument($this);
		}
	}

	public function __invoke($selector, $n = null)
	{
		return $this->find($selector, $n);
	}

	public function __toString()
	{
		return $this->outerHtml();
	}

	public function outerHtml()	{		return $this->html();	}
	public function htmlOuter()	{		return $this->outerHtml();	}
	public function innerHtml()	{		return $this->html();	}

	public function text($value = null)
	{
		// Set
		if ($value !== null) {
			// array
			if (is_array($value)) {
				$list = new kiNodesList();
				$list->list = $value;
				$value = new kiNodeText($list->textAll());
			}
			// string
			else if (!is_object($value)) {
				$value = new kiNodeText($value);
			}
			// nodes list
			else if ($value instanceof kiNodesList) {
				$value = new kiNodeText($value->textAll());
			}
			// not text node
			else if (!($value instanceof kiNodeText)) {
				$value = new kiNodeText($value->text());
			}
			return $this->clearChildren()->append($value);
		}

		// Get
		return $this->_text();
	}

	protected function _text($recursive = false)
	{
		$ret = '';
		$blockTags = &ki::$blockTags;
		/** @var $n kiNode|kiNodeTag */
		foreach ($this->nodes as $n) {
			if ($n instanceof kiNodeTag) {
				$name = $n->name;
				if ($n->selfClosed) {
					if ($name === 'br') {
						$ret .= "\n";
					}
				} else {
					$ret .= $n->_text(true);
				}
				if (isset($blockTags[$name])) {
					$ret .= "\n";
				}
			} else {
				$ret .= $n->text();
			}
		}; unset($n);
		if (!$recursive && ki::$skipWhitespaces) {
			$ret = trim($ret);
			$ret = preg_replace('/^[ \t]+|[ \t]+$/SXm', '', $ret);
			$ret = preg_replace('/\n{3,}/SX', "\n\n", $ret);
		}
		return $ret;
	}

	public function tag() {
		return $this->nameReal;
	}

	public function html($value = null)
	{
		// Set
		if ($value !== null) {
			$this->clearChildren();
			if (!is_object($value) && !is_array($value)) {
				$value = ki::fromString($value)->detachChildren();
			}
			$this->append($value);
			return $this;
		}

		// Get
		$ret = '';
		foreach ($this->nodes as $n) {
			$ret .= $n->outerHtml();
		}; unset($n);
		return $ret;
	}

	public function child($n) {
		$n >= 0 || $n = count($this->children) + $n;
		return isset($this->children[$n]) ? $this->children[$n] : null;
	}

	public function node($n) {
		$n >= 0 || $n = count($this->nodes) + $n;
		return isset($this->nodes[$n]) ? $this->nodes[$n] : null;
	}

	public function attr($name, $value = null, $toString = true) {
		if ($value === null) {
			if ($this->attributes) {
				if ($attr = $this->attributes->get($name)) {
					return $toString ? $attr->value() : $attr;
				}
			}
			return null;
		}
		if (!$this->attributes) {
			$this->attributes = new kiAttributesList();
		}
		$this->attributes->set($name, $value);
		return $this;
	}

	public function removeAttr($name) {
		$this->attributes && $this->attributes->delete($name);
		return $this;
	}

	public function hasAttribute($name)	{
		return $this->attributes && $this->attributes->has($name);
	}

	protected function insertAt(&$nodes, $targetCnid = 0, $targetChid = -1, $replace = false) {
		// Work only with tags and docs
		if (!($this instanceof kiNodeTag) && !($this instanceof kiDocument)) {
			$nodes = array();
			return $this;
		}

		// Convert content variants to simple array with nodes
		if (!is_array($nodes)) {
			// String
			if (!is_object($nodes)) {
				$nodes = ki::fromString($nodes);
				$nodes = &$nodes->detachChildren();
			}
			// Document
			else if ($nodes instanceof kiDocument) {
				$nodes = &$nodes->detachChildren();
			}
			// Nodes list
			else if ($nodes instanceof kiNodesList) {
				$nodes = $nodes->list;
			}
			// Node
			else {
				$nodes = array($nodes);
			}
		}
		if (!$nodes) {
			$nodes = array();
			return $this;
		}

		// Find $targetChid if it's not set
		$thisNodes = &$this->nodes;
		if ($targetChid === -1) {
			if (!$this->firstChild) {
				$targetChid = 0;
			} else if (isset($thisNodes[$targetCnid])) {
				$lNode = $thisNodes[$targetCnid];
				if ($lNode->chid !== -1) {
					$targetChid = $lNode->chid;
				} else {
					$targetChid = 0;
					$cnid = $targetCnid;
					while (--$cnid > -1) {
						$lNode = $thisNodes[$cnid];
						if ($lNode->chid !== -1) {
							$targetChid = $lNode->chid+1;
							break;
						}
					}
				}
				unset($lNode);
			} else {
				$targetChid = count($this->children);
			}
		}

		// Prepare nodes for inserting
		$childs = array();
		$cnid = $targetCnid;
		$chid = $targetChid;
		if ($chid === 0) {
			if ($replace) {
				$this->firstChild = isset($this->children[$chid+1]) ? $this->children[$chid+1] : null;
			}
			$prev = null;
		} else {
			$prev = $this->children[$chid-1];
			$prev->next = null;
		}
		foreach ($nodes as $n) {
			if ($n->parent) {
				$n->detach();
			}
			$n->parent = $this;
			$n->updateOwnerDocument($this->ownerDocument);
			$n->cnid = $cnid++;
			if ($n instanceof kiNodeTag) {
				if ($chid === 0) {
					$this->firstChild = $n;
				}
				$n->chid = $chid++;
				$n->prev = $prev;
				$n->next = null;
				if ($prev) {
					$prev->next = $n;
				}
				$childs[] = $n;
				$prev = $n;
			} else {
				$n->chid = -1;
				$n->prev = null;
				$n->next = null;
			}
		}; unset($n);

		// Recalculate child ids and properties
		$nextNotFound = true;
		$replaceChild = false;
		$cnid = $targetCnid;
		if ($replace) {
			if (isset($thisNodes[$cnid])) {
				$n = $thisNodes[$cnid];
				if ($n instanceof kiNodeTag) {
					$replaceChild = true;
				}
				$n->clear();
			}
			$cnid++;
		}
		if (isset($thisNodes[$cnid])) {
			$incNodes  = count($nodes);
			$incChilds = count($childs);
			if ($replace) {
				$incNodes--;
				if ($replaceChild) {
					$incChilds--;
				}
			}
			do {
				$n = $thisNodes[$cnid];
				$n->cnid += $incNodes;
				if ($n instanceof kiNodeTag) {
					if ($nextNotFound) {
						$n->prev = $prev;
						if ($prev) {
							$prev->next = $n;
						}
						$nextNotFound = false;
					}
					$n->chid += $incChilds;
				}
			} while (isset($thisNodes[++$cnid]));
		}
		if ($nextNotFound) {
			$this->lastChild = $prev;
		}
		// Insert elements
		array_splice($thisNodes, $targetCnid, $replace ? 1 : 0, $nodes);
		if ($childs || $replaceChild) {
			array_splice($this->children, $targetChid, $replaceChild ? 1 : 0, $childs);
		}
		/** @noinspection PhpUndefinedFieldInspection */
		isset($this->selfClosed) && $this->selfClosed = false;
		return $this;
	}

	public function after($content)	{
		if ($p = $this->parent) {
			$p->insertAt($content, $this->cnid+1);
		}
		return $this;
	}

	public function attrlist()	{
		parse_str(str_replace(array(" ",'"'),array("&",""),$this->attributes()),$attr);
		return $attr;
	}

	public function before($content) {
		if ($p = $this->parent) {
			$p->insertAt($content, $this->cnid, $this->chid);
		}
		return $this;
	}

	public function append($content) {
		return $this->insertAt($content, count($this->nodes), count($this->children));
	}

	public function prepend($content) {
		return $this->insertAt($content, 0, 0);
	}

	public function replaceWith($content) {
		if ($p = $this->parent) {
			$p->insertAt($content, $this->cnid, $this->chid, true);
		}
	}

	public function replaceAll($target)	{
		return $this->targetManipulation($target, false, false, false, false, true);
	}

	protected function targetManipulation($target, $append = true, $prepend = false, $after = false, $before = false, $replace = false)
	{
		// Prepare and check targets
		if (is_string($target)) {
			if (!$this->ownerDocument) {
				return $this;
			}
			$target = new kiSelector($target);
			$target = $target->find($this->ownerDocument);
		}
		if (!is_array($target)) {
			if ($target instanceof kiNodesList) {
				$target = $target->list;
			} else {
				$target = array($target);
			}
		} else {
			$list = new kiNodesList;
			$list->add($target);
			$target = $list->list;
			unset($list);
		}
		if ($target) {
			// Prepare object
			if ($this instanceof kiDocument) {
				$obj = new kiNodesList;
				$obj->add($this->nodes);
			} else {
				$obj = $this;
			}
			$return = null;
			foreach ($target as $t) {
				if (!($t instanceof kiNode)) {
					continue;
				}
				if ($append || $prepend) {
					if (!($t instanceof kiNodeTag) && !($t instanceof kiDocument)) {
						continue;
					}
					$o = $t;
				} else if (!$o = $t->parent) {
					continue;
				}
				if ($return === null) {
					$return = new kiNodesList();
					// Remove current element from DOM
					$obj->detach();
				} else {
					$obj = clone $obj;
				}
				$nodes = $obj;
				if ($replace) {
					$o->insertAt($nodes, $t->cnid, $t->chid, true);
				} else if ($append) {
					$o->insertAt($nodes, count($o->nodes), count($o->children));
				} else if ($prepend) {
					$o->insertAt($nodes, 0, 0);
				} else if ($after) {
					$o->insertAt($nodes, $t->cnid+1);
				} else if ($before) {
					$o->insertAt($nodes, $t->cnid, $t->chid);
				}
				if ($nodes) {
					$return->add($nodes);
				}
			}; unset($t);
			return $return === null ? $this : $return;
		}
		return $this;
	}

	public function appendTo($target)	{
		return $this->targetManipulation($target);
	}


	public function prependTo($target)	{
		return $this->targetManipulation($target, false, true);
	}

	public function insertAfter($target) {
		return $this->targetManipulation($target, false, false, true);
	}

	public function insertBefore($target) {
		return $this->targetManipulation($target, false, false, false, true);
	}

	protected function prepareWrapContent($content)	{
		// Prepare and check content
		if (is_string($content)) {
			$content = trim($content);
			if ($content[0] !== ki::$bracketOpen && $this->ownerDocument) {
				$content = new kiSelector($content);
				$content = $content->find($this->ownerDocument);
				$content = $content->list;
			} else {
				$dom = ki::fromString($content);
				$content = $dom->firstChild;
			}
		}
		if ($content instanceof kiNodesList) {
			$content = $content->list;
		}
		if (is_array($content)) {
			$content = reset($content);
		}
		if (!$content) {
			return false;
		}
		if ($content instanceof kiDocument) {
			if (!$content = $content->firstChild) {
				return false;
			}
		} else if (!($content instanceof kiNodeTag)) {
			return false;
		}
		$content = clone $content;
		$content->clearChildren();
		if (isset($dom)) {
			$dom->clear();
		}
		return $content;
	}

	public function wrap($content)	{
		if (!$p = $this->parent) {
			return $this;
		}
		// Prepare and check content
		if (!$content = $this->prepareWrapContent($content)) {
			return $this;
		}
		// Wrap
		$cnid = $this->cnid;
		$chid = $this->chid;
		$this->detach();
		$content->insertAt($this, 0, 0);
		$p->insertAt($content, $cnid, $chid);
		return $this;
	}

	public function wrapInner($content)	{
		// Prepare and check content
		if (!$content = $this->prepareWrapContent($content)) {
			return $this;
		}
		return $this->append($content->append($this->detachChildren()));
	}

	public function unwrap() {
		if ($p = $this->parent) {
			$p->replaceWith($this);
		}
		return $this;
	}

	public function detach()	{
		if ($parent = $this->parent) {
			$next = $this->next;
			$prev = $this->prev;

			if ($isTag = $this instanceof kiNodeTag) {
				$next ? $next->prev = $prev : $parent->lastChild  = $prev;
				$prev ? $prev->next = $next : $parent->firstChild = $next;
			}

			$nodes = &$parent->nodes;
			$cnid = $this->cnid;
			$chid = $this->chid !== -1;
			unset($parent->children[$this->chid], $nodes[$cnid]);
			while (isset($nodes[ ++$cnid ])) {
				$n = $nodes[$cnid];
				$n->cnid--;
				if ($chid && $n->chid !== -1) {
					$n->chid--;
				}
			}

			$parent->children = array_values($parent->children);
			$nodes = array_values($nodes);

			$this->chid   = -1;
			$this->cnid   = -1;
			$this->next   = null;
			$this->prev   = null;
			$this->parent = null;
		}

		return $this;
	}

	public function &detachChildren()	{
		foreach ($children = $this->nodes as $node) {
			$node->parent = null;
		}; unset($node);
		$this->firstChild = null;
		$this->lastChild  = null;
		$this->children   = array();
		$this->nodes      = array();
		return $children;
	}

	public function remove() {
		$this->detach();
		$this->clear();
	}

	public function find($selector, $n = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		$list = $selector->find($this, $n);
		return $n === null ? $list : $list->get($n);
	}

	public function is($selector)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		return $selector->match($this);
	}

	public function has($selector)	{
		if (!$this->children) {
			return false;
		}
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->children as $child) {
			if ($selector->match($child)) {
				return true;
			}
		}; unset($child);
		return false;
	}

	public function children($selector = null, $list = null)	{
		$list || $list = new kiNodesList($this);

		if ($this->children) {
			$list->add($this->children);
			if ($selector) {
				$list->filter($selector);
			}
		}

		return $list;
	}

	protected function getRelativeAll($type, $selector = null, $list = null)	{
		$list || $list = new kiNodesList($this);

		if ($node = $this->$type) {
			if ($selector && is_string($selector)) {
				$selector = new kiSelector($selector);
			}
			do {
				if ((!$selector || $selector->match($node)) && $node instanceof kiNodeTag) {
					$list->add($node);
				}
			} while ($node = $node->$type);
		}

		return $list;
	}

	protected function getRelativeUntil($type, $selector, $list = null)	{
		$list || $list = new kiNodesList($this);

		if ($node = $this->$type) {
			if (is_string($selector)) {
				$selector = new kiSelector($selector);
			}
			do {
				if (!($node instanceof kiNodeTag) || $selector->match($node)) {
					break;
				}
				$list->add($node);
			} while ($node = $node->$type);
		}

		return $list;
	}

	public function parents($selector = null, $list = null)	{
		return $this->getRelativeAll('parent', $selector, $list);
	}

	public function parentsUntil($selector, $list = null)	{
		return $this->getRelativeUntil('parent', $selector, $list);
	}

	public function closest($selector)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		$node = $this;
		do {
			if ($selector->match($node)) {
				return $node;
			}
		} while ($node = $node->parent);
		return null;
	}

	public function nextAll($selector = null, $list = null)	{
		return $this->getRelativeAll('next', $selector, $list);
	}

	public function nextUntil($selector, $list = null)	{
		return $this->getRelativeUntil('next', $selector, $list);
	}

	public function prevAll($selector = null, $list = null)	{
		return $this->getRelativeAll('prev', $selector, $list);
	}

	public function prevUntil($selector, $list = null)	{
		return $this->getRelativeUntil('prev', $selector, $list);
	}

	public function siblings($selector = null, $list = null)	{
		$list = $this->getRelativeAll('prev', $selector, $list);
		$list = $this->getRelativeAll('next', $selector, $list);
		return $list;
	}

	public function getElementById($id)	{
		return $this->find("#$id", 0);
	}

	public function getElementByTagName($name)	{
		return $this->find($name, 0);
	}

	public function getElementsByTagName($name, $n = null)	{
		return $this->find($name, $n);
	}

	public function dump($attributes = true, $text_nodes = true, $level = 0)	{
		if (!$text_nodes && $this->type === ki::NODE_TEXT) {
			return null;
		}

		if ($level === 0) {
			echo "\n";
		}

		/** @var $obj kiNodeTag|kiNode */
		$obj = $this;

		if ($isTag = ($obj->type === ki::NODE_ELEMENT)) {
			$current = ki::$bracketOpen . $obj->name;
		} else {
			$current = $obj->name;
		}
		echo str_repeat('    ', $level) , $current;
		if ($attributes && $obj->attributes) {
			echo $obj->attributes;
		}
		if ($isTag) {
			if ($obj->selfClosed) {
				echo ' /';
			}
			echo ki::$bracketClose;
		}
		echo "\n";

		if (count($obj->nodes)) {
			foreach ($obj->nodes as $node) {
				$node->dump($attributes, $text_nodes, $level+1);
			}; unset($node);
		}

		if ($level === 0) {
			echo "\n";
			PHP_SAPI === 'cli' && ob_get_level() > 0 && @ob_flush();
			return $this;
		}

		return null;
	}
 }

class kiDocument extends kiNode {
	public $isXml = false;
	public $name = 'document';
	public $type = ki::NODE_DOCUMENT;
	public function is($selector)
	{
		if (count($this->children) === 1) {
			return $this->firstChild->is($selector);
		}
		return false;
	}
}

class kiNodeTag extends kiNode
{
	public $type = ki::NODE_ELEMENT;
	public $selfClosed = false;
	public $isNamespaced = false;
	public $namespace;
	public $nameLocal;
	public $bracketOpen  = '<';
	public $bracketClose = '>';
	public function __construct($name, $closed = false)	{
		parent::__construct();
		$l_name = strtolower($name);
		if (!$closed && isset(ki::$selfClosingTags[$l_name])) {
			$closed = true;
		}
		$this->name 		= (string)$l_name;
		$this->nameReal 	= (string)$name;
		$this->selfClosed 	= (bool)$closed;
		if (($pos = strpos($l_name, ':')) !== false) {
			$this->namespace = substr($l_name, 0, $pos);
			$this->nameLocal = substr($l_name, $pos+1);
		} else {
			$this->nameLocal = (string)$l_name;
		}
	}

	public function outerHtml()	{
		$bo = ki::$bracketOpen;
		$bc = ki::$bracketClose;
		$html = $bo . $this->name . $this->attributes;

		if ($this->selfClosed) {
			$html .= ' /' . $bc;
		} else {
			// Iterate here, without calling html(), to speed up
			$html .= $bc;
			foreach ($this->nodes as $n) {
				$html .= $n->outerHtml();
			}; unset($n);
			$html .= $bo . '/' . $this->name . $bc;
		}

		return $html;
	}
}

class kiNodeXmlDeclaration extends kiNodeText {
	public $name = 'xml declaration';
	public $type = ki::NODE_XML_DECL;
	public function outerHtml()	{
		return '<?xml' . $this->value . '?>';
	}

	public function text($value = null)	{
		return ($value !== null) ? $this : '';
	}

	public function html($value = null)	{
		return ($value !== null) ? $this : '';
	}
}

class kiNodeDoctype extends kiNode {
	public $nameReal = 'DOCTYPE';
	public $name = 'doctype';
	public $type = ki::NODE_DOCTYPE;

	public function __construct($doctype)	{
		parent::__construct();
		$this->value = trim($doctype);
	}

	public function outerHtml()	{
		return '<!DOCTYPE ' . $this->value . '>';
	}

	public function text($value = null)	{
		return ($value !== null) ? $this : '';
	}

	public function html($value = null)	{
		return ($value !== null) ? $this : '';
	}
}

class kiNodeText extends kiNode {
	public $name = 'text';
	public $type = ki::NODE_TEXT;
	public function __construct($text = '')
	{
		parent::__construct();
		$this->value = (string)$text;
	}

	public function text($value = null)
	{
		// Set
		if ($value !== null) {
			// array
			if (is_array($value)) {
				$list = new kiNodesList();
				$list->list = $value;
				$value = $list->textAll();
			}
			// object
			else if (is_object($value)) {
				// nodes list
				if ($value instanceof kiNodesList) {
					$value = $value->textAll();
				}
				// node
				else {
					$value = $value->text();
				}
			}
			return $this->value = (string)$value;
		}
		// Get
		return $this->value;
	}

	public function html($value = null)
	{
		return $this->text($value);
	}
}

class kiNodeCdata extends kiNodeText
{
	public $name = 'cdata';
	public $type = ki::NODE_CDATA;
	public function outerHtml()	{
		return '<![CDATA[' . $this->value . ']]>';
	}
}

class kiNodeCommment extends kiNodeText {
	public $name = 'comment';
	public $type = ki::NODE_COMMENT;
	public function outerHtml()	{
		return '<!--' . $this->value . '-->';
	}

	public function text($value = null)	{
		return ($value !== null) ? $this : '';
	}

	public function html($value = null)	{
		return ($value !== null) ? $this : '';
	}
}

class kiAttribute {
	public $name;
	public $nameReal;
	protected $value;
	public $type = ki::NODE_ATTRIBUTE;
	public $node;

	public function __construct($name, $value = true, $real_name = null)
	{
		if ($real_name === null) {
			$real_name = $name;
			$name = strtolower($name);
		}
		$this->name = $name;
		$this->nameReal = $real_name;
		$this->value = $value;
	}

	public function value($value = null)
	{
		if ($value === null) {
			return $this->value === true ? $this->name : $this->value;
		}
		$this->value = $value;
		return null;
	}

	public function html()
	{
		if (($val = $this->value) === true) {
			if (empty($this->node->ownerDocument->isXml)) {
				return $this->name;
			}
			$val = $this->name;
		} else {
			if (is_array($val)) {$val=$val[0];}
			if (is_array($val)) {$val=implode("",$val);}
			$val = htmlSpecialChars($val, ENT_QUOTES, ki::CHARSET, false);
		}
		return $this->name . '="' . $val . '"';
	}

	public function text()	{
		return $this->value();
	}

	function __invoke($value = null)	{
		return $this->value($value);
	}

	function __toString()	{
		return $this->value();
	}
}

class kiSelector extends CLexer {
	const NODE = ki::NODE_ELEMENT;
	protected static $matchedSetFilters = array(
		'eq'    => true,
		'gt'    => true,
		'lt'    => true,
		'even'  => true,
		'odd'   => true,
		'first' => true,
		'last'  => true,
	);

	protected static $headers = array(
		'h1' => true,
		'h2' => true,
		'h3' => true,
		'h4' => true,
		'h5' => true,
		'h6' => true,
	);

	protected static $structCache = array();
	protected $struct;

	public function __construct($selector)	{
		$this->struct = $this->parseCssSelector($selector);
	}

	protected function parseCssSelector($selector)	{
		$selector = trim($selector);
		if (isset(self::$structCache[$selector])) {
			return self::$structCache[$selector];
		}
		if ($selector === '') {
			throw new InvalidArgumentException('Expects valid CSS selector expression.');
		}

		$this->string = &$selector;
		$this->length = strlen($selector);
		$this->chr = $this->string[0];
		$this->pos = 0;

		$chr = &$this->chr;
		$pos = &$this->pos;

		$mask_space = self::CHARS_SPACE;
		$mask_hierarchy = '~+>';
		$mask_eq = '=]*~$!^|';
		$mask = '.#[:,' . $mask_space . $mask_hierarchy;

		$struct = array();

		do {
			$cSel = array();
			$h = false;
			do {
				// Selector expression structure
				$sel = array(
					// Element
					'e' => '*',
					// Attributes
					'a' => array(),
					// Modifiers | pseudo-classes
					'm' => array(),
					// Matched set filters
					's' => array(),
					// Hierarchy
					'h' => $h,
					// Set limiter
					'l' => false,
				);

				// Element name
				$str = $this->getUntilChars($mask);
				if ($str !== '') {
					$sel['e'] = strtolower($str);
				}

				// Additional
				if ($chr !== null) {
					// Attributes & Modifiers
					do {
						// Class selector
						// Equivalent of [class~=value]
						if ($chr === '.') {
							$this->movePos();
							$str = $this->getUntilChars($mask);
							if ($str === '') {
								throw new InvalidArgumentException("Expects valid class name at pos #$pos.");
							}
							$sel['a']["class~=$str"] = array('class', $str, '~=');
						}

						// Id selector
						// Equivalent of [name="value"]
						else if ($chr === '#') {
							$this->movePos();
							$str = $this->getUntilChars($mask);
							if ($str === '') {
								throw new InvalidArgumentException("Expects valid id name at pos #$pos.");
							}
							$sel['a']["id=$str"] = array('id', $str, '=');
						}

						// Attribute selector
						else if ($chr === '[') {
							$eq = $value = '';

							// Name
							$this->movePos();
							$name = $this->getUntilChars($mask_eq);
							if ($name === '') {
								throw new InvalidArgumentException("Expects valid attribute name at pos #$pos.");
							}
							$name = strtolower($name);

							// Value
							if ($chr !== ']') {
								if ($chr !== '=') {
									$eq .= $chr;
									if ($this->movePos() !== '=') {
										throw new InvalidArgumentException("Expects equals sign at pos #$pos.");
									}
								}
								$eq .= $chr;
								$this->movePos();

								if ($chr === "'" || $chr === '"') {
									$quote = $chr;
									$this->movePos();
								} else {
									$quote = false;
								}

								if ($quote) {
									// Quoted parameter
									$value = $this->getUntilCharEscape($quote, $res, true);
									if (!$res) {
										throw new InvalidArgumentException(
											"Expects quote after parameter at pos #$pos."
										);
									}
									if ($chr !== ']') {
										$res = false;
									}
								} else {
									// Simple parameter
									$value = $this->getUntilString(']', $res, true);
								}
								if (!$res) {
									throw new InvalidArgumentException("Expects sign ']' at pos #$pos.");
								}
							}

							$this->movePos();

							$sel['a']["{$name}{$eq}{$value}"] = array($name, $value, $eq);
						}

						// Pseudo-class
						else if ($chr === ':') {
							// Name
							$this->movePos();
							$name = $this->getUntilChars($mask.'(');
							if ($name === '') {
								throw new InvalidArgumentException(
									"Expects valid pseudo-selector at pos #$pos."
								);
							}
							$name = strtolower($name);

							// Value
							if ($chr === '(') {
								$this->movePos();
								$value = $this->getUntilCharEscape(')', $res, true);
								if (!$res) {
									throw new InvalidArgumentException(
										"Expects closing bracket at pos #$pos."
									);
								}
							} else {
								$value = '';
							}

							if (isset(self::$matchedSetFilters[$name])) {
								$value = (int)$value;
								if (!$sel['s']) {
									if ($name === 'first') {
										$sel['l'] = 0;
									} else if (($name === 'eq' || $name === 'lt')) {
										$sel['l'] = $value;
									}
								}
								$sel['s'][] = array($name, $value);
							} else {
								$key = "{$name}={$value}";
								// Preparsing of recursive selectors
								if ($name === 'has' || $name === 'not') {
									$value = $this->parseCssSelector($value === '' ? '*' : $value);
								}
								// Preparsing of nth- rules
								else if (!strncmp($name, 'nth-', 4)) {
									$value = $this->parseNthRule($value);
								}
								$sel['m'][$key] = array($name, $value);
							}
						}

						// Break
						else {
							break;
						}
					} while (true);

					if ($chr !== null) {
						// Hierarchy
						$this->skipChars($mask_space);
						$continue = true;
						if ($chr === '~' || $chr === '+' || $chr === '>') {
							$h = $chr;
							$this->movePos();
							$this->skipChars($mask_space);
						} else {
							$h = false;
							// Next group
							if ($chr === ',') {
								$continue = false;
								$this->movePos();
								$this->skipChars($mask_space);
							}
						}
					} else {
						// End of
						$continue = false;
					}
				} else {
					// End of
					$continue = false;
				}

				// Cleanup keys used for dublicates filtration
				foreach (array('a', 'm', 's') as $k) {
					if ($sel[$k]) {
						$sel[$k] = array_values($sel[$k]);
					}
				}; unset($k);
				$cSel[] = $sel;
			} while ($continue);

			$struct[] = $cSel;
		} while ($chr !== null);

		return self::$structCache[$selector] = &$struct;
	}

	protected function parseNthRule($value)
	{
		if (is_numeric($value)) {
			return array(0, (int)$value);
		} else {
			$value = trim(strtolower($value));
			if ($value === 'odd') {
				return array(2, 1);
			} else if ($value === 'even') {
				return array(2, 0);
			}
			$regex = '/^(?:(?:([+-])?(\d+)|([+-]))?(n)(?:\s*([+-])\s*(\d+))?|([+-])?\s*(\d+))$/DSX';
			if (!preg_match($regex, $value, $m)) {
				return array(0, 0);
			}
			if ($m[4] === 'n') {
				if ($m[2] !== '') {
					$a = (int)($m[1].$m[2]);
				} else {
					$a = (int)($m[3].'1');
				}
				if (isset($m[6])) {
					$b = (int)($m[5].$m[6]);
				} else {
					$b = 0;
				}
			} else {
				$a = 1;
				$b = (int)($m[7].$m[8]);
			}
			return array($a, $b);
		}
	}

	public function find($context, $n = null, $result = null)	{
		// Prepare result
		$result || $result = new kiNodesList($context);
		// Speed up for needless searches
		if (!$context->children) {
			return $result;
		}
		// Element number, to return only, from full matched set
		$n === null || $n = (int)$n;
		// Iteration over independent selectors
		$rListByIds	= &$result->listByIds;
		foreach ($this->struct as $selector) {
			$e		   = $selector[0];
			$only	   = !isset($selector[1]);
			$setFilter = (bool)$e['s'];
			$en		   = ($e['l'] !== false) ? $e['l'] : null;
			$list	   = array();
			$res = $this->findNodes($context, $selector, $list, $n, $e, $only, $setFilter, $en);
			if ($list) {
				if ($rListByIds) {	$rListByIds += $list;} else {$rListByIds = $list;}
			}
			if (!$res) {break;}
		}; unset($selector);
		if ($rListByIds) {
			$result->list = array_values($rListByIds);
			$result->length = count($rListByIds);
		}
		return $result;
	}

	public function match($node) {
		return $this->nodeMatchSelector($node, $this->struct);
	}

	protected function findNodes($context, $selector, &$listByIds, $n, $e, $only, $setFilter, $en, $recursive = false) {
		// Iteration over nodes
		foreach ($context->children as $node) {
			if ($this->nodeMatchExpression($node, $e, $tree)) {
				// Simple match
				if ($only || $setFilter) {
					$listByIds[$node->uniqId] = $node;
					// speed up
					if ($en !== null && count($listByIds) > $en) {
						break;
					} else if (!$setFilter && $n !== null && count($listByIds) > $n) {
						return false;
					}
				}
				// Hierarchical match
				else {
					$nodes = array($node->uniqId => $node);
					$this->findNodesHierarchical($nodes, $selector);
					// Matches found
					if ($nodes) {
						$listByIds += $nodes;
					}
					// speed up
					if ($n !== null && count($listByIds) > $n) {
						return false;
					}
				}
			}
			if ($tree && $node->children) {
				$res = $this->findNodes($node, $selector, $listByIds, $n, $e, $only, $setFilter, $en, true);
				if (!$res) {
					return false;
				}
			}
			// speed up
			if ($en !== null && count($listByIds) > $en) {
				break;
			}
		}; unset($node);

		// Matched set filtraton
		if ($setFilter && !$recursive && $listByIds) {
			$this->matchedSetFilter($listByIds, $e['s']);
			if (!$only) {
				$this->findNodesHierarchical($listByIds, $selector);
			}
		}
		return true;
	}

	protected function findNodesHierarchical(&$listByIds, $selector) {
		// Shift first part
		unset($selector[0]);
		// Iteration over hierarchical parts of selector
		foreach ($selector as $e) {
			$list = array();
			// Ancestor descendant
			if (!$h = $e['h']) {
				foreach ($listByIds as $node) {
					$this->findDescedants($node, $e, $list);
				}; unset($node);
			}
			// parent > child
			else if ($h === '>') {
				foreach ($listByIds as $node) {
					foreach ($node->children as $child) {
						if ($this->nodeMatchExpression($child, $e)) {
							$list[$child->uniqId] = $child;
						}
					}; unset($child);
				}; unset($node);
			}
			// prev + next
			else if ($h === '+') {
				foreach ($listByIds as $node) {
					if (($next = $node->next) && $this->nodeMatchExpression($next, $e)) {
						$list[$next->uniqId] = $next;
					}
				}; unset($node);
			}
			// prev ~ siblings
			else if ($h === '~') {
				foreach ($listByIds as $next) {
					while (($next = $next->next) !== null) {
						if ($this->nodeMatchExpression($next, $e)) {
							$list[$next->uniqId] = $next;
						}
					}
				}; unset($next);
			}

			// nothing found
			if (!$listByIds = $list) {
				break;
			}

			// Matched set filtration
			if ($e['s']) {
				$this->matchedSetFilter($listByIds, $e['s']);
			}
		}; unset($e);
	}

	protected function matchedSetFilter(&$listByIds, $filters)	{
		foreach ($filters as $f) {
			list($name, $value) = $f;

			// :first
			if ($name === 'first') {
				// We always have only one item here
//				if (count($listByIds) > 1) {
//					if ($node = reset($listByIds)) {
//						$listByIds = array($node->uniqId => $node);
//					} else {
//						$listByIds = array();
//					}
//				}
			}
			// :last
			else if ($name === 'last') {
				$node = end($listByIds);
				$listByIds = array(
					$node->uniqId => $node,
				);
			}
			// :eq()
			else if ($name === 'eq') {
				$listByIds = array_slice($listByIds, $value, 1, true);
			}
			// :lt()
			else if ($name === 'lt') {
				$listByIds = array_slice($listByIds, 0, $value, true);
			}
			// :gt()
			else if ($name === 'gt') {
				$listByIds = array_slice($listByIds, $value+1, null, true);
			}
			// :odd
			else if ($name === 'odd') {
				$i = 0;
				foreach($listByIds as $key => $node) {
					if (!($i & 1)) {
						unset($listByIds[$key]);
					}
					$i++;
				}
			}
			// :even
			else if ($name === 'even') {
				$i = 0;
				foreach($listByIds as $key => $node) {
					if ($i & 1) {
						unset($listByIds[$key]);
					}
					$i++;
				}
			}

			// speed up
			if (!$listByIds) {
				return;
			}
		}; unset($f);
	}

	protected function nodeMatchSelector($node, $struct)	{
		// Iteration over independent selectors
		foreach ($struct as $selector) {
			if (!$only = !isset($selector[1])) {
				foreach ($selector as $k => &$e) {
					if (isset($selector[$k+1])) {
						$e['h'] = $selector[$k+1]['h'];
					}
				}
				unset($k, $e);
				$selector = array_reverse($selector);
			}
			$e = $selector[0];
			if ($this->nodeMatchExpression($node, $e)) {
				// Simple match
				if ($only) {
					return true;
				}
				// Hierarchy
				else {
					unset($selector[0]);
					/** @var $nodes kiNodeTag[] */
					$nodes = array($node);
					foreach ($selector as $k => $e) {
						$list = array();
						// Ancestor descendant
						if (!$h = $e['h']) {
							foreach ($nodes as $_n) {
								$this->findAncestors($_n, $e, $list);
							}; unset($_n);
						}
						// parent > child
						else if ($h === '>') {
							foreach ($nodes as $_n) {
								if ((($parent = $_n->parent) && $parent->type === self::NODE)
									&& $this->nodeMatchExpression($parent, $e)
								) {
									$list[] = $parent;
								}
							}; unset($_n);
						}
						// prev + next
						else if ($h === '+') {
							foreach ($nodes as $_n) {
								if (($prev = $_n->prev) && $this->nodeMatchExpression($prev, $e)) {
									$list[] = $prev;
								}
							}; unset($_n);
						}
						// prev ~ siblings
						else if ($h === '~') {
							foreach ($nodes as $prev) {
								while (($prev = $prev->prev) !== null) {
									if ($this->nodeMatchExpression($prev, $e)) {
										$list[] = $prev;
									}
								}
							}; unset($prev);
						}

						if (!$nodes = $list) {
							// nothing found
							break;
						}
					}; unset($e);
					if ($nodes) {
						return true;
					}
				}
			}
		}; unset($selector);

		return false;
	}

	protected function nodeMatchExpression($node, $e, &$tree = null) {
		$tree = true;
		// Element name
		if ($e['e'] !== '*' && $node->name !== $e['e']) {
			return false;
		}
		// Attributes
		if ($e['a']) {
			foreach ($e['a'] as $a) {
				$search = (string)$a[1];
				$has = ($attrs = $node->attributes) && isset($attrs->list[$a[0]]);

				// Attribute with any value
				if (!$eq = $a[2]) {
					if (!$has) {
						return false;
					}
					continue;
				}

				/** @noinspection PhpUndefinedMethodInspection */
				$val = $has ? $attrs->list[$a[0]]->value() : '';

				// Exactly not equal
				if ($eq === '!=') {
					if ($val === $search) {
						return false;
					}
				}
				// Speed up - empty val, but not empty search
				else if ($val === '' && $search !== '') {
					return false;
				}
				// Exactly equal
				else if ($eq === '=') {
					if ($val !== $search) {
						return false;
					}
				}
				// Containing a given substring
				else if ($eq === '*=') {
					if (strpos($val, $search) === false) {
						return false;
					}
				}
				// Space-separated values, one of which is exactly equal
				else if ($eq === '~=') {
					$regex = '/(?:^|\s)' . preg_quote($search, '/') . '(?:\s|$)/DSXu';
					if (!preg_match($regex, $val)) {
						return false;
					}
				}
				// Exactly equal at start
				else if ($eq === '^=') {
					if (strncmp($val, $search, strlen($search))) {
						return false;
					}
				}
				// Exactly equal at end
				else if ($eq === '$=') {
					if (substr_compare($val, $search, -strlen($search))) {
						return false;
					}
				}
				// Hyphen-separated list of values beginning (from the left) with a given string
				else if ($eq === '|=') {
					$regex = '/^' . preg_quote($search, '/') . '(?:-|$)/DSXu';
					if (!preg_match($regex, $val)) {
						return false;
					}
				}
			}; unset($a);
		}

		// Modifiers / pseudo-classes
		if ($e['m']) {
			foreach ($e['m'] as $m) {
				list($name, $value) = $m;
				// elements that have no children (including text nodes).
				if ($name === 'empty') {
					if ($node->nodes) {
						return false;
					}
				}
				// elements that are the parent of another element, including text nodes.
				else if ($name === 'parent') {
					if (!$node->nodes) {
						return false;
					}
				}
				// elements that are headers, like h1, h2, h3 and so on. (jQuery Selector Extensions)
				else if ($name === 'header') {
					if (!isset(self::$headers[$node->name])) {
						return false;
					}
				}
				// elements that contain the specified text (case-sensitive)
				else if ($name === 'contains') {
					if ($value !== '' && mb_strpos($node->text(), $value, null, ki::CHARSET) === false) {
						$tree = false;
						return false;
					}
				}
				// elements that do not match the given selector.
				else if ($name === 'not') {
					if ($this->nodeMatchSelector($node, $value)) {
						return false;
					}
				}
				// elements which contain at least one element that matches the specified selector.
				else if ($name === 'has') {
					if (!$node->children) {
						return false;
					}
					// speed up
					if ($value[0][0]['e'] === '*' && count($value) === 1 && count($value[0]) === 1) {
						return true;
					}
					$res = false;
					foreach ($node->children as $child) {
						if ($this->nodeMatchSelector($child, $value)) {
							$res = true;
							break;
						}
					}; unset($child);
					if (!$res) {
						return false;
					}
				}

				/*
				 * Speed up for -childs checks (Structural pseudo-classes)
				 * search only if the parent is element or has multiple children
				 *
				 * @link http://www.w3.org/TR/css3-selectors/#structural-pseudos
				 */
				else if (!($parent = $node->parent) || ($parent->type !== self::NODE
					&& count($parent->children) < 2)
				) {
					return false;
				}

				// elements that are the first child of their parent.
				else if ($name === 'first-child') {
					if ($node->prev) {
						return false;
					}
				}
				// elements that are the last child of their parent.
				else if ($name === 'last-child') {
					if ($node->next) {
						return false;
					}
				}
				// elements that are the only child of their parent.
				else if ($name === 'only-child') {
					if ($node->prev || $node->next) {
						return false;
					}
				}
				// elements that are the nth-child of their parent.
				else if ($name === 'nth-child') {
					if (!$this->numberMatchNthRule($node->chid+1, $value)) {
						return false;
					}
				}
				// elements that are the nth-child of their parent from end.
				else if ($name === 'nth-last-child') {
					if (!$this->numberMatchNthRule(count($parent->children)-$node->chid, $value)) {
						return false;
					}
				}
				// elements that are the only child of their parent of specified type.
				else if ($name === 'only-of-type') {
					$chid = $node->chid;
					$type = $node->name;
					$res = false;
					$_node = $node->parent->firstChild;
					do {
						if ($_node->name === $type) {
							if ($res) {
								return false;
							} else if ($_node->chid === $chid) {
								$res = true;
							} else {
								return false;
							}
						}
					} while ($_node = $_node->next);
				}
				// group of type checks
				else if (!substr_compare($name, '-of-type', -8)) {
					if (strncmp($name, 'nth-', 4)) {
						$value = array(0, 1);
					}
					$break = !$value[0];
					$chid = $node->chid;
					$type = $node->name;
					$i = 1;
					/**
					 * nth-of-type, first-of-type
					 *
					 * :nth-of-type(an+b|even|odd)
					 * elements that are the nth-child of their parent of specified type.
					 *
					 * :first-of-type
					 * elements that are the first child of their parent of specified type.
					 */
					if (strpos($name, 'last-') === false) {
						$first = 'firstChild';
						$next = 'next';
					}
					/**
					 * nth-last-of-type, last-of-type
					 *
					 * :nth-last-of-type(an+b|even|odd)
					 * elements that are the nth-child of their parent of specified type from end.
					 *
					 * :last-of-type
					 * elements that are the last child of their parent of specified type.
					 */
					else {
						$first = 'lastChild';
						$next = 'prev';
					}

					/** @noinspection PhpUndefinedVariableInspection */
					$_node = $node->parent->$first;
					do {
						if ($_node->name === $type) {
							if ($_node->chid === $chid) {
								if (!$this->numberMatchNthRule($i, $value)) {
									return false;
								}
								break;
							}
							// speed up
							if ($break && $i >= $value[1]) {
								return false;
							}
							$i++;
						}
					} while ($_node = $_node->$next);
				}
			}; unset($m);
		}

		return true;
	}

	protected function numberMatchNthRule($n, $rule)	{
		list($a, $b) = $rule;
		return !$a ? $b == $n : !(($d = $n - $b) % $a) && $d / $a >= 0;
	}

	protected function findDescedants($node, $e, &$list = null)
	{
		$list !== null || $list = array();

		foreach ($node->children as $child) {
			if ($this->nodeMatchExpression($child, $e)) {
				$list[$child->uniqId] = $child;
			}
			$this->findDescedants($child, $e, $list);
		}; unset($child);
	}

	protected function findAncestors($node, $e, &$list = null)
	{
		$list !== null || $list = array();

		if (($parent = $node->parent) && $parent->type === self::NODE) {
			if ($this->nodeMatchExpression($parent, $e)) {
				$list[] = $parent;
			}
			$this->findAncestors($parent, $e, $list);
		}
	}
}

abstract class kiList implements Iterator, ArrayAccess, Countable {
	public $length = 0;
	public $list = array();
	public function get($name)
	{
		return isset($this->list[$name]) ? $this->list[$name] : null;
	}
	abstract function set($name, $value);
	public function has($name)
	{
		return isset($this->list[$name]);
	}
	abstract function delete($name);
	public function __toString()
	{
		return $this->html();
	}
	abstract function text();
	abstract function html();
	public function __get($name)	{
		return $this->get($name);
	}
	public function __set($name, $value)	{
		return $this->set($name, $value);
	}
	public function __isset($name)	{
		return $this->has($name);
	}
	public function __unset($name)	{
		return $this->delete($name);
	}
	public function count()	{
		return count($this->list);
	}

	public function current()	{
		return current($this->list);
	}

	public function key()	{
		return key($this->list);
	}

	public function next()	{
		return next($this->list);
	}

	public function offsetExists($offset)	{
		return $this->has($offset);
	}

	public function offsetGet($offset)	{
		return $this->get($offset);
	}

	public function offsetSet($offset, $value)	{
		return $this->set($offset, $value);
	}

	public function offsetUnset($offset)	{
		return $this->delete($offset);
	}

	public function rewind()	{
		return reset($this->list);
	}

	public function valid()	{
		return key($this->list) !== null;
	}
}


class kiAttributesList extends kiList {
	public $node;
	public function __clone()
	{
		$node = null;
		$this->node = &$node;
		foreach ($this->list as &$attr) {
			$attr = clone $attr;
			$attr->node = &$node;
		}; unset($attr);
	}

	public function get($name)	{
		$name_l = strtolower($name);
		return isset($this->list[$name_l]) ? $this->list[$name_l] : null;
	}

	public function set($name, $value)	{
		$name_l = strtolower($name);

		if (isset($this->list[$name_l])) {
			$attr = $this->list[$name_l];
			$attr->value($value);
			$attr->nameReal = $name;
		} else {
			$attr = new kiAttribute($name_l, $value, $name);
			$attr->node = &$this->node;
			$this->list[$name_l] = $attr;
		}
	}

	public function has($name)	{
		$name_l = strtolower($name);
		return isset($this->list[$name_l]);
	}

	public function delete($name)	{
		$name_l = strtolower($name);
		unset($this->list[$name_l]);
	}

	public function text()	{
		return $this->html();
	}

	function html()	{
		$html = '';

		foreach ($this->list as $attr) {
			$html .= ' ' . $attr->html();
		}; unset($attr);

		return $html;
	}
}


/**
 * ki attributes list
 *
 * @method kiNodeTag   get(int $n) Returns node by it's position in list.
 * @method kiNodesList clone()     Create a deep copy of the set of elements.
 * @method kiNodesList empty()     Remove all child nodes of the set of elements from the DOM.
 * @method int           size()      Returns size of elements in the list. Use 'length' property instead of this method.
 *
 * @property kiNodeTag[]     $list       Internal nodes list.
 * @property kiNodeTag|null  $first      First node in list.
 * @property kiNodeTag |null $last       Last node in list.
 * @property kiNodeTag|null  $prev       The node immediately preceding first node in the list. NULL if there is no such node.
 * @property kiNodeTag|null  $next       The node immediately following first node in the list. NULL if there is no such node.
 * @property kiNodeTag|null  $firstChild The first child of first node in the list. NULL if there is no such node.
 * @property kiNodeTag|null  $lastChild  The last child of first node in the list. NULL if there is no such node.
 * @property kiNode|null     $firstNode  First child node of first node in the list. NULL if there is no such node.
 * @property kiNode|null     $lastNode   Last child node of first node in the list. NULL if there is no such node.
 */
class kiNodesList extends kiList
{
	public $listByIds = array();
	public $ownerDocument;
	public $context;
	protected $state;
	public function __construct($context = null)	{
		if ($context) {
			$this->context = $context;
			$this->ownerDocument = $context->ownerDocument;
		}
	}

	public function __call($name, $arguments)	{
		$name_l = strtolower($name);
		// clone
		if ($name_l === 'clone') {
			return clone $this;
		}
		// empty
		else if ($name_l === 'empty') {
			foreach ($this->list as $node) {
				$node->clearChildren();
			}; unset($node);
			return $this;
		}
		// size
		else if ($name_l === 'size') {
			return $this->length;
		}
		throw new BadMethodCallException('Method "'.get_class($this).'.'.$name.'" is not defined.');
	}

	public function &__get($name)	{
		$name_l = strtolower($name);

		// Last
		if ($name_l === 'last') {
			$res = ($k = $this->length) ? $this->list[$k-1] : null;
			return $res;
		}
		// First, attributes and properties
		else if (isset($this->list[0])) {
			$first = $this->list[0];
			// First
			if ($name_l === 'first') {
				return $first;
			}
			// Properties
			else if (isset($first->$name)) {
				return $first->$name;
			}
			// Attributes
			$val = $this->list[0]->attr($name_l, null, true);
			return $val;
		}

		$res = null;
		return $res;
	}

	public function __set($name, $value)	{
		if (isset($this->list[0])) {
			$first = $this->list[0];
			// Properties
			if (isset($first->$name)) {
				$first->$name = $value;
			}
			// Attributes
			else {
				$this->list[0]->attr($name, $value);
			}
		}
	}

	public function __clone()	{
		$this->listByIds = array();
		foreach ($this->list as &$node) {
			$node = clone $node;
			$this->listByIds[$node->uniqId] = $node;
		}; unset($node);
	}

	public function __invoke($selector, $n = null)	{
		return $this->find($selector, $n);
	}

	public function __toString()	{
		return $this->htmlAll();
	}

	public function removeClass($class) {
		$res=false;
		$classes=explode(" ",trim($this->class));
		foreach($classes as $k => $val) {
			if ($val==$class) {unset($classes[$k]);}
		}; unset($val);
		$this->class=trim(implode(" ",$classes));
		if ($this->class=="") {$this->removeAttr("class");}
	}

	public function addClass($class) {
		$res=false;
		$list=explode(" ",trim($this->class));
		foreach($list as $k => $val) {
			if ($val==$class) {$res=true;}
		}; unset($val,$list);
		if ($res==false) {$this->class=trim($this->class." ".$class);}
	}

	public function add($value)
	{
		if ($value instanceof self) {
			$value = $value->list;
		}
		if (is_array($value)) {
			foreach ($value as $node) {
				if ($node instanceof kiNode && !isset($this->listByIds[$uid = $node->uniqId])) {
					$this->listByIds[$uid] = $node;
					$this->list[] = $node;
				}
			}; unset($node);
		} else if ($value instanceof kiNode && !isset($this->listByIds[$uid = $value->uniqId])) {
			$this->listByIds[$uid] = $value;
			$this->list[] = $value;
		}
		$this->length = count($this->list);
		return $this;
	}

	public function delete($n)	{
		if (!($n instanceof kiNode)) {
			$n = isset($this->list[$n]) ? $this->list[$n] : null;
		}
		if ($n && isset($this->listByIds[$n->uniqId])) {
			unset($this->listByIds[$n->uniqId]);
			$this->list = array_values($this->listByIds);
			$this->length = count($this->list);
		}
		return $this;
	}

	public function set($name, $value)	{
		return $this;
	}

	public function text($value = null)	{
		if ($this->length > 0) {
			$res = $this->list[0]->text($value);
			return ($value === null) ? $res : $this;
		}
		return ($value === null) ? '' : $this;
	}

	public function html($value = null)	{
		if ($this->length > 0) {
			$res = $this->list[0]->html($value);
			return ($value === null) ? $res : $this;
		}
		return ($value === null) ? '' : $this;
	}

	public function outerHtml()	{
		if ($this->length > 0) {
			return $this->list[0]->outerHtml();
		}
		return '';
	}

	public function textAll()	{
		$string = '';
		foreach ($this->list as $node) {
			$string .= $node->text();
		}; unset($node);
		return $string;
	}

	public function htmlAll()	{
		$string = '';
		foreach ($this->list as $node) {
			$string .= $node->html();
		}; unset($node);
		return $string;
	}

	public function outerHtmlAll()	{
		$string = '';
		foreach ($this->list as $node) {
			$string .= $node->outerHtml();
		}; unset($node);
		return $string;
	}

	public function attr($name, $value = null, $toString = true)	{
		if (isset($this->list[0])) {
			return $this->list[0]->attr($name, $value, $toString);
		}
		return $value === null ? null : $this;
	}

	public function removeAttr($name)	{
		foreach ($this->list as $node) {
			$node->attributes && $node->attributes->delete($name);
		}; unset($node);
		return $this;
	}

	public function hasAttribute($name)	{
		/** @noinspection PhpUndefinedMethodInspection */
		return isset($this->list[0])
			   && $this->list[0]->attributes
			   && $this->list[0]->attributes->has($name);
	}

	protected function prepareContent($content)	{
		if (!($content instanceof kiNodesList)) {
			// Document
			if ($content instanceof kiDocument) {
				$content = &$content->detachChildren();
			}
			// String
			else if (!is_object($content) && !is_array($content)) {
				$content = ki::fromString($content);
				$content = &$content->detachChildren();
			}
			$list = new self;
			$list->add($content);
			$content = $list;
		}
		return $content->length ? $content : false;
	}

	protected function prepareTarget($target)	{
		if (is_string($target)) {
			if (!isset($this->list[0])
				|| !$od = $this->list[0]->ownerDocument
			) {
				return false;
			}
			$target = new kiSelector($target);
			$target = $target->find($od);
		}
		if (!is_array($target)) {
			$target = ($target instanceof kiNodesList)
					? $target->list
					: array($target);
		}
		return $target;
	}

	public function after($content)	{
		if ($content = $this->prepareContent($content)) {
			foreach ($this->list as $i => $node) {
				if ($i !== 0) {
					$content = clone $content;
				}
				$node->after($content->list);
			}; unset($node);
		}
		return $this;
	}

	public function before($content)	{
		if ($content = $this->prepareContent($content)) {
			foreach ($this->list as $i => $node) {
				if ($i !== 0) {
					$content = clone $content;
				}
				$node->before($content->list);
			}; unset($node);
		}
		return $this;
	}

	public function insertAfter($target)	{
		if ($target = $this->prepareTarget($target)) {
			/** @var $list kiNode[] */
			$list = array_reverse($this->resetState());
			foreach ($list as $node) {
				$res = $node->insertAfter($target);
				$this->add($res);
			}; unset($node);
			$this->list = array_reverse($this->list);
			$this->listByIds = array_reverse($this->listByIds, true);
		}
		return $this;
	}

	public function insertBefore($target)	{
		if ($target = $this->prepareTarget($target)) {
			$list = $this->resetState();
			foreach ($list as $node) {
				$res = $node->insertBefore($target);
				$this->add($res);
			}; unset($node);
		}
		return $this;
	}

	public function append($content)	{
		if ($content = $this->prepareContent($content)) {
			foreach ($this->list as $i => $node) {
				if ($i !== 0) {
					$content = clone $content;
				}
				$node->append($content->list);
			}; unset($node);
		}
		return $this;
	}

	public function prepend($content)	{
		if ($content = $this->prepareContent($content)) {
			foreach ($this->list as $i => $node) {
				if ($i !== 0) {
					$content = clone $content;
				}
				$node->prepend($content->list);
			}; unset($node);
		}
		return $this;
	}

	public function appendTo($target)	{
		if ($target = $this->prepareTarget($target)) {
			$list = $this->resetState();
			foreach ($list as $node) {
				$res = $node->appendTo($target);
				$this->add($res);
			}; unset($node);
		}
		return $this;
	}

	public function prependTo($target)	{
		if ($target = $this->prepareTarget($target)) {
			/** @var $list kiNode[] */
			$list = array_reverse($this->resetState());
			foreach ($list as $node) {
				$res = $node->prependTo($target);
				$this->add($res);
			}; unset($node);
			$this->list = array_reverse($this->list);
			$this->listByIds = array_reverse($this->listByIds, true);
		}
		return $this;
	}

	public function replaceAll($target)	{
		if ($target = $this->prepareTarget($target)) {
			$list = $this->resetState();
			$last = null;
			foreach ($list as $i => $node) {
				if ($i === 0) {
					$res = $node->replaceAll($target);
				} else {
					$res = $node->insertAfter($last);
				}
				$last = $res;
				$this->add($res);
			}; unset($node);
		}
		return $this;
	}

	public function replaceWith($content)	{
		if ($content = $this->prepareContent($content)) {
			foreach ($this->list as $i => $node) {
				if ($i !== 0) {
					$content = clone $content;
				}
				$node->replaceWith($content->list);
			}; unset($node);
		}
		return $this;
	}

	public function wrap($content)	{
		if ($content = $this->prepareContent($content)) {
			$content = $content[0];
			foreach ($this->list as $i => $node) {
				if ($i !== 0) {
					$content = clone $content;
				}
				$node->wrap($content);
			}; unset($node);
		}
		return $this;
	}

	public function wrapAll($content)	{
		if (isset($this->list[0]) && $content = $this->prepareContent($content)) {
			$first = $this->list[0];
			if ($p = $first->parent) {
				$content = $content[0];
				$content->insertBefore($first)->append($this->list);
			}
		}
		return $this;
	}

	public function wrapInner($content)	{
		if ($content = $this->prepareContent($content)) {
			$content = $content[0];
			foreach ($this->list as $i => $node) {
				if ($i !== 0) {
					$content = clone $content;
				}
				$node->wrapInner($content);
			}; unset($node);
		}
		return $this;
	}

	public function unwrap() {
		foreach ($this->list as $node) {
			$node->unwrap();
		}; unset($node);
		return $this;
	}

	public function detach()	{
		foreach ($this->list as $node) {
			$node->detach();
		}; unset($node);
		return $this;
	}

	public function remove()	{
		foreach ($this->list as $node) {
			$node->remove();
		}; unset($node);
		$this->resetState();
		$this->state = null;
		return $this;
	}

	protected function saveState()	{
		$list = $this->list;
		$this->state = array(
			$this->listByIds,
			$list,
		);
		$this->listByIds = array();
		$this->list      = array();
		$this->length    = 0;
		return $list;
	}

	protected function resetState()	{
		$list = $this->list;
		$this->listByIds = array();
		$this->list      = array();
		$this->length    = 0;
		return $list;
	}

	public function end()	{
		if ($this->state) {
			list(
				$this->listByIds,
				$this->list
			) = $this->state;
			$this->length = count($this->list);
		}
		return $this;
	}

	public function andSelf()	{
		if ($this->state) {
			$this->add($this->state[0]);
		}
		return $this;
	}

	public function eq($n)	{
		$list = $this->saveState();
		$n >= 0 || $n = count($list) + $n;
		if ($el = isset($list[$n]) ? $list[$n] : null) {
			$this->add($el);
		}
		return $this;
	}

	public function first()	{
		$list = $this->saveState();
		if ($el = reset($list)) {
			$this->add($el);
		}
		return $this;
	}

	public function last()	{
		$list = $this->saveState();
		if ($el = end($list)) {
			$this->add($el);
		}
		return $this;
	}

	public function slice($offset, $length = null)	{
		$list = $this->saveState();
		if ($list = array_slice($list, $offset, $length)) {
			$this->add($list);
		}
		return $this;
	}

	public function index($node)	{
		if ($node instanceof kiNodesList) {
			$node = reset($node->list);
		}
		if ($node && isset($this->listByIds[$uid = $node->uniqId])) {
			$i = 0;
			foreach ($this->listByIds as $uniqId => $n) {
				if ($uniqId === $uid) {
					return $i;
				}
				$i++;
			}; unset($n);
		// @codeCoverageIgnoreStart
		}
		// @codeCoverageIgnoreEnd
		return -1;
	}

	public function find($selector, $n = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			$selector->find($node, $n, $this);
			if ($n && $this->length > $n) {
				break;
			}
		}; unset($node);
		return $n === null ? $this : $this->list[$n];
	}

	public function filter($selector, $saveState = true)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		$list = $saveState ? $this->saveState() : $this->resetState();
		foreach ($list as $node) {
			if ($selector->match($node)) {
				$this->add($node);
			}
		}; unset($node);
		return $this;
	}

	public function not($selector)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			if (!$selector->match($node)) {
				$this->add($node);
			}
		}; unset($node);
		return $this;
	}

	public function is($selector)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->list as $node) {
			if ($selector->match($node)) {
				return true;
			}
		}; unset($node);
		return false;
	}

	public function children($selector = null)	{
		foreach ($this->saveState() as $node) {
			$node->children(null, $this);
		}; unset($node);
		if ($selector) {
			$this->filter($selector, false);
		}
		return $this;
	}

	public function contents()	{
		foreach ($this->saveState() as $node) {
			if ($node->nodes) {
				$this->add($node->nodes);
			}
		}; unset($node);
		return $this;
	}

	public function getNext($selector = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			if (($next = $node->next) && (!$selector || $selector->match($next))) {
				$this->add($next);
			}
		}; unset($node);
		return $this;
	}

	public function nextAll($selector = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			$node->nextAll($selector, $this);
		}; unset($node);
		return $this;
	}

	public function nextUntil($selector)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			$node->nextUntil($selector, $this);
		}; unset($node);
		return $this;
	}

	public function getPrev($selector = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			if (($prev = $node->prev) && (!$selector || $selector->match($prev))) {
				$this->add($prev);
			}
		}; unset($node);
		return $this;
	}

	public function prevAll($selector = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			$node->prevAll($selector, $this);
		}; unset($node);
		return $this;
	}

	public function prevUntil($selector)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			$node->prevUntil($selector, $this);
		}; unset($node);
		return $this;
	}

	public function parent($selector = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			if (($parent = $node->parent) && (!$selector || $selector->match($parent))) {
				$this->add($parent);
			}
		}; unset($node);
		return $this;
	}

	public function parents($selector = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			$node->parents($selector, $this);
		}; unset($node);
		return $this;
	}

	public function parentsUntil($selector)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			$node->parentsUntil($selector, $this);
		}; unset($node);
		return $this;
	}

	public function closest($selector)	{
		if (isset($this->list[0])) {
			return $this->list[0]->closest($selector);
		}
		return null;
	}

	public function siblings($selector = null)	{
		if (is_string($selector)) {
			$selector = new kiSelector($selector);
		}
		foreach ($this->saveState() as $node) {
			$node->siblings($selector, $this);
		}; unset($node);
		return $this;
	}

	public function dump($attributes = true, $text_nodes = true)	{
		if ($this->length) {
			foreach ($this->list as $node) {
				$node->dump($attributes, $text_nodes);
			}; unset($node);
		} else {
			echo "\n";
		}
		echo 'NodesList dump: ' , $this->length , "\n";
		PHP_SAPI === 'cli' && ob_get_level() > 0 && @ob_flush();
	}
}

// Класс для работы $_SESSION через memcache
/*class MemcachedSessionHandler implements \SessionHandlerInterface
{

    private $memcached;
    private $ttl;
    private $prefix;

    public function __construct(
        \Memcached $memcached,
        $expiretime = 86400,
        $prefix = 'sess_')
    {
        $this->memcached = $memcached;
        $this->ttl = $expiretime;
        $this->prefix = $prefix;
        $this->useMe();
    }

    public function open($savePath, $sessionName)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($sessionId)
    {
        return $this->memcached->get($this->prefix . $sessionId) ? : '';
    }

    public function write($sessionId, $data)
    {
        return $this->memcached->set(
          $this->prefix . $sessionId,
          $data,
          time() + $this->ttl);
    }

    public function destroy($sessionId)
    {
        return $this->memcached->delete($this->prefix . $sessionId);
    }

    public function gc($lifetime)
    {
        return true;
    }

    private function useMe()
    {
        session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );

        register_shutdown_function('session_write_close');
    }
}

*/
?>
