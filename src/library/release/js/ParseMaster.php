<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/

/**
 * Package: server\optimization\js
 * Class ParseMaster
 * Created By ankio.
 * Date : 2023/3/16
 * Time : 12:17
 * Description :
 */

namespace library\release\js;

class ParseMaster
{
    const EXPRESSION = 0;
    const REPLACEMENT = 1;

    // constants
    const LENGTH = 2;
    public $ignoreCase = false;
    public $escapeChar = '';

    // used to determine nesting levels
    private $GROUPS = '/\\(/'; //g
    private $SUB_REPLACE = '/\\$\\d/';
    private $INDEXED = '/^\\$\\d+$/';
    private $TRIM = '/([\'"])\\1\\.(.*)\\.\\1\\1$/';
    private $ESCAPE = '/\\\./'; //g
    private $QUOTE = '/\'/';
    private $DELETED = '/\\x01[^\\x01]*\\x01/'; //g
    private $_escaped = array();
    private $_patterns = array();
    private $buffer;

    // private

    public function add($expression, $replacement = '')
    {
        // count the number of sub-expressions
        //  - add one because each pattern is itself a sub-expression
        $length = 1 + preg_match_all($this->GROUPS, $this->_internalEscape((string)$expression), $out);

        // treat only strings $replacement
        if (is_string($replacement)) {
            // does the pattern deal with sub-expressions?
            if (preg_match($this->SUB_REPLACE, $replacement)) {
                // a simple lookup? (e.g. "$2")
                if (preg_match($this->INDEXED, $replacement)) {
                    // store the index (used for fast retrieval of matched strings)
                    $replacement = (int)(substr($replacement, 1)) - 1;
                } else { // a complicated lookup (e.g. "Hello $2 $1")
                    // build a function to do the lookup
                    $quote = preg_match($this->QUOTE, $this->_internalEscape($replacement))
                        ? '"' : "'";
                    $replacement = array(
                        'fn' => '_backReferences',
                        'data' => array(
                            'replacement' => $replacement,
                            'length' => $length,
                            'quote' => $quote
                        )
                    );
                }
            }
        }
        // pass the modified arguments
        if (!empty($expression)) {
            $this->_add($expression, $replacement, $length);
        } else {
            $this->_add('/^$/', $replacement, $length);
        }
    } // escaped characters

    private function _internalEscape($string)
    {
        return preg_replace($this->ESCAPE, '', $string);
    } // patterns stored by index

    // create and add a new pattern to the patterns collection

    private function _add()
    {
        $arguments = func_get_args();
        $this->_patterns[] = $arguments;
    }

    // this is the global replace function (it's quite complicated)

    public function exec($string)
    {
        // execute the global replacement
        $this->_escaped = array();

        // simulate the _patterns.toSTring of Dean
        $regexp = '/';
        foreach ($this->_patterns as $reg) {
            $regexp .= '(' . substr($reg[self::EXPRESSION], 1, -1) . ')|';
        }
        $regexp = substr($regexp, 0, -1) . '/';
        $regexp .= ($this->ignoreCase) ? 'i' : '';

        $string = $this->_escape($string, $this->escapeChar);
        $string = preg_replace_callback(
            $regexp,
            array(
                &$this,
                '_replacement'
            ),
            $string
        );
        $string = $this->_unescape($string, $this->escapeChar);

        return preg_replace($this->DELETED, '', $string);
    }

    private function _escape($string, $escapeChar)
    {
        if ($escapeChar) {
            $this->buffer = $escapeChar;
            return preg_replace_callback(
                '/\\' . $escapeChar . '(.)' . '/',
                array(&$this, '_escapeBis'),
                $string
            );

        } else {
            return $string;
        }
    }

    private function _unescape($string, $escapeChar)
    {
        if ($escapeChar) {
            $regexp = '/' . '\\' . $escapeChar . '/';
            $this->buffer = array('escapeChar' => $escapeChar, 'i' => 0);
            return preg_replace_callback
            (
                $regexp,
                array(&$this, '_unescapeBis'),
                $string
            );

        } else {
            return $string;
        }
    }

    public function reset()
    {
        // clear the patterns collection so that this object may be re-used
        $this->_patterns = array();
    }


    // php : we cannot pass additional data to preg_replace_callback,
    // and we cannot use &$this in create_function, so let's go to lower level

    private function _replacement($arguments)
    {
        if (empty($arguments)) {
            return '';
        }

        $i = 1;
        $j = 0;
        // loop through the patterns
        while (isset($this->_patterns[$j])) {
            $pattern = $this->_patterns[$j++];
            // do we have a result?
            if (isset($arguments[$i]) && ($arguments[$i] != '')) {
                $replacement = $pattern[self::REPLACEMENT];

                if (is_array($replacement) && isset($replacement['fn'])) {

                    if (isset($replacement['data'])) {
                        $this->buffer = $replacement['data'];
                    }
                    return call_user_func(array(&$this, $replacement['fn']), $arguments, $i);

                } elseif (is_int($replacement)) {
                    return $arguments[$replacement + $i];

                }
                $delete = ($this->escapeChar == ''
                    || strpos($arguments[$i], $this->escapeChar) === false)
                    ? '' : "\x01" . $arguments[$i] . "\x01";
                return $delete . $replacement;

                // skip over references to sub-expressions
            } else {
                $i += $pattern[self::LENGTH];
            }
        }
    }

    // encode escaped characters

    private function _backReferences($match, $offset)
    {
        $replacement = $this->buffer['replacement'];
        $quote = $this->buffer['quote'];
        $i = $this->buffer['length'];
        while ($i) {
            $replacement = str_replace('$' . $i--, $match[$offset + $i], $replacement);
        }
        return $replacement;
    }

    private function _replace_name($match, $offset)
    {
        $length = strlen($match[$offset + 2]);
        $start = $length - max($length - strlen($match[$offset + 3]), 0);
        return substr($match[$offset + 1], $start, $length) . $match[$offset + 4];
    }

    // decode escaped characters

    private function _replace_encoded($match, $offset)
    {
        return $this->buffer[$match[$offset]];
    }

    private function _escapeBis($match)
    {
        $this->_escaped[] = $match[1];
        return $this->buffer;
    }

    private function _unescapeBis()
    {
        if (isset($this->_escaped[$this->buffer['i']])
            && $this->_escaped[$this->buffer['i']] != ''
        ) {
            $temp = $this->_escaped[$this->buffer['i']];
        } else {
            $temp = '';
        }
        $this->buffer['i']++;
        return $this->buffer['escapeChar'] . $temp;
    }
}