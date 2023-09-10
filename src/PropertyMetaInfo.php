<?php

declare(strict_types=1);

namespace CannaPress\Util\Json;

class PropertyMetaInfo extends MetaInfo
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
        $value = ($this->coerce_load)($value);
        $instance->{$this->prop} = $value;
    }
    public function serialize(array &$result, $instance): void
    {
        $value = $instance->{$this->prop};
        $result[$this->json_prop] = ($this->coerce_save)($value);
    }
}
