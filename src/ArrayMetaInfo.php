<?php

declare(strict_types=1);

namespace CannaPress\Util\Json;

class ArrayMetaInfo extends MetaInfo
{
    public function __construct(private string $prop, private  $default, private $coerce_load, private $coerce_save, private string $json_prop)
    {
    }
    public function key(): string
    {
        return $this->json_prop;
    }
    public function load(object $instance, array|object $json): void
    {
        if (is_object($json)) {
            $json = get_object_vars($json);
        }
        $value = isset($json[$this->json_prop]) ? $json[$this->json_prop] : ($this->default)();
        if (is_null($value)) {
            $instance->{$this->prop} = null;
        } else {
            $instance->{$this->prop} = [];
            if (is_array($value)) {
                foreach ($value as $element) {
                    $instance->{$this->prop}[] = ($this->coerce_load)($element);
                }
            }
        }
    }
    public function serialize(array &$result, $instance): void
    {
        $value = $instance->{$this->prop};
        if (is_array($value)) {
            $result[$this->json_prop] = [];
            foreach ($value as $element) {
                $result[$this->json_prop][] = ($this->coerce_save)($element);
            }
        } else {
            $result[$this->json_prop] = is_null($value) ? null : [];
        }
    }
}
