<?php
/*
 * Package: cleanphp\objects
 * Class ArgObject
 * Created By ankio.
 * Date : 2022/11/14
 * Time : 21:08
 * Description :
 */

namespace cleanphp\base;

class ArgObject
{
    public function __construct(?array $item = [])
    {
        if (!empty($item)) {
            foreach (get_object_vars($this) as $key => &$val) {
                $data = $val;
                if (array_key_exists($key, $item)) {
                    $data = $item[$key];
                }

                if($this->onParseType($key, $data, $val)){
                    $data = parse_type($val, $data);
                    $this->$key = $data;
                }
            }
        }
    }

    public function onParseType(string $key, mixed &$val, mixed $demo): bool
    {
        return true;
    }

    public function toArray(): array
    {
        $ret = get_object_vars($this);
        array_walk($ret, function (&$value, $key) {
            $this->onToArray($key, $value);
        });
        return $ret;
    }

    public function onToArray($key, &$value)
    {

    }

    public function hash(): string
    {
        return md5(implode(",", get_object_vars($this)));
    }

    public function getDisableKeys(): array
    {
        return [];
    }

    public function merge(ArgObject $object): void
    {
        $disable = $this->getDisableKeys();
        foreach (get_object_vars($this) as $key => $val) {
            if (property_exists($object, $key) && !in_array($key, $disable)) {
                $this->onMerge($key,$this->$key,$object->$key);
                $this->$key = $object->$key;
            }
        }
    }

    public function onMerge($key,$raw, &$val):void
    {

    }
    public function mergeArray(array $object): void
    {
        $disable = $this->getDisableKeys();
        foreach (get_object_vars($this) as $key => $val) {
            if (array_key_exists($key,$object) && !in_array($key, $disable)) {
                $data = $object[$key];
                if ($this->onParseType($key, $data, $val)) {
                    $this->onMerge($key,$this->$key,$data);
                    $data = parse_type($val, $data);
                    $this->$key = $data;
                }
            }
        }
    }
}
