<?php

namespace Statamic\SeoPro\Fieldtypes;

use Statamic\Fields\Field;
use Statamic\Fields\Fieldtype;
use Statamic\Support\Str;

class SourceFieldtype extends Fieldtype
{
    public static $handle = 'seo_pro_source';

    public $selectable = false;

    public function preProcess($data)
    {
        if (is_string($data) && Str::startsWith($data, '@seo:')) {
            return ['source' => 'field', 'value' => explode('@seo:', $data)[1]];
        }

        $originalData = $data;

        if ($data === false) {
            $data = null;
        }

        $data = $this->sourceField()
            ? $this->fieldtype()->preProcess($data)
            : $data;

        if ($originalData === false && $this->config('disableable') === true) {
            return ['source' => 'disable', 'value' => $data];
        }

        if (! $data && $this->config('inherit') !== false) {
            return ['source' => 'inherit', 'value' => $data];
        }

        return ['source' => 'custom', 'value' => $data];
    }

    public function process($data)
    {
        if ($data['source'] === 'field') {
            return '@seo:'.$data['value'];
        }

        if ($data['source'] === 'inherit') {
            return null;
        }

        if ($data['source'] === 'disable') {
            return false;
        }

        return $this->fieldtype()->process($data['value']);
    }

    public function preload()
    {
        if (! $sourceField = $this->sourceField()) {
            return null;
        }

        $value = is_array($originalValue = $this->field->value())
            ? $originalValue['value']
            : $originalValue;

        return $sourceField->setValue($value)->process()->meta();
    }

    protected function sourceField()
    {
        if (! $config = $this->config('field')) {
            return null;
        }

        return new Field(null, $config);
    }

    protected function fieldtype()
    {
        return $this->sourceField()->fieldtype();
    }
}
