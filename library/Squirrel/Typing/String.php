<?php

namespace Squirrel\Typing;

//$string->encode('base64|hex'), $string->decode('base64|hex') extends Encoding
class String extends Object {
    protected $string;

    public function __construct($string = '') {
        $this->string = (string) $string;
    }

    public function __toString() {
        return $this->string;
    }

    public function isEmpty()
    {
        return $this->length() === 0;
    }

    public function length()
    {
        return strlen($this->string);
    }

    public function append($string)
    {
        $this->string .= $string;
    }

    public function prepend($string)
    {
        $this->string = $string . $this->string;
    }

    public function match($pattern)
    {
        return preg_match($pattern, $this->string) === 1;
    }

    public function replace($search, $replace, $regexp = false) {
        if ($regexp) {
            return static::cast(preg_replace($search, $replace, $this->string));
        }

        return static::cast(str_replace($search, $replace, $this->string));
    }

    public function unaccent() {
        $accents = Config::instance('accents');
        return $this->replace($accents->keys(), $accents->values());
    }

    public function upper() {
        return static::cast(strtoupper($this->string));
    }

    public function lower() {
        return static::cast(strtolower($this->string));
    }

    public function trim($list = null) {
        if ($list === null) {
            return static::cast(trim($this->string));
        }
        
        return static::cast(trim($this->string, $list));
    }

    public function find($string)
    {
        return strpos($this->string, $string);
    }

    public function urlize() {
        return $this
            ->lower()
            ->unaccent()
            ->replace('/[^a-z0-9]/', '-', true)
            ->replace('/-+/', '-', true)
            ->trim('-');
    }

    public function decamelize($separator = ' ') {
        return $this
            ->replace('/([a-z])([A-Z])/', '$1' . $separator . '$2', true)
            ->replace('/([A-Z])([A-Z][a-z])/', '$1' . $separator . '$2', true);
    }

    public function camelize($first = false) {
        $string = $this
            ->trim()
            ->decamelize()
            ->lower()
            ->replace('/[^a-z0-9]/', ' ', true)
            ->ucwords()
            ->replace(' ', '');

        return $first ? $string : $string->lcfirst();
    }

    public function ucwords() {
        return static::cast(ucwords($this->string));
    }

    public function lcfirst() {
        return static::cast(lcfirst($this->string));
    }

    public function ucfirst() {
        return static::cast(ucfirst($this->string));
    }

    public function cut($start, $length = null) {
        if ($length === null) {
            return static::cast(substr($this->string, $start));
        }
        return static::cast(substr($this->string, $start, $length));
    }

    public function compile(array $params) {
        $string = $this;

        foreach ($params as $search => $replace) {

            $string = $string->replace($search, $replace);
        }

        return $string;
    }

    public function split($delimiter)
    {
        return Collection::cast(explode($delimiter, $this->string));
    }

    public function compare($string, $length = null)
    {
        if ($length === null)
        {
            return strcmp($this->string, (string) $string);
        }

        return strncmp($this->string, (string) $string, $length);
    }
}
