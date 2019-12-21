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
 * @link https://github.com/aikianapa/wb/
 * @link http://wbcms.online/
 *
 *
 * @license MIT
 */

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
        if (is_array($string)) $string=implode(" ",$string);
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
            if (is_array($markup)) {
                $tmp=substr(implode(" ",$markup),0,1000);
            } else {
                $tmp=substr($markup,0,1000);
            }
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
        $res = "";
        if ($file=="") {
            return ki::fromString("");
        } else {
            if (!session_id()) {
                session_start();
            }
            $context = stream_context_create(array(
                                                 "{$ENV['route']['scheme']}"=>array(
                                                         'method'=>"POST",
                                                         'header'=>	'Accept-language:' . " en\r\n" .
                                                         'Content-Type:' . " application/x-www-form-urlencoded\r\n" .
                                                         'Cookie: ' . session_name()."=".session_id()."\r\n" .
                                                         'Connection: ' . " Close\r\n\r\n",
                                                         'content' => http_build_query($_POST)
                                                 ),
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
                                             ));
            session_write_close();
            $url=parse_url($file);
            if (is_file($file)) {
                $fp = fopen ($file,"r");
                flock ($fp, LOCK_SH);
            }
            if (isset($url["scheme"])) {
                $res=@file_get_contents($file,false,$context);
            } else if (is_file($file)) {
                $res=file_get_contents($file);
            }
            if (is_file($file)) {
                $res=file_get_contents($file,false,$context);
                flock ($fp, LOCK_UN);
                fclose ($fp);
            }
            @session_start(); // reopen session after session_write_close()
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
            };
            unset($ch);
            $this->debug('Detecting charset settings in document: ' . join(', ', $docCharsets));
        } else {
            // is it UTF-8 ?
            // @link http://w3.org/International/questions/qa-forms-utf-8.html
            if (preg_match('~^(?:
                           [\x09\x0A\x0D\x20-\x7E]            # ASCII
                           | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                           | \xE0[\xA0-\xBF][\x80-\xBF]         # excluding overlongs
                           | [\xE1-\xEC\xEE\xEF][\x80-\xBF] {2}  # straight 3-byte
                           | \xED[\x80-\x9F][\x80-\xBF]         # excluding surrogates
                           | \xF0[\x90-\xBF][\x80-\xBF] {2}      # planes 1-3
                           | [\xF1-\xF3][\x80-\xBF] {3}          # planes 4-15
                           | \xF4[\x80-\x8F][\x80-\xBF] {2}      # plane 16
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
            };
            unset($charset);
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
            };
            unset($val);
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
        if (($name = $this->getUntilChars($chars_eq)) === '') {
            break;
        }
        if (strpos($chars_space, $chr) !== false) {
            $this->skipChars($chars_space);
        }
        if ($chr !== '=') {
            if ($attributes === null) {
                $attributes = new kiAttributesList;
            }
            $attributes->set($name, true);
            if ($chr === $bc || $chr === '/') {
                break;
            }
            continue;
        }

        $this->movePos();
        if (strpos($chars_space, $chr) !== false) {
            $this->skipChars($chars_space);
        }

        // Value
        if ($chr === "'" || $chr === '"') {
            $quote = $chr;
            $this->movePos();
        } else {
            $quote = false;
        }
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
        };
        unset($node);
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
        if ($this->find("head")->length AND !$this->find("head base")->length) {
			$this->find("head",0)->prepend("<base>");
			$this->find("head base")->attr("href",str_replace("//","/",$_ENV["base"]."/"));
		}
        return $this;
    }

    public function wbGetFormLocale($ini=null) {
        $locale=null;
        if ($this->find("[type='text/locale']")->length OR $ini!==null) {
            $obj=$this->find("[type='text/locale']");
            if ( $ini!==null AND is_file($ini) ) {
                $loc=parse_ini_string(file_get_contents($ini),true);
            } else {
                if ($obj->is("[data-wb-role=include]") AND $ini!==null) {
                    $obj->tagInclude();
                    $obj->html("\n\r".$obj->html());
                }
                $loc=parse_ini_string($obj->html(),true);
            }
            if (count($loc)) {
                $locale=array();
                if (isset($loc["_global"]) AND $loc["_global"]==false) {
                    $global=false;
                }
                else {
                    $global=true;
                }
                foreach($loc as $lang => $variables) {
                    if (!isset($locale[$lang])) {
                        $locale[$lang]=array();
                    }
                    if (!isset($_ENV["locale"][$lang])) {
                        $_ENV["locale"][$lang]=array();
                    }
                    foreach($variables as $var => $val) {
                        $locale[$lang][$var]=$val;
                        if ($global==true) {
                            $_ENV["locale"][$lang][$var]=$val;
                        }
                    }
                }
            }
            if (is_object($obj)) $obj->remove();
        }
        return $locale;
    }

    public function wbSetFormLocale($ini=null) {
        $locale=$this->wbGetFormLocale($ini);
        if ($locale!==null AND count($locale)) {
            $this->wrap("div");
            $loc=wbFromString(wbSetValuesStr($this,$locale,2,"_LANG"));
            $this->html($loc);
            $this->unwrap("div");
        }
        return $locale;
    }


    public function wbSetData($Item=array(),$clear=false) {
        $this->wbSetFormLocale();
        $this->wbExcludeTags($Item);
        $this->wbSetAttributes($Item);
        $this->wbUserAllow();
        $exit=false;
        while($exit !== true) {
            $nodes=$this->find("*:not(.wb-done)");
            //$this->addClass("wb-done");
            foreach($nodes as $inc) {
                if (!$inc->parents("[type=text/template]")->length AND !$inc->parents("script")->length) {
                    //$inc->addClass("wb-done");
                    $inc->wbUserAllow();
                    $inc->wbWhere($Item);
                    $tag=$inc->wbCheckTag();
                    if ($tag) {
                        $inc->wbSetAttributes($Item);
                        if ($inc->has("[data-wb-json]")) {
                            if (strpos($inc->attr("data-wb-json"),"}}")) $inc->attr("data-wb-json",wbSetValuesStr($inc->attr("data-wb-json"),$Item));
                        }
                        if ($inc->is("[data-wb-tpl=true]")) $inc->addTemplate();
                        if ($tag>"") {$func="tag".$tag; $inc->$func($Item); $inc->addClass("wb-done");}
                        $inc->tagHideAttrs();
                        $inc->addClass("wb-done");
                    }

                }
            };
            $this->tagInclude_inc($Item);
            $this->wbSetValues($Item);
            $this->wbPlugins($Item);
            $this->wbIncludeTags($Item);
            $this->wbTargeter($Item);
            $this->tagHideAttrs();
            $this->find(".wb-value")->removeClass("wb-value");
			$this->wbAppends($Item);
			$this->addClass("wb-done");
            $check=$this->find("[data-wb-role]:not(.wb-done),[role]:not(.wb-done)");
            if ($check->length) {
                $exit=true;
                foreach($check as $inc) {
                    if ((!$inc->parents("[type=text/template]")->length
                            OR !$inc->parents("script")->length)
                            AND $inc->wbCheckTag() ) {
                        $exit=false;
                    }
                }
            } else {
                $exit=true;
            }
        }
        if ($clear) {
			$this->wbExcludeTags();
			$this->html(wbClearValues($this->html()));
			$this->wbIncludeTags();
		}
        return $this;
    }

    public function wbWhere($Item) {
        $where=$this->attr("data-wb-where");
        if ($where=="") $where=$this->attr("where");
        if ($where=="") return;
		$where=wbSetValuesStr($where,$Item);
		$where=wbClearValues($where);
        if ($this->is("meta")) $this->addClass("wb-done");
        if ($this->hasRole("foreach")) {
            return;
        }
        elseif (!wbWhereItem($Item,$where)) {
            $this->remove();
        }
        if ($this->attr("data-wb-hide")!=="false") $this->removeAttr("data-wb-where");
    }


    public function wbPlugins() {
        $script=$this->find("[data-wb-src]");
        foreach($script as $sc) {
            switch( $sc->attr("data-wb-src") ) {
            case "jquery" :
                $sc->after('<script src="/engine/js/jquery.min.js" ></script>');
                $sc->remove();
                break;
            case "bootstrap3" :
                $sc->before('<link href="/engine/js/bootstrap3/bootstrap.min.css" rel="stylesheet">');
                $sc->after('<script src="/engine/js/bootstrap3/bootstrap.min.js" ></script>');
                $sc->remove();
                break;
            case "bootstrap3css" :
                $sc->before('<link href="/engine/js/bootstrap3/bootstrap.min.css" rel="stylesheet">');
                $sc->remove();
                break;
            case "bootstrap3js" :
                $sc->after('<script src="/engine/js/bootstrap3/bootstrap.min.js" ></script>');
                $sc->remove();
                break;
            case "bootstrap4" :
                $sc->before('<link href="/engine/js/bootstrap/bootstrap.min.css" rel="stylesheet">');
                $sc->after('<script src="/engine/js/bootstrap/bootstrap.min.js" ></script>');
                $sc->remove();
                break;
            case "bootstrap" :
                $sc->before('<link href="/engine/js/bootstrap/bootstrap.min.css" rel="stylesheet">');
                $sc->after('<script src="/engine/js/bootstrap/bootstrap.min.js" ></script>');
                $sc->remove();
                break;
            case "bootstrap4css" :
                $sc->before('<link href="/engine/js/bootstrap/bootstrap.min.css" rel="stylesheet">');
                $sc->remove();
                break;
            case "bootstrap4js" :
                $sc->after('<script src="/engine/js/bootstrap/bootstrap.min.js" ></script>');
                $sc->remove();
                break;
            case "datepicker" :
                $lang=$_ENV["settings"]["js_locale"];
                if ($sc->attr("data-wb-lang")>"") {
                    $lang=$sc->attr("data-wb-lang");
                }
                $sc->before('<link href="/engine/js/datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">');
                if ($lang>"") $sc->after("<script src='/engine/js/datetimepicker/locales/bootstrap-datetimepicker.{$lang}.js' type='text/javascript' charset='UTF-8'></script>");
                $sc->after('<script src="/engine/js/datetimepicker/bootstrap-datetimepicker.min.js" ></script>');
                $sc->remove();
                break;
            case "plugins" :
                $sc->before('<link href="/engine/js/plugins/plugins.css" rel="stylesheet">');
                $sc->after('<script src="/engine/js/plugins/plugins.js"></script>');
                $lang=$_ENV["settings"]["js_locale"];
                if ($sc->attr("data-wb-lang")>"") {
                    $lang=$sc->attr("data-wb-lang");
                }
                $sc->before('<link href="/engine/js/datetimepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">');
                if ($lang>"") $sc->after("<script src='/engine/js/datetimepicker/locales/bootstrap-datetimepicker.{$lang}.js' type='text/javascript' charset='UTF-8'></script>");
                $sc->after('<script src="/engine/js/datetimepicker/bootstrap-datetimepicker.min.js" ></script>');
                $sc->remove();
                break;
            case "ckeditor" :
                //$sc->before('<link href="/engine/js/ckeditor/style.css" rel="stylesheet">');
                $sc->after('<script src="/engine/js/ckeditor/adapters/jquery.js"></script>');
                $sc->after('<script src="/engine/js/ckeditor/bootstrap-ckeditor-fix.js"></script>');
                $sc->after('<script src="/engine/js/ckeditor/ckeditor.js"></script>');
                $sc->remove();
                break;
            case "source" :
                $sc->after('<script language="javascript" src="/engine/js/ace/ace.js"></script>');
                $sc->remove();
                break;
            case "uploader" :
                $sc->after(wb_file_get_contents(__DIR__ ."/js/uploader/uploader.php"));
                $sc->remove();
                break;

            case "imgviewer" :
                $sc->after(wb_file_get_contents(__DIR__ ."/js/fancybox/fancybox.php"));
                $sc->remove();
                break;

            case "font" :
                switch ($name) {
                case "font-awesome":
                    $sc->after('<link href="/engine/lib/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">');
                    break;
                case "open-sans":
                    $sc->after('<link href="/engine/lib/fonts/open-sans/stylesheet.css" rel="stylesheet">');
                    break;
                case "simple-line-icons":
                    $sc->after('<link href="/engine/lib/fonts/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">');
                    break;
                case "weather-icons":
                    $sc->after('<link href="/engine/lib/fonts/weather-icons/css/weather-icons.min.css" rel="stylesheet">');
                    $sc->after('<link href="/engine/lib/fonts/weather-icons/css/weather-icons-wind.min.css" rel="stylesheet">');
                    break;
                }
                $sc->remove();
                break;

            case "engine" :
                $sc->after('<script src="/engine/js/wbengine.js" ></script>');
                //$sc->before('<link href="/engine/tpl/css/custom.css" rel="stylesheet">');
                $sc->remove();
                break;

            }
        }
    }

    public function wbCheckAllow() {
        foreach($this->find(wbControls("allow")) as $inc) {
            $inc->wbUserAllow();
        }
    }

    public function wbUserAllow() {
        if (isset($_SESSION["user_role"])) {
            $role=$_SESSION["user_role"];
        } else {
            $role="noname";
        }
        $allow=trim($this->attr("data-wb-allow"));
        if ($allow>"") {
            $allow=str_replace(" ",",",trim($allow));
            $allow=explode(",",$allow);
            $allow = array_map('trim', $allow);
        }
        $disallow=trim($this->attr("data-wb-disallow"));
        if ($disallow>"") {
            $disallow=str_replace(" ",",",trim($disallow));
            $disallow=explode(",",$disallow);
            $disallow = array_map('trim', $disallow);
        }
        $disabled=trim($this->attr("data-wb-disabled"));
        if ($disabled>"") {
            $disabled=str_replace(" ",",",trim($disabled));
            $disabled=explode(",",$disabled);
            $disabled = array_map('trim', $disabled);
        }
        $enabled=trim($this->attr("data-wb-enabled"));
        if ($enabled>"") {
            $enabled=str_replace(" ",",",trim($enabled));
            $enabled=explode(",",$enabled);
            $enabled = array_map('trim', $enabled);
        }
        $readonly=trim($this->attr("data-wb-readonly"));
        if ($readonly>"") {
            $readonly=str_replace(" ",",",trim($readonly));
            $readonly=explode(",",$readonly);
            $readonly = array_map('trim', $readonly);
        }
        $writable=trim($this->attr("data-wb-writable"));
        if ($writable>"") {
            $writable=str_replace(" ",",",trim($writable));
            $writable=explode(",",$writable);
            $writable = array_map('trim', $writable);
        }
        if ($disallow>""	&&  in_array($role,$disallow)) {
            $this->remove();
        }
        if ($allow>"" 		&& !in_array($role,$allow)) {
            $this->remove();
        }
        if ($disabled>"" 	&&  in_array($role,$disabled)) {
            $this->attr("disabled",true);
        }
        if ($enabled>"" 	&& !in_array($role,$enabled)) {
            $this->attr("disabled",true);
        }
        if ($readonly>"" 	&&  in_array($role,$readonly)) {
            $this->attr("readonly",true);
        }
        if ($writable>"" 	&& !in_array($role,$writable)) {
            $this->attr("readonly",true);
        }
    }


    public function wbCheckTag() {
        $res=FALSE;
        $tags=wbControls("tags");
        foreach($tags as $tag) {
            if ($this->hasRole($tag)) {
                $res=$tag;
                return $res;
            }
        };
        unset($tag);
        return $res;
    }


    function wbTargeter($Item=array()) {
        $controls=wbControls("target");
        $tar=$this->find($controls);
        $attr=(explode(",",str_replace(array("[","]"," "),array("","",""),$controls)));
        foreach($tar as $inc) {
            if (!$inc->is("[data-wb-ajax]")) {
                if (!$inc->is("[data-wb-selector]")) {
                    $res=false;
                    foreach ($attr as $key => $attribute) {
                        $$attribute=$inc->attr($attribute);
                        $com=str_replace("data-wb-","",$attribute);
                        if ($$attribute>"" ) {
                            if ($this->find($$attribute)->length) {
                                $inc->removeAttr($attribute);
                                if ($com=="replace") $com="replaceWith";
                                if ($com>"") {
                                    $this->find($$attribute)->$com($inc);
                                    $res=true;
                                }
                            }
                        }
                    };
                    if ($res) $inc->addClass("wb-done");
                } else if ($inc->is("[data-wb-selector]")) {
                    $selector=$inc->attr("data-wb-selector");
                    foreach ($attr as $key => $attribute) {
                        $$attribute=$inc->attr($attribute);
                        $com=str_replace("data-wb-","",$attribute);
                        if ($com=="replace") $com="replaceWith";
                        if ($$attribute>""  AND $com>"" AND $com!=="selector") {

				if ($com=="prependto" OR $com=="appendto" OR $com=="replaceWith") {
						if ($com=="replaceWith" AND $this->find($$attribute)->length AND $this->find($selector)->length) {
							$this->find($$attribute)->$com($this->find($selector));
						} else {
							if ($this->find($$attribute)->length AND $this->find($selector)->length ) {
								$this->find($selector)->$com($$attribute);
								$inc->remove();
							}
						}
				} else {
				    $res=$this->find($selector);
				    if ($res->length) {
					foreach($res as $s) {
					    if ($com == "attr") {
						$s->$com($$attribute,$inc->attr("value"));
					    } else {
						$s->$com($$attribute);
					    }

					}
					$inc->remove();
				    }
				}
                        }
                    }
                }
            }
        };
        // clear double js loading
        $srcjs = $this->find("script[src$=.js]");
        $loaded = [];
        foreach ($srcjs as $inc) {
            if (in_array($inc->attr("src"),$loaded)) {
                $inc->remove();
            } else {
                $loaded[] = $inc->attr("src");
            }
        }


        $this->find("[data-wb-remove]")->remove();
        $this->find("[data-wb-clear]")->html("");
        $this->find("[data-wb-clear]")->removeAttr("data-wb-clear");
    }


    public function wbExcludeTags($Item=array()) {
        if (!isset($_ENV["ta_save"])) $_ENV["ta_save"]=array();
        $list=$this->find("textarea,[type=text/template],.wb-value,pre,.nowb,[data-role=module],[data-wb-role=module]");
        foreach ($list as $ta) {
            $id=wbNewId();
            if (!$ta->is(".wb-attrs")) $ta->wbSetAttributes($Item);
            $ta->attr("taid",$id);
            $ta->replaceWith(strtr($ta->outerHtml(),array("{{"=>"#~#~","}}"=>"~#~#")));
        };
    }
    function wbIncludeTags($Item=array()) {
        $list=$this->find("[taid]");
        foreach ($list as $ta) {
            $ta->removeAttr("taid");
            $ta->replaceWith(strtr($ta->outerHtml(),array("#~#~"=>"{{","~#~#"=>"}}")));
        };
    }

    public function wbClearClass() {
        $clear=$this->find(".wb-attrs, .wb-value, .wb-done, .wb-plugins");
        foreach($clear as $c) {
            $c->removeClass("wb-attrs");
            $c->removeClass("wb-value");
            $c->removeClass("wb-done");
            $c->removeClass("wb-plugin");
        }
        $this->wbControlClass();
        $this->wbControlClass(); // два прохода!
    }

    public function wbControlClass() {

        $check = "empty-control";
        $remove = "empty-remove";

        $ec = $this->find("[class*='{$check}']");
        foreach($ec as $c) {
            if (trim(strip_tags_smart($c->html())) == "") {
                $ats = explode(" ",$c->attr("class"));
                foreach ($ats as $at => $v) {
                    if (strpos(" ".$v,$check)) {
                        $v = explode("-",$v);
                        if (!isset($v[2])) {
                            $c->parents(".{$remove}")->remove();
                        } else {
                            $this->find(".{$remove}-{$v[2]}")->remove();
                        }
                        $c->remove();
                    }
                }
            }
        }
    }


    public function wbSetValues($Item=array(),$obj=TRUE) {
        if ( !( (array)$Item === $Item ) ) $Item=array($Item);
        $this->wbSetAttributes($Item);
        if (isset($Item["form"])) {
            $Item=wbTrigger("form",__FUNCTION__,"BeforeSetValues",func_get_args(),$Item);
        }
        $list=$this->find("input,select,textarea");
        foreach($list as $inp) {
            if (!$inp->hasClass("wb-value") AND !$inp->parents("script[type='text/template']")->length) {
                $inp->wbSetAttributes($Item);
                $from=$inp->attr("data-wb-from");
                $name=$inp->attr("name");
                $def=$inp->attr("value");
                if ($inp->is("textarea")) {
                    if (isset($Item[$name])) {
                        $inp->html(htmlspecialchars($Item[$name]));
                    }
                    else {
                        $inp->html(htmlspecialchars($def));
                    }
                } else {
                    if (substr($name,-2)=="[]") {
                        $name=substr($name,0,-2);
                    }
                    if (substr($def,0,3)=="{{_") {
                        $def="";
                    }
                    if (isset($Item[$name]) AND $inp->attr("value")=="") {
                        if (is_array($Item[$name])) {
                            $Item[$name]=wbJsonEncode($Item[$name]);
                        }
                        $value=$Item[$name];
                    } else {
                        $value=$def;
                    }
                    if (!$inp->hasAttr("value") OR ($inp->hasAttr("value") AND $value!=="")) {
                        $inp->attr("value",$value);
                    }
                    else {
                        if (!$inp->hasAttr("value") AND isset($Item[$name])) {
				            $tmpname=$Item[$name];
                            if ((array)$tmpname === $tmpname) {
                                $Item[$name]=wbJsonEncode($tmpname);
                            }
                            $inp->attr("value",$Item[$name]);
                        }
                    }

                    if ($inp->attr("type")=="checkbox") {
                        if ($inp->attr("value")=="on" OR $inp->attr("value")=="1") {
                            $inp->checked="checked";
                        }
                    }

                    if ($inp->is("select") AND $inp->attr("value")>"") {
                        if ($inp->is("[multiple]")) {
                            $value=json_decode($Item[$name],true);
                            if ((array)$value === $value) {
                                foreach($value as $val) {
                                    if ($val>"") $inp->find("option[value=".$val."]")->selected="selected";
                                }
                            }
				            $value=$value[0];
                    } else {
                        $value=$inp->attr("value");
                        $inp->find("option[value=".$value."]")->selected="selected";
                    }
                    }
                };
                $inp->wbSetMultiValue($Item);
                $inp->addClass("wb-value");
            }
        }
        unset($list);
        $this->wbExcludeTags($Item);
        if (strpos($this->outerHtml(),"}}")) $this->wbSetValuesStr($Item);
        $this->wbIncludeTags($Item);
        if ($obj==FALSE) return $this->outerHtml();
    }

    public function wbSetValuesStr($Item=array()) {
        $this->html(wbSetValuesStr($this->html(),$Item));
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
            if ($type==$t) {
                $oconv=$val;
            }
        }
        if ($this->attr("date-wb-oconv")>"") {
            $oconv=$this->attr("date-wb-oconv");
        }
        if ($oconv>"" && $this->attr("value")>"") $this->attr("value",date($oconv,strtotime($this->attr("value"))));
    }


    public function wbSetMultiValue($Item) {
        $name=$this->attr("name");
        if (!strpos($name,"]")) return;
        preg_match_all('/(.*)\[.*\]/',$name,$fld);
        if (isset($fld[1][0])) {
            $fldname=$fld[1][0];
            if (isset($Item[$fldname])) {
                preg_match_all('/\[(.*)\]/',$name,$sub);
                $sub=$sub[1][0];
				if ($sub=="") {$value="";} else {$value=$Item[$fldname][$sub];}
                if ($this->attr("type")=="checkbox") {
                    if ($value=="on" OR $this->attr("value")=="1") {
                        $this->attr("checked","checked");
                    }
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
            };
            unset($sub);
        }
    }

	public function wbAppends($Item=array()) {

        if ($_ENV["route"]["controller"]=="engine") return;
	    if ($this->find("head")->length) {
		// Вставки в тэг HEAD
		if (isset($Item["head_add"]) AND isset($Item["head_add_active"]) AND $Item["head_add_active"]=="on")  {$this->find("head")->append($Item["head_add"]);}
		if (!isset($Item["head_noadd_glob"]) OR $Item["head_noadd_glob"]!=="on") {
		    if (isset($_ENV["settings"]["head_add"]) AND isset($_ENV["settings"]["head_add_active"]) AND $_ENV["settings"]["head_add_active"]=="on")  {
			    $this->find("head")->append($_ENV["settings"]["head_add"]);
			    unset($_ENV["settings"]["head_add"]);
		    }
		}
		if (isset($Item["meta_description"]) AND $Item["meta_description"]>"") {
		    $this->find("head meta[name=description]")->remove();
		    $this->find("head")->prepend("<meta name='description' content='{$Item["meta_description"]}'>");
		}
		if (isset($Item["meta_keywords"]) AND $Item["meta_keywords"]>"") {
		    $this->find("head meta[name=keywords]")->remove();
		    $this->find("head")->prepend("<meta name='keywords' content='{$Item["meta_keywords"]}'>");
		}
	    }
	    if ($this->find("body")->length) {
		// Вставки в тэг BODY
		if (isset($Item["body_add"]) AND isset($Item["body_add_active"]) AND $Item["body_add_active"]=="on")  {$this->append($Item["body_add"]);}
		if (!isset($Item["body_noadd_glob"]) OR $Item["body_noadd_glob"]!=="on") {
		    if (isset($_ENV["settings"]["body_add"]) AND isset($_ENV["settings"]["body_add_active"]) AND $_ENV["settings"]["body_add_active"]=="on") {
			    $this->append($_ENV["settings"]["body_add"]);
			    unset($_ENV["settings"]["body_add"]);
		    }
		}
	    }
	}

    public function wbTableProcessor() {
        $data=array();
        $grps=array();
        $total=array();
        $grand=array();
        $tmp=$this->attr("data-wb-group");
        if ($tmp>"") {
            $groups=wbAttrToArray($tmp);
        }
        else {
            $groups=array(null);
        }
        $tmp=$this->attr("data-wb-total");
        if ($tmp>"") {
            $totals=wbAttrToArray($tmp);
            $total=array();
        }
        if ($this->is("[data-wb-suppress]")) {
            $sup=true;
        }
        else {
            $sup=false;
        }
        $lines=$this->find("tr");
        $index=0;
        foreach ($lines as $tr) {
            unset($grp_id,$grpidx);
            $fields=$tr->find("td[data-wb-fld]:not([data-wb-eval])");
            $Item=array();
            foreach($fields as $field) {
                $Item[$field->attr("data-wb-fld")]=$field->text();
            }
            $fields=$tr->find("[data-wb-eval]");
            foreach($fields as $field) {
                $evalStr=contentSetValuesStr($field,$Item);
                eval ("\$tmp = ".$field->text().";");
                $field->text($tmp);
            }
            foreach($groups as $group) {
                $grp_text=$tr->find("[data-wb-fld={$group}]")->text();
                if (!isset($grp_id)) {
                    $grp_id=$grp_text;
                }
                else {
                    $grp_id.="|".$grp_text;
                }
                if (!isset($grpidx)) {
                    $grpidx=$group;
                }
                else {
                    $grpidx.="|".$group;
                }
                if (!isset($grps[$grp_id])) {
                    $grps[$grp_id]=array("data"=>array(),"total"=>array());
                }
                $grps[$grp_id]["grpidx"]=$grpidx;
                $grps[$grp_id]["data"][]=$index;
                if (isset($totals)) {
                    foreach($totals as $totfld) {
                        $totval=$tr->find("[data-wb-fld={$totfld}]")->text()*1;
                        if (!isset($grps[$grp_id]["total"][$totfld])) {
                            $grps[$grp_id]["total"][$totfld]=0;
                        }
                        $grps[$grp_id]["total"][$totfld]+=$totval;
                        if (!isset($grand[$totfld])) {
                            $grand[$totfld]=0;
                        }
                        if ($group==$groups[0]) $grand[$totfld]+=$totval;
                    }
                }
            }
            $index++;
        }
        ksort($grps);
        $grps=array_reverse($grps,true);
        $tbody=wbFromString("<tbody type='result'></tbody>");
        $ready=array();
        foreach($grps as $grpid => $grp) {
            $inner="";
            $count=count($grp["data"])-1;
            foreach($grp["data"] as $key => $idx) {
                if (!in_array($idx,$ready)) {
                    $tpl=$this->find("tr:eq({$idx})")->outerHtml();
                    if ($sup==false) $tbody->append($tpl);
                }
                if ($key==$count AND count($grp["total"])>0) {
                    // выводим тоталы группы
                    $trtot=wbFromString("<tr>".$this->find("tr:eq({$idx})")->html()."</tr>");
                    $totchk=array();
                    foreach($grp["total"] as $fld => $total) {
                        $trtot->find("td[data-wb-fld={$fld}]")->html($total);
                        $totchk[]=$fld;
                    }
                    $trtot->find("tr")->attr("data-wb-group",$grp["grpidx"]);
                    $trtot->find("tr")->attr("data-wb-group-value",$grpid);
                    $trtot->find("tr")->addClass("data-wb-total success");
                    $grpchk=explode("|",$grp["grpidx"]);
                    $tmp=$trtot->find("td:not([data-wb-fld])");
                    foreach($tmp as $temp) {
                        $temp->html("");
                    }
                    $tdflds=$trtot->find("td[data-wb-fld]");
                    foreach($tdflds as $tdfld) {
                        $data_fld=$tdfld->attr("data-wb-fld");
                        if (!in_array($data_fld,$grpchk) && !in_array($data_fld,$totchk)) {
                            $tdfld->html("");
                        }
                        if (in_array($data_fld,$grpchk)) {
                            $tdfld->addClass("data-wb-group");
                        }
                        if (in_array($data_fld,$totchk)) {
                            $tdfld->addClass("data-wb-total");
                        }
                    }
                    $inner.=$trtot->outerHtml();

                }
                $ready[]=$idx;
            }
            $tbody->append($inner);
        }
        // выводим общий итог
        if (isset($grp["total"]) && count($grp["total"])>0) {
            $grtot=wbFromString("<tr>".$this->find("tr:eq({$idx})")->html()."</tr>");
            $grtot->find("tr")->addClass("data-wb-grand-total info");
            $tmp=$grtot->find("td");
            foreach($tmp as $temp) {
                $temp->html("");
            }
            $grflds=$grtot->find("td[data-wb-fld]");
            foreach($grflds as $grfld) {
                $data_fld=$grfld->attr("data-wb-fld");
                if (in_array($data_fld,$totchk)) {
                    $grfld->html($grand[$data_fld]);
                    $grfld->addClass("data-wb-total");
                }
            }
            $tbody->append($grtot);
        }
        $this->html($tbody->innerHtml());
    }


    public function tagUploader($Item) {
        if ($this->attr("id")==NULL) {
            $uid=wbNewId();
            $this->attr("id",$uid);
        }
        else {
            $uid=$this->attr("id");
        }
        if ($this->hasAttr("name")) {
            $fldname=$this->attr("name");
        }
        else {
            $fldname="images";
        }
        if (!$this->hasAttr("data-wb-form") AND isset($_ENV["route"]["form"])) {
            $this->attr("data-wb-form",$_ENV["route"]["form"]);
        }
        if (!$this->hasAttr("data-wb-item") AND isset($_ENV["route"]["item"])) {
            $this->attr("data-wb-item",$_ENV["route"]["item"]);
        }
        if ($this->hasAttr("multiple")) {
            $out=wbGetForm("common","upl_multiple");
        }
        else {
            $out=wbGetForm("common","upl_single");
        }
        $out->find(".wb-uploader")->attr("id",$uid);

        $attrs=$this->attributes();
        foreach ($attrs as $attr) {
            $tmp=$attr->name;
            if (strpos($tmp,"ata-wb-")) {
                $tmp=str_replace("data-wb-","",$tmp);
                $$tmp=$attr."";
            }
        };
        unset($attrs);
		if (isset($ext)) $out->find(".wb-uploader")->children("input[name]")->attr("data-wb-ext",$ext);
        if (isset($form) AND isset($item) AND !isset($path)) {
            $Item["path"]="/uploads/{$form}/{$item}";
            $out->find(".wb-uploader")->attr("data-wb-path",$Item["path"]);
        } else if (!isset($path)) {
            if (isset($Item['_form']) AND isset($Item['_item']) AND isset($Item['_id'])) {
                // this branch work in wbFieldBuild
                $data["path"]="/uploads/{$Item['_form']}/{$Item['_item']}/{$Item['_id']}/";
                $out->find(".wb-uploader")->attr("data-wb-path",$data["path"]);
                $out->find(".wb-uploader")->attr("data-wb-form",$form);
                $out->find(".wb-uploader")->attr("data-wb-item",$item);
            } else {
                $Item["path"]=wbFormUploadPath();
                if (!isset($form)) {
                    $form=$_ENV["route"]["form"];
                }
                if (!isset($item)) {
                    $item=$_ENV["route"]["item"];
                }
                $out->find(".wb-uploader")->attr("data-wb-form",$form);
                $out->find(".wb-uploader")->attr("data-wb-item",$item);
                $out->find(".wb-uploader")->attr("data-wb-path",$Item["path"]);
            }
        } else {
            $out->find(".wb-uploader")->attr("data-wb-path",$path);
            $Item["path"]=$path;
        }

        if (!isset($width) AND $this->attr("width")!==NULL) {
            $width=$this->attr("width");
        }
        else if (!isset($width)) {
            $width=$_ENV['thumb_width'];
        }
        if (!isset($height) AND $this->attr("height")!==NULL) {
            $height=$this->attr("height");
        }
        else if (!isset($height)) {
            $height=$_ENV['thumb_height'];
        }
        if (!isset($size) AND $this->attr("size")!==NULL) {
            $size=$this->attr("size");
        }
        else if (!isset($size)) {
            $size="cover";
        }

        $out->find(".wb-uploader [data-wb-role=thumbnail]")->attr($size,"true");
        $out->find(".wb-uploader [data-wb-role=thumbnail]")->attr("size","{$width};{$height}");
        $out->find(".wb-uploader")->children("input[name]")->attr("name",$fldname);
        $out->find(".wb-uploader .gallery[data-wb-from]")->attr("data-wb-from",$fldname);
        $out->wbSetData($Item);
        $this->replaceWith($out);
    }


    public function getAttrVars() {
        return $tag->attr("vars");
    }

    public function wbSetAttributes($Item=array()) {
        $attributes=$this->attributes();
        if (is_object($attributes) AND !$this->hasClass("wb-attrs")) {
            foreach($attributes as $at) {
                $atname=$at->name;
                $atval=html_entity_decode($this->attr($atname));
                if (strpos($atname,"}}")) $atname=wbSetValuesStr($atname,$Item);
                if ($atval>"" && strpos($atval,"}}")) {
                    $fld=str_replace(array("{{","}}"),array("",""),$atval);
                    if (isset($Item[$fld]) AND $this->is(":input")) {
                        $atval=$Item[$fld];
                        if ((array)$atval === $atval) $atval=wbJsonEncode($atval);
                    } else {
                        $atval=wbSetValuesStr($atval,$Item);
                    }
                    $this->attr($atname,$atval);
                };
            };
            $this->addClass("wb-attrs");
        }
    }

    /* do not fork with qoutes in tree text
    public function wbSetAttributes($Item=array()) {
        $attributes=$this->attributes();
        if (is_object($attributes) AND !$this->hasClass("wb-attrs")) {
            foreach($attributes as $at) {
                $atname=$at->name;
                $atval=html_entity_decode($this->attr($atname));
                $atname=wbSetValuesStr($atname,$Item);
                $atval=wbSetValuesStr($atval,$Item);
                $this->attr($atname,$atval);
            };
            $this->addClass("wb-attrs");
        }
    }
    */

    public function tagHideAttrs() {
        if (($this->is("[data-wb-role]") AND !$this->hasClass("wb-done") AND !$this->is("[data-wb-done]")) ) return;
        $list=wbAttrToArray($this->attr("data-wb-hide"));
        if (in_array("wb",$list)) {
            $attrs=$this->attributes();
            foreach($attrs as $attr) {
                if (substr($attr->name,0,8)=="data-wb-") {
			if (!in_array($attr->name,$list)) {
				$list[]=$attr->name;
			}
                }
            }
        } else if (in_array("*",$list)) {
            $this->after($this->innerHtml());
            $this->remove();
            return;
        }
        foreach($list as $attr) {
		if ($this->hasAttr($attr)) {
			$this->removeAttr($attr);
		} else if ($this->hasAttr("data-wb-".$attr)) {
			$this->removeAttr("data-wb-".$attr);
		}
        }
	$this->removeAttr("data-wb-hide");
        if (!$this->is("[data-wb-role]") AND !$this->is("[role]") AND $this->is("[data-wb-done=true]")) {
            $this->removeAttr("data-wb-done");
        }
    }

    public function tagTree($Item=array()) {
        if (!is_array($Item)) {
            $Item=array($Item);
        }
        $srcVal=array();
        foreach($Item as $k => $v) {
            $srcVal["%{$k}"]=$v;
        };
        unset($v);
        $this->wbSetAttributes($Item);
        include("wbattributes.php");
        $name=$this->attr("name");
        if (isset($from)) $name=$from;
        if ($name=="" AND isset($item)) $name=$item;
        $type=$this->attr("type");
        if (isset($dict)) {
            $dictdata=wbItemRead("tree",$dict);
            if (!isset($Item["_{$name}__dict_"])) {
                $Item["_{$name}__dict_"]=$dictdata["_tree__dict_"];
            }
            if (!isset($Item[$name])) {
                $Item[$name]=$dictdata["tree"];
            }
            unset($dictdata);
        }

        if (($this->hasAttr("name") OR $this->is("input")) AND !$this->is("select") ) {
            $tree=wbGetForm("common","tree_ol");
            $this->append("<input type='hidden' name='{$name}'><input type='hidden' name='_{$name}__dict_' data-name='dict'>");
            if (isset($Item[$name]) && $Item[$name]!=="[]" && $Item[$name]!=="") {
                $tree->tagTreeData($Item[$name]);
            }
            else {
                $tree->find("ol")->append(wbGetForm("common","tree_row"));
            }
            $this->prepend($tree);
            $this->addClass("wb-tree dd");
            $this->wbSetData($Item);
        }
        elseif ($type=="select") {
            $this->tagTreeUl($Item,null,$srcVal);
        } else {
            if (isset($item) AND $item>"") {
                $tree=wbTreeRead($item);
                $Item[$name]=$tree["assoc"];
                $Item["_{$name}"]=$tree["dict"];
            }
            $this->tagTreeUl($Item,null,$srcVal);
        }
    }

    public function tagTreeData($data=array()) {
        $tree=wbGetForm("common","tree_ol");
        $tree->find("input[name]")->remove();
        $rowtpl=wbGetForm("common","tree_row");
        $name=$this->find("input[name]")->attr("name");
        if (!is_array($data)) {
            $data=wbItemToArray($data);
        }
        if (!is_array($data)) return;
        foreach($data as $item) {
            $tpl=$rowtpl->clone();
            $tpl->wbSetAttributes($item);
            $tpl->wbSetData($item);
            if (isset($item["children"]) AND $item["children"]!==false AND $item["children"]!==array()) {
                if (isset($item["open"]) AND $item["open"]==false) {
                    $tpl->find(".wb-tree-item")->addClass("dd-collapsed");
                }
                $tch=$tree->clone();
                $tch->tagTreeData($item["children"]);
                $tpl->find(".dd-item")->append($tch);
            }
            $this->children("ol")->append($tpl);
        }
    }

    public function tagTreeUl($Item=array(),$param=null,$srcVal=array()) {
        $limit=-1;
        $lvl=0;
        $level=-1;
        $idx=0;
        $tree=$Item;
        $branch=0;
        $parent=1;
        $parent_id="";
        $pardis=0;
        $children=1;
        $rand=false;
        $srcItem=$Item;
        if ($param==null) {
            include("wbattributes.php");
            $name=$this->attr("name");
            if (isset($from)) $name=$from;
            if ($name=="" AND isset($item)) $name=$item;
            if (isset($Item[$name])) {
				if (!is_array($Item[$name])) {
					$tree=json_decode($Item[$name],true);
				} else {
					$tree=$Item[$name];
				}
            } else {
                $tree=$Item["children"];
            }
            if (isset($call) AND $call > "" AND is_callable($call)) {
                $tree=@$call();
            }
            if (isset($rand) AND $rand=="true") {$rand=true;}
            $tag=$this->tag();
            if (!isset($limit) OR $limit=="false" OR $limit*1<0) {
                $limit=-1;
            }
            else {
                $limit=$limit*1;
            }
            if ($parent=="disabled") {
                $pardis=1;
                $parent=1;
            }
            else {
                $pardis=0;
            }
            if ($parent=="false" OR $parent=="0") {
                $parent=0;
            }
            elseif ($parent=="true") {
                $parent=1;
            }
            if ($children=="false" OR $children=="0") {
                $children=0;
            }
            elseif ($children=="true" OR $children=="1") {
                $children=1;
            }
        } else {
            foreach($param as $k =>$val) {
                $$k=$val;
            }
            $tree=$Item;
        }
        if (!isset($level)) $level="";
        $tpl=$this->html();
        $this->html("");
        if ($branch!==0) {
			if ($tree==NULL AND $branch>"") {
				$tree=wbTreeFindBranch($Item["children"],$branch);
			} else {
				$tree=wbTreeFindBranch($tree,$branch);
			}
        }
        if ($this->hasAttr("placeholder") AND $this->is("select")) {
            $this->prepend("<option value='' class='placeholder'>".$this->attr("placeholder")."</option>");
        }
        $idx=0;
        $srcItem=$srcVal;
        if ((array)$srcItem === $srcItem) {
            foreach($srcItem as $k => $v) {
                $srcVal["%{$k}"]=$v;
            };
        }
        //$tree=wbItemToArray($tree);
        if ((array)$tree === $tree) {
			if ($rand==true) shuffle($tree);
            foreach($tree as $i => $item) {
                $lvl++;
                $item=(array)$srcVal + (array)$item;
                $item["_pid"]=$parent_id;
                $item["_idx"]=$idx;
                $item["_ndx"]=$idx+1;
                $item["_lvl"]=$lvl-1;
                if ($parent_id>"") $item["%id"]=$parent_id;
                $line=wbFromString($tpl);
                $line->wbSetData($item);

                if ($parent==0 OR (isset($item["children"]) AND is_array($item["children"]) AND count($item["children"]) AND $children==1)) {
                    if ($pardis==1 AND ($limit!==$lvl-1)) {
                        $line->children()->attr("disabled",true);
                    }
                    $child=wbFromString($tpl);
                    if ($lvl>1) $parent=1;
                    $child->tagTreeUl($item["children"],array("name"=>$name,"tag"=>$tag,"lvl"=>$lvl,"idx"=>$idx,"level"=>$level,"parent_id"=>$item["id"],"pardis"=>$pardis,"parent"=>$parent,"children"=>$children,"limit"=>$limit),$srcVal);
                    if (($limit==-1 OR $lvl<=$limit)) {
                        if ($tag=="select") {
                            if ($parent!==1) {
                                $lvl--;
                            }
                            $child->children("option")->prepend(str_repeat("<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>",$lvl));
                            $child->wbSetValues($item);
                            if ($parent!==1) {
                                $line->html($child);
                            } else {
                                if ($children == 1) $line->append($child);
                            }
                        } else {
                            if ($parent!==1) {
                                $lvl--;
                            }
                            if ($parent!==1) {
                                $line->html($child);
                            } else {
                                //if ($children==1) $line->children("")->append("<{$tag}>".$child->outerHtml()."</{$tag}>");
                                if ($children==1) $line->children(":first")->append("<{$tag}>".$child->outerHtml()."</{$tag}>");
                                //if ($children==1) $line->children(":first-child")->append("<{$tag}>".$child->outerHtml()."</{$tag}>");
                            }
                        }

                    }
                }
                $idx++;
                $lvl--;

                if (isset($line)) $this->append($line);
            }
        }
    }

    public function tagImageLoader($Item) {
        if ($this->attr("load")!==1) {
            $form=$_GET["form"];
            $item=$Item["id"];
            if ($this->attr("data-form")>"") {
                $form=$this->attr("data-form");
            }
            if ($this->attr("data-item")>"") {
                $item=$this->attr("data-item");
            }
            $path=formPathGet($form,$item);
            $img=formGetForm("form","comImages");
            $ext=$this->attr("data-ext");
            $max=$this->attr("data-max");
            $name=$this->attr("data-name");
            if ($this->attr("data-prop")=="false") {
                $img->find(".form-group.prop")->remove();
            }
            $img->wbSetValues($Item);
            $this->attr("path",$path["uplitem"]);
            $this->html($img->outerHtml());
            $this->attr("load",1);
            $this->addClass("wb-done");
            if ($name>"") $this->find("[data-role=imagestore]")->attr("name",$name);
            if ($ext>"") $ext=implode(" ",wbAttrToArray($ext));
            $this->find("[data-role=imagestore]")->attr("data-ext",$ext);
            if ($max>"") $this->find("[data-role=imagestore]")->attr("data-max",$max);
        }
    }

    public function tagWhere($Item=array()) {
        include("wbattributes.php");
        if ($this->attr("data") > "") {
            $where=$this->attr("data");
        }
        $res=wbWhereItem($Item,$where);
        if ($res==0) {
            $this->remove();
        }
        else {
            $vars=$this->find("[data-wb-role=variable]");
            foreach($vars as $v => $var) {
                $var->tagVariable($Item);
            }
            $this->wbSetData($Item);
            $this->tagHideAttrs();
        }
    }


    public function tagPagination($size=10,$page=1,$pages=1,$cacheId,$count=0,$find="") {
        ini_set('max_execution_time', 900);
        ini_set('memory_limit', '1024M');
        $this->attr("data-wb-pages",$pages);
        $this->attr("data-wb-cache",$cacheId);
        $tplId=$this->attr("data-wb-tpl");
        $class="ajax-".$tplId;
        if (!isset($_SESSION["temp"])) {
            $_SESSION["temp"]=array();
        }
        $_SESSION["temp"][$class]=$_ENV;
        unset($_SESSION["temp"][$class]["DOM"],
              $_SESSION["temp"][$class]["cache"],
              $_SESSION["temp"][$class]["error"],
              $_SESSION["temp"][$class]["errors"]
             );
        if (is_object($this->parent("table")) && $this->parent("table")->find("thead th[data-wb-sort]")->length) {
            $this->parent("table")->find("thead")->attr("data-wb-size",$size);
            $this->parent("table")->find("thead")->attr("data-wb",$class);
        }
        if ($pages>"0" OR $this->attr("data-wb-sort")>"") {
            $find=urlencode($find);
            $pag=wbGetForm("common","pagination");
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
            $pag->wbSetData($pagination);
            $pag->find("[data-page={$page}]")->addClass("active");
            $pag->find("ul")->attr("data-wb-route",json_encode($_ENV["route"]));
            if ($pages<2) {
                $style=$pag->find("ul")->attr("style");
                $pag->find("ul")->attr("style",$style.";display:none;");
            }
            $this->after($pag->innerHtml());
        }
        $this->removeAttr("data-wb-pagination");
    }

    public function addTemplate($type="innerHtml") {
        include($_ENV["path_engine"]."/wbattributes.php");
        if (isset($tpl) AND $tpl > "") {
            $tplid=$tpl;
        }
        else {
            $tplid=wbNewId();
        }
        $this->attr("data-wb-tpl",$tplid);
        $that=$this->clone();
        $that->removeClass("wb-done");
        if ($type=="innerHtml") {
            $tpl=$that->innerHtml();
        }
        else {
            $tpl=$that->outerHtml();
        }
        $this->after("<script type='text/template' id='{$tplid}' style='display:none;'>\n{$tpl}\n</script>");
    }


    public function tagGallery($Item) {
        $vars=$this->attr("vars");
        $src=$this->attr("src");
        $id=$this->attr("id");
        if ($vars>"") {
            $Item=wbAttrAddData($vars,$Item);
        }
        //$inner=wbFromFile($_SESSION['engine_path']."/tpl/gallery.php");
        $inner=wbGetTpl("gallery.php");
        if (!$inner) $inner=wbGetForm("common","common_gallery");
        if ($src=="") {
            if (trim($this->html())>"<p>&nbsp;</p>") {
                $inner->find(".comGallery")->html($this->html());
            }
        } else {
            $file=$_SESSION["path_app"].$src;
            if (is_file($_SERVER["DOCUMENT_ROOT"].$file)) {
                $src=$file;
            }
            if (substr($src,0,7)!="{$_ENV["route"]["scheme"]}://") {
                if (substr($src,0,1)!="/") {
                    $src="/".$src;
                } $src=$_SERVER['DOCUMEN_ROOT'].$src;
            }
            $inner->find(".comGallery")->html(ki::fromFile($src));
        }
        $this->html($inner);
        if ($id>"") {
            $this->find("#comGallery")->attr("id",$id);
        }
        $this->wbSetData($Item);
        $this->find(".comGallery")->removeAttr("vars");
        $this->find(".comGallery")->removeAttr("from");
        $this->find(".comGallery img[visible!=1]")->parents(".thumbnail")->remove();
        if (count($this->find(".comGallery")->children())>0) {
            $this->after($this->html());
        }
        $this->remove();
    }

    public function contentAppends() {
        contentAppends($this);
    }

    public function hasRole($checkrole=null) {
        if ($checkrole!==null) {
            $tl=array();
            if ($this->hasAttr("data-wb-role")) {
                $tl=wbAttrToArray($this->attr("data-wb-role"));
            }
            elseif 	($this->hasAttr("role")) 		{
                $tl=wbAttrToArray($this->attr("role"));
            } elseif ($this->hasAttr("data-wb-".$checkrole)) {
		    return true;
	    }
            include("wbattributes.php");
            if ($role == $checkrole) {return true;}
            if (in_array($checkrole,$tl)) {
                return true;
            } else {
                return false;
            }
        } else {
            return $this->wbCheckTag();
        }
    }

    public function hasClass($class) {
        $res=false;
        $list=explode(" ",trim($this->class));
        foreach($list as $k => $val) {
            if ($val==$class) {
                $res=true;
            }
        };
        unset($val,$list);
        return $res;
    }

    public function removeClass($class) {
        $res=false;
        $classes=explode(" ",trim($this->class));
        foreach($classes as $k => $val) {
            if ($val==$class) {
                unset($classes[$k]);
            }
        };
        unset($val);
        $this->class=trim(implode(" ",$classes));
        if ($this->class=="") {
            $this->removeAttr("class");
        }
    }

    public function addClass($class) {
        $res=false;
        $list=explode(" ",trim($this->class));
        foreach($list as $k => $val) {
            if ($val==$class) {
                $res=true;
            }
        };
        unset($val,$list);
        if ($res==false) {
            $this->class=trim($this->class." ".$class);
        }
    }

//======================================================================//
//======================================================================//

    protected function updateOwnerDocument($document) {
        if (!$od = &$this->ownerDocument || !$document || $od->uniqId !== $document->uniqId) {
            $od = $document;
            foreach ($this->nodes as $node) {
                $node->updateOwnerDocument($document);
            };
            unset($node);
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
        $class=explode("_",$name);
        $class=$class[0];
        $tag=substr(strtolower($class),3);

        if (in_array($tag,array_keys($_ENV['tags']))) {
            $node = new $class($this);
            return $node->$name($arguments[0]);
        } else {
		echo $tag;
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
            };
            unset($node);
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

    public function outerHtml()	{
        return $this->html();
    }
    public function htmlOuter()	{
        return $this->outerHtml();
    }
    public function innerHtml()	{
        return $this->html();
    }

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
        };
        unset($n);
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
        };
        unset($n);
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

    public function hasAttr($name) {
	return $this->hasAttribute($name);
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
        };
        unset($n);

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
            };
            unset($t);
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
        };
        unset($node);
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
        };
        unset($child);
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
        echo str_repeat('    ', $level), $current;
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
            };
            unset($node);
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
            };
            unset($n);
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
            if (is_array($val)) {
                $val=$val[0];
            }
            if (is_array($val)) {
                $val=implode("",$val);
            }
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
                };
                unset($k);
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
                if ($rListByIds) {
                    $rListByIds += $list;
                }
                else {
                    $rListByIds = $list;
                }
            }
            if (!$res) {
                break;
            }
        };
        unset($selector);
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
        };
        unset($node);

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
                };
                unset($node);
            }
            // parent > child
            else if ($h === '>') {
                foreach ($listByIds as $node) {
                    foreach ($node->children as $child) {
                        if ($this->nodeMatchExpression($child, $e)) {
                            $list[$child->uniqId] = $child;
                        }
                    };
                    unset($child);
                };
                unset($node);
            }
            // prev + next
            else if ($h === '+') {
                foreach ($listByIds as $node) {
                    if (($next = $node->next) && $this->nodeMatchExpression($next, $e)) {
                        $list[$next->uniqId] = $next;
                    }
                };
                unset($node);
            }
            // prev ~ siblings
            else if ($h === '~') {
                foreach ($listByIds as $next) {
                    while (($next = $next->next) !== null) {
                        if ($this->nodeMatchExpression($next, $e)) {
                            $list[$next->uniqId] = $next;
                        }
                    }
                };
                unset($next);
            }

            // nothing found
            if (!$listByIds = $list) {
                break;
            }

            // Matched set filtration
            if ($e['s']) {
                $this->matchedSetFilter($listByIds, $e['s']);
            }
        };
        unset($e);
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
        };
        unset($f);
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
                            };
                            unset($_n);
                        }
                        // parent > child
                        else if ($h === '>') {
                            foreach ($nodes as $_n) {
                                if ((($parent = $_n->parent) && $parent->type === self::NODE)
                                        && $this->nodeMatchExpression($parent, $e)
                                   ) {
                                    $list[] = $parent;
                                }
                            };
                            unset($_n);
                        }
                        // prev + next
                        else if ($h === '+') {
                            foreach ($nodes as $_n) {
                                if (($prev = $_n->prev) && $this->nodeMatchExpression($prev, $e)) {
                                    $list[] = $prev;
                                }
                            };
                            unset($_n);
                        }
                        // prev ~ siblings
                        else if ($h === '~') {
                            foreach ($nodes as $prev) {
                                while (($prev = $prev->prev) !== null) {
                                    if ($this->nodeMatchExpression($prev, $e)) {
                                        $list[] = $prev;
                                    }
                                }
                            };
                            unset($prev);
                        }

                        if (!$nodes = $list) {
                            // nothing found
                            break;
                        }
                    };
                    unset($e);
                    if ($nodes) {
                        return true;
                    }
                }
            }
        };
        unset($selector);

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
            };
            unset($a);
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
                    };
                    unset($child);
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
            };
            unset($m);
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
        };
        unset($child);
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
        };
        unset($attr);
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
        };
        unset($attr);

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
            };
            unset($node);
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
        };
        unset($node);
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
            if ($val==$class) {
                unset($classes[$k]);
            }
        };
        unset($val);
        $this->class=trim(implode(" ",$classes));
        if ($this->class=="") {
            $this->removeAttr("class");
        }
    }

    public function addClass($class) {
        $res=false;
        $list=explode(" ",trim($this->class));
        foreach($list as $k => $val) {
            if ($val==$class) {
                $res=true;
            }
        };
        unset($val,$list);
        if ($res==false) {
            $this->class=trim($this->class." ".$class);
        }
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
            };
            unset($node);
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
        };
        unset($node);
        return $string;
    }

    public function htmlAll()	{
        $string = '';
        foreach ($this->list as $node) {
            $string .= $node->html();
        };
        unset($node);
        return $string;
    }

    public function outerHtmlAll()	{
        $string = '';
        foreach ($this->list as $node) {
            $string .= $node->outerHtml();
        };
        unset($node);
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
        };
        unset($node);
        return $this;
    }

    public function hasAttribute($name)	{
        /** @noinspection PhpUndefinedMethodInspection */
        return isset($this->list[0])
               && $this->list[0]->attributes
               && $this->list[0]->attributes->has($name);
    }

    public function hasAttr($name) {
	return $this->hasAttribute($name);
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
            };
            unset($node);
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
            };
            unset($node);
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
            };
            unset($node);
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
            };
            unset($node);
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
            };
            unset($node);
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
            };
            unset($node);
        }
        return $this;
    }

    public function appendTo($target)	{
        if ($target = $this->prepareTarget($target)) {
            $list = $this->resetState();
            foreach ($list as $node) {
                $res = $node->appendTo($target);
                $this->add($res);
            };
            unset($node);
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
            };
            unset($node);
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
            };
            unset($node);
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
            };
            unset($node);
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
            };
            unset($node);
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
            };
            unset($node);
        }
        return $this;
    }

    public function unwrap() {
        foreach ($this->list as $node) {
            $node->unwrap();
        };
        unset($node);
        return $this;
    }

    public function detach()	{
        foreach ($this->list as $node) {
            $node->detach();
        };
        unset($node);
        return $this;
    }

    public function remove()	{
        foreach ($this->list as $node) {
            $node->remove();
        };
        unset($node);
        $this->resetState();
        $this->state = null;
        return $this;
    }

	public function clear()	{
		foreach ($this->list as $node) {
		    $node->clearChildren();
		};
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
            };
            unset($n);
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
        };
        unset($node);
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
        };
        unset($node);
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
        };
        unset($node);
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
        };
        unset($node);
        return false;
    }

    public function children($selector = null)	{
        foreach ($this->saveState() as $node) {
            $node->children(null, $this);
        };
        unset($node);
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
        };
        unset($node);
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
        };
        unset($node);
        return $this;
    }

    public function nextAll($selector = null)	{
        if (is_string($selector)) {
            $selector = new kiSelector($selector);
        }
        foreach ($this->saveState() as $node) {
            $node->nextAll($selector, $this);
        };
        unset($node);
        return $this;
    }

    public function nextUntil($selector)	{
        if (is_string($selector)) {
            $selector = new kiSelector($selector);
        }
        foreach ($this->saveState() as $node) {
            $node->nextUntil($selector, $this);
        };
        unset($node);
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
        };
        unset($node);
        return $this;
    }

    public function prevAll($selector = null)	{
        if (is_string($selector)) {
            $selector = new kiSelector($selector);
        }
        foreach ($this->saveState() as $node) {
            $node->prevAll($selector, $this);
        };
        unset($node);
        return $this;
    }

    public function prevUntil($selector)	{
        if (is_string($selector)) {
            $selector = new kiSelector($selector);
        }
        foreach ($this->saveState() as $node) {
            $node->prevUntil($selector, $this);
        };
        unset($node);
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
        };
        unset($node);
        return $this;
    }

    public function parents($selector = null)	{
        if (is_string($selector)) {
            $selector = new kiSelector($selector);
        }
        foreach ($this->saveState() as $node) {
            $node->parents($selector, $this);
        };
        unset($node);
        return $this;
    }

    public function parentsUntil($selector)	{
        if (is_string($selector)) {
            $selector = new kiSelector($selector);
        }
        foreach ($this->saveState() as $node) {
            $node->parentsUntil($selector, $this);
        };
        unset($node);
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
        };
        unset($node);
        return $this;
    }

    public function dump($attributes = true, $text_nodes = true)	{
        if ($this->length) {
            foreach ($this->list as $node) {
                $node->dump($attributes, $text_nodes);
            };
            unset($node);
        } else {
            echo "\n";
        }
        echo 'NodesList dump: ', $this->length, "\n";
        PHP_SAPI === 'cli' && ob_get_level() > 0 && @ob_flush();
    }
}
?>
