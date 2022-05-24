<?php namespace Anomaly\ImageFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeAccessor;

class ImageFieldTypeAccessor extends FieldTypeAccessor
{

    /**
     * Desired data properties.
     *
     * @var array
     */
    protected $properties = [
        'x',
        'y',
        'width',
        'height',
    ];

    /**
     * Set the value.
     *
     * @param $value
     * @return array
     */
    public function set($value)
    {
        $entry = $this->fieldType->getEntry();

        $attributes = $entry->getAttributes();

        if (is_numeric($value)) {
            $attributes[$this->fieldType->getColumnName()] = $value;
        }

        if (is_object($value) && $data = $this->toData($value)) {
            $attributes[$this->fieldType->getField() . '_data'] = json_encode($data);
        }

        if (is_array($value) && $data = $this->toData($value)) {
            $attributes[$this->fieldType->getField() . '_data'] = json_encode($data);
        }

        if (is_null($value)) {
            $attributes[$this->fieldType->getColumnName()]      = $value;
            $attributes[$this->fieldType->getField() . '_data'] = $value;
        }

        $entry->setRawAttributes($attributes);
    }

    /**
     * Convert the object to data.
     *
     * @param $value
     * @return array
     */
    public function toData($value)
    {
        $data = [];

        foreach ($this->properties as $property) {
            $data[$property] = (int)$value->{$property};
        }

        return $data;
    }
}
