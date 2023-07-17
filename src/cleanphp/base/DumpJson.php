<?php
/*******************************************************************************
 * Copyright (c) 2022. Ankio. All Rights Reserved.
 ******************************************************************************/
/**
 * Package: cleanphp\base
 * Class DumpJson
 * Created By ankio.
 * Date : 2023/6/20
 * Time : 16:35
 * Description :
 */

namespace cleanphp\base;

use ReflectionClass;
use ReflectionException;

class DumpJson
{
    private array $vars = [];

    /**
     * 自动选择类型输出
     * @param       $param
     * @param int $i
     * @param array $data
     * @return array
     */
    public function dumpType($param, int $i = 0, array $data = []): array
    {
        if(!is_iterable($param))return  $data;
        foreach ($param as $key => $value) {
            if (is_object($value)) {
                $hash = spl_object_hash($value);
                if (in_array($hash, $this->vars)) {
                    $data[$key] = "reference &". get_class($value);
                }else{
                    $this->vars[] = $hash;

                    $className = get_class($value);
                    if ($className != 'stdClass') {
                        try {
                            $reflect = new ReflectionClass($value);
                            $prop = $reflect->getProperties();
                            $len = count($prop);
                            $array = [
                                "Class [".$className."]"
                            ];
                            for ($i = 0; $i < $len; $i++) {
                                $index = $i;
                                $prop[$index]->setAccessible(true);
                                $prop_name = $prop[$index]->getName();
                                $array[$prop_name] = $this->dumpType($prop[$index]->getValue($value),$i+1);
                            }
                            $data[$key]  = $array;
                        } catch (ReflectionException $e) {
                            $data[$key]  = $e->getMessage();
                        }

                    }else{
                        foreach ($value as $items => $v){
                            $data[$key][$items]  =  $this->dumpType($v, $i + 1);
                        }

                    }




                }

            } elseif (is_array($value)) {
                if (in_array($value, $this->vars)) {
                    $data[$key]  = "reference &array";
                }else{
                    $this->vars[] = $value;
                    foreach ($value as $items => $v){
                        $data[$key][$items]  =  $this->dumpType($v, $i + 1);
                    }

                }
            }else{
                $data[$key] = $value;
            }

        }
        return $data;
    }



    public function __destruct()
    {
        unset($this->vars);
        unset($this->json);
    }
}