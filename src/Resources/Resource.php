<?php

namespace Papertrail\Resources;

use Papertrail\Papertrail;

class Resource
{

    public $attributes;

    protected $papertrail;

    public function __construct($attributes, Papertrail $papertrail = null)
    {
        if(property_exists($attributes, 'data')) {
            $this->attributes = (array) $attributes->data;
        } else {
            $this->attributes = (array) $attributes;
        }

        $this->papertrail = $papertrail;

        $this->fill();
    }

    protected function fill()
    {
        foreach ($this->attributes as $key => $value) {
            $key = $this->camelCase($key);
            if(property_exists($this, $key)) {
                $this->{$key} = $value;
            }

        }
    }

    protected function camelCase($key)
    {
        $parts = explode('_', $key);

        foreach ($parts as $i => $part) {
            if ($i !== 0) {
                $parts[$i] = ucfirst($part);
            }
        }

        return str_replace(' ', '', implode(' ', $parts));
    }

    protected function transformCollection(array $collection, $class, array $extraData = [])
    {
        return array_map(function ($data) use ($class, $extraData) {
            return new $class($data + $extraData, $this->forge);
        }, $collection);
    }

    protected function transformTags(array $tags, $separator = null)
    {
        $separator = $separator ?: ', ';

        return implode($separator, array_column($tags ?? [], 'name'));
    }
}
