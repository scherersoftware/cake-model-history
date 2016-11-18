<?php

namespace ModelHistory\Model\Filter;

use Cake\Utility\Inflector;

abstract class Filter
{

    /**
     * Amend the data before displaying.
     *
     * @param  string  $fieldname  Field name
     * @param  mixed   $value      Value to be amended
     * @return mixed
     */
    abstract public function display($fieldname, $value);

    /**
     * Amend the data before saving.
     *
     * @param  mixed  $value  Value to be amended
     * @param  mixed  $value  Value to be amended
     * @return mixed
     */
    abstract public function save($fieldname, $value);

    /**
     * Filter factory
     *
     * @param  string  $type  Filter type
     * @return Filter
     */
    public static function getFilter($type)
    {
        $namespace = '\\ModelHistory\\Model\\Filter\\';
        $filterClass = $namespace . ucfirst(strtolower($type));
        if (class_exists($filterClass)) {
            return new $filterClass();
        }
        throw new InvalidArgumentException('Filter ' . $filterClass . ' not found!');
    }
}
