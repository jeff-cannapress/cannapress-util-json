<?php

declare(strict_types=1);

namespace CannaPress\Util\Json;

use DateTimeImmutable;


abstract class MetaInfo
{
    public abstract function load(object $instance, array $json): void;

    public abstract function serialize(array &$result, $instance): void;

    public static function datetime(string $prop, ?string $json_prop = null)
    {
        return self::prop(
            $prop,
            null,
            fn ($x) => !empty($x) ? DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, $x) : null,
            fn ($x) => $x?->format(DateTimeImmutable::ATOM),
            $json_prop
        );
    }
    public static function instance(string $prop, string $clazz, callable|null $default = null, ?string $json_prop = null)
    {
        if (is_null($json_prop)) {
            $json_prop = $prop;
        }
        if (is_null($default)) {
            $default = fn () => new $clazz();
        }

        return new InstanceMetaInfo($prop, $clazz, $default, $json_prop);
    }
    public static function array_prop(string $prop, mixed $default = [], callable $coerce_load = null, callable $coerce_save = null, ?string $json_prop = null): MetaInfo
    {
        if (is_null($coerce_load)) {
            $coerce_load = fn ($x) => $x;
        }
        if (is_null($coerce_save)) {
            $coerce_save = fn ($x) => $x;
        }
        if (!is_callable($default)) {
            $value = $default;
            $default = fn () => $value;
        }
        if (is_null($json_prop)) {
            $json_prop = $prop;
        }
        return new ArrayMetaInfo($prop, $default, $coerce_load, $coerce_save, $json_prop);
    }
    public static function prop(string $prop, mixed $default = null, callable $coerce_load = null, callable $coerce_save = null, ?string $json_prop = null): MetaInfo
    {
        if (is_null($coerce_load)) {
            $coerce_load = fn ($x) => $x;
        }
        if (is_null($coerce_save)) {
            $coerce_save = fn ($x) => $x;
        }
        if (!is_callable($default)) {
            $value = $default;
            $default = fn () => $value;
        }
        if (is_null($json_prop)) {
            $json_prop = $prop;
        }
        return new PropertyMetaInfo($prop, $default, $coerce_load, $coerce_save, $json_prop);
    }

    public static function loadProps(object $instance, $json): object
    {
        if (is_object($instance)) {
            if (is_string($json)) {
                $json = json_decode($json);
            }
            if (is_object($json)) {
                $json = get_object_vars($json);
            }
            if (!is_null($json)) {
                foreach ($json as $key => $value) {
                    $instance->{$key} = $value;
                }
            }
        }
        return $instance;
    }
}
