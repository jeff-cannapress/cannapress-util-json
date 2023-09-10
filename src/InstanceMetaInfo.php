<?php

declare(strict_types=1);

namespace CannaPress\Util\Json;

class InstanceMetaInfo extends MetaInfo
{
    public function __construct(private string $prop, private string $clazz, private $default,  private string $json_prop)
    {
    }

    public function load(object $instance, array|object $json): void
    {
        $value = isset($json[$this->json_prop]) ? $json[$this->json_prop] : null;
        try {
            $to_assign = ($this->default)();
            if (!is_null($to_assign)) {
                if(method_exists($this->clazz, 'loadInstance')){
                    call_user_func([$this->clazz, 'loadInstance'], $to_assign, $value);
                }
                else{
                    $to_assign = MetaInfo::loadProps($to_assign, $value);
                }
                
            }
            $instance->{$this->prop} = $to_assign;
        } catch (\Exception $err) {
            var_dump($err);
        }
    }
    public function serialize(array &$result, $instance): void
    {
        $value = $instance->{$this->prop};
        $result[$this->json_prop] = $value?->jsonSerialize();
    }
}
