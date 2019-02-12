<?php
/**
 * Creator.php
 *
 * A pure PHP SQL creator, which generates SQL from the output of PHPSQLParser.
 *
 * Copyright (c) 2012, André Rothe <arothe@phosco.info, phosco@gmx.de>
 * with contributions by Dan Vande More <bigdan@gmail.com>
 *
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     andgo/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
 * OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT
 * SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
 * TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR
 * BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 */

namespace PHPSQL;

class Phpif {

	public $created;

    public function __construct($parsed = false) {
        if ($parsed) {
            $this->create($parsed);
        }
    }

    public function create($parsed) {
        $k = key($parsed);
        switch ($k) {
	        case "UNION":
	        case "UNION ALL":
	            throw new \PHPSQL\Exception\UnsupportedFeature($k);
	            break;
	        case "SELECT":
	            $this->created = $this->processSelectStatement($parsed);
	            break;
	        default:
	            throw new \PHPSQL\Exception\UnsupportedFeature($k);
	            break;
        }
        return $this->created;
    }

    protected function processSelectStatement($parsed) {
        //$sql = $this->processSELECT($parsed['SELECT']) . " " . $this->processFROM($parsed['FROM']);
        $sql="";
        if (isset($parsed['WHERE'])) {
            $sql .= $this->processWHERE($parsed['WHERE']);
        }
        return $sql;
    }

    protected function processSELECT($parsed) {
        $sql = "";
        foreach ($parsed as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->processColRef($v);
            $sql .= $this->processSelectExpression($v);
            //$sql .= $this->processFunction($v);
            $sql .= $this->processConstant($v);
            if ($len == strlen($sql)) {
                throw new \PHPSQL\Exception\UnableToCreateSQL('SELECT', $k, $v, 'expr_type');
            }

            $sql .= ",";
        }
        $sql = substr($sql, 0, -1);
        return "SELECT " . $sql;
    }

    protected function processFROM($parsed) {
        $sql = "";
        if (!is_array($parsed["FROM"])) return;
        foreach ($parsed as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->processTable($v, $k);
            $sql .= $this->processTableExpression($v, $k);
            $sql .= $this->processSubquery($v, $k);

            if ($len == strlen($sql)) {
                throw new \PHPSQL\Exception\UnableToCreateSQL('FROM', $k, $v, 'expr_type');
            }

            $sql .= " ";
        }
        return "FROM " . substr($sql, 0, -1);
    }

    protected function processWHERE($parsed) {
        $sql = ""; $prev="";
        foreach ($parsed as $k => $v) {
		$oper = $this->processOperator($v);
		if ($oper == "LIKE") {$sql.=" wbWhereLike(".$prev; $oper=$prev=""; $flag = true;}
		if ($oper == "NOT_LIKE") {$sql.=" wbWhereNotLike(".$prev; $oper=$prev=""; $flag = true;}
		if ($flag!==true) {$sql.=" ".$prev; $prev="";}
            $prev .= $oper;
            if ($flag == "func" AND $this->processColRef($v)>"") {$prev.=", ".$this->processColRef($v).") ";$flag=false;} else {$prev .= $this->processColRef($v);}
            if ($flag == "func" AND $this->processConstant($v)>"") {$prev.=", ".$this->processConstant($v).") ";$flag=false;} else {$prev .= $this->processConstant($v);}
            $prev .= $this->processSubquery($v);
            $prev .= $this->processInList($v);
            //$sql .= $this->processFunction($v);
            $prev .= $this->processWhereExpression($v);
            $prev .= $this->processWhereBracketExpression($v);
             if ($flag==true) $flag="func";
        }
        $sql.=" ".$prev;
        $sql = str_replace("!== ==","!==",$sql);
        return $sql;
    }

    protected function processWhereExpression($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::EXPRESSION) {
            return "";
        }
        $sql = ""; $prev=""; $flag = false;
        foreach ($parsed['sub_tree'] as $k => $v) {
		$oper = $this->processOperator($v);
		if ($oper == "LIKE") {$sql.=" wbWhereLike(".$prev; $oper=$prev=""; $flag = true;}
		if ($oper == "NOT_LIKE") {$sql.=" wbWhereNotLike(".$prev; $oper=$prev=""; $flag = true;}
		if ($flag!==true) {$sql.=" ".$prev; $prev="";}
            if ($flag == "func" AND $this->processColRef($v)>"") {$prev.=", ".$this->processColRef($v).") ";$flag=false;} else {$prev .= $this->processColRef($v);}
            if ($flag == "func" AND $this->processConstant($v)>"") {$prev.=", ".$this->processConstant($v).") ";$flag=false;} else {$prev .= $this->processConstant($v);}
            $prev .= $oper;
            $prev .= $this->processInList($v);
            $prev .= $this->processFunction($v);
            $prev .= $this->processWhereExpression($v);
            $prev .= $this->processWhereBracketExpression($v);
             if ($flag==true) $flag="func";
        }
	$sql.=" ".$prev;
        return $sql;
    }

    protected function processWhereBracketExpression($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::BRACKET_EXPRESSION) {
            return "";
        }
        $sql = ""; $prev=""; $flag = false;
        foreach ($parsed['sub_tree'] as $k => $v) {
		$oper = $this->processOperator($v);
		if ($oper == "LIKE") {$sql.=" wbWhereLike(".$prev; $oper=$prev=""; $flag = true;}
		if ($oper == "NOT_LIKE") {$sql.=" wbWhereNotLike(".$prev; $oper=$prev=""; $flag = true;}
		if ($flag!==true) {$sql.=" ".$prev; $prev="";}

            if ($flag == "func" AND $this->processColRef($v)>"") {$prev.=", ".$this->processColRef($v).") ";$flag=false;} else {$prev .= $this->processColRef($v);}
            if ($flag == "func" AND $this->processConstant($v)>"") {$prev.=", ".$this->processConstant($v).") ";$flag=false;} else {$prev .= $this->processConstant($v);}
            $prev .= $oper;
            $prev .= $this->processInList($v);
            $prev .= $this->processFunction($v);
            $prev .= $this->processWhereExpression($v);
            $prev .= $this->processWhereBracketExpression($v);
            if ($flag==true) $flag="func";
        }
        $sql.=" ".$prev;

        $sql = "(" . $sql . ")";
        return $sql;
    }

    protected function processOrderByAlias($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::ALIAS) {
            return "";
        }
        return $parsed['base_expr'] . $this->processDirection($parsed['direction']);
    }

    protected function processLimitRowCount($key, $value) {
        if ($key != 'rowcount') {
            return "";
        }
        return $value;
    }

    protected function processLimitOffset($key, $value) {
        if ($key !== 'offset') {
            return "";
        }
        return $value;
    }

    protected function processFunction($parsed) {
        if (($parsed['expr_type'] !== \PHPSQL\Expression\Type::AGGREGATE_FUNCTION)
                && ($parsed['expr_type'] !== \PHPSQL\Expression\Type::SIMPLE_FUNCTION)) {
            return "";
        }

        if ($parsed['sub_tree'] === false) {
            return $parsed['base_expr'] . "()";
        }

        $sql = "";
        foreach ($parsed['sub_tree'] as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->processFunction($v);
            $sql .= $this->processConstant($v);
            $sql .= $this->processColRef($v);
            $sql .= $this->processReserved($v);

            if ($len == strlen($sql)) {
                throw new \PHPSQL\Exception\UnableToCreateSQL('function subtree', $k, $v, 'expr_type');
            }

            $sql .= ($this->isReserved($v) ? " " : ",");
        }
        return $parsed['base_expr'] . "(" . substr($sql, 0, -1) . ")";
    }

    protected function processSelectExpression($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::EXPRESSION) {
            return "";
        }
        $sql = $this->processSubTree($parsed, " ");
        $sql .= $this->processAlias($parsed['alias']);
        return $sql;
    }

    protected function processSelectBracketExpression($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::BRACKET_EXPRESSION) {
            return "";
        }
        $sql = $this->processSubTree($parsed, " ");
        $sql = "(" . $sql . ")";
        return $sql;
    }

    protected function processSubTree($parsed, $delim = " ") {
        if ($parsed['sub_tree'] === '') {
            return "";
        }
        $sql = "";
        foreach ($parsed['sub_tree'] as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->processFunction($v);
            $sql .= $this->processOperator($v);
            $sql .= $this->processConstant($v);
            $sql .= $this->processSubQuery($v);
            $sql .= $this->processSelectBracketExpression($v);

            if ($len == strlen($sql)) {
                throw new \PHPSQL\Exception\UnableToCreateSQL('expression subtree', $k, $v, 'expr_type');
            }

            $sql .= $delim;
        }
        return substr($sql, 0, -1);
    }

    protected function processRefClause($parsed) {
        if ($parsed === false) {
            return "";
        }

        $sql = "";
        foreach ($parsed as $k => $v) {
            $len = strlen($sql);
            $sql .= $this->processColRef($v);
            $sql .= $this->processOperator($v);
            $sql .= $this->processConstant($v);

            if ($len == strlen($sql)) {
                throw new \PHPSQL\Exception\UnableToCreateSQL('expression ref_clause', $k, $v, 'expr_type');
            }

            $sql .= " ";
        }
        return "(" . substr($sql, 0, -1) . ")";
    }

    protected function processAlias($parsed) {
        if ($parsed === false) {
            return "";
        }
        $sql = "";
        if ($parsed['as']) {
            $sql .= " as";
        }
        $sql .= " " . $parsed['name'];
        return $sql;
    }

    protected function processRefType($parsed) {
        if ($parsed === false) {
            return "";
        }
        if ($parsed === 'ON') {
            return " ON ";
        }

        if ($parsed === 'USING') {
            return " USING ";
        }
        // TODO: add more
        throw new \PHPSQL\Exception\UnsupportedFeature($parsed);
    }

    protected function processTable($parsed, $index) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::TABLE) {
            return "";
        }

        $sql = $parsed['table'];
        $sql .= $this->processAlias($parsed['alias']);

        if ($index !== 0) {
            $sql = $this->processJoin($parsed['join_type']) . " " . $sql;
            $sql .= $this->processRefType($parsed['ref_type']);
            $sql .= $this->processRefClause($parsed['ref_clause']);
        }
        return $sql;
    }

    protected function processSubQuery($parsed, $index = 0) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::SUBQUERY) {
            return "";
        }

        $sql = $this->processSelectStatement($parsed['sub_tree']);
        $sql = "(" . $sql . ")";

        if (isset($parsed['alias'])) {
            $sql .= $this->processAlias($parsed['alias']);
        }

        if ($index !== 0) {
            $sql = $this->processJoin($parsed['join_type']) . " " . $sql;
            $sql .= $this->processRefType($parsed['ref_type']);
            $sql .= $this->processRefClause($parsed['ref_clause']);
        }
        return $sql;
    }

    protected function processOperator($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::OPERATOR) {
            return "";
        }
        $oper=" ".strtoupper($parsed['base_expr'])." ";
        $oper = strtr($oper, array(
		' <> ' => ' !== ',
		' != ' => ' !== ',
		' # ' => ' !== ',
		' = ' => ' == '
	));
        return trim($oper);
    }

    protected function processColRef($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::COLREF) {
            return "";
        }
        $sql = '$item["'.$parsed['base_expr'].'"]';
        if (isset($parsed['alias'])) {
            $sql .= $this->processAlias($parsed['alias']);
        }
        if (isset($parsed['direction'])) {
            $sql .= $this->processDirection($parsed['direction']);
        }
        return $sql;
    }

    protected function processDirection($parsed) {
        $sql = ($parsed ? " " . $parsed : "");
        return $sql;
    }

    protected function processReserved($parsed) {
        if (!$this->isReserved($parsed)) {
            return "";
        }
        return $parsed['base_expr'];
    }

    protected function isReserved($parsed) {
        return ($parsed['expr_type'] === \PHPSQL\Expression\Type::RESERVED);
    }

    protected function processConstant($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::CONSTANT) {
            return "";
        }
        return $parsed['base_expr'];
    }

    protected function processInList($parsed) {
        if ($parsed['expr_type'] !== \PHPSQL\Expression\Type::IN_LIST) {
            return "";
        }
        $sql = $this->processSubTree($parsed, ",");
        return "(" . $sql . ")";
    }

}
