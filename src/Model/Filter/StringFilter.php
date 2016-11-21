<?php

namespace ModelHistory\Model\Filter;

class StringFilter extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $value, $model = null) {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value, $model = null)
    {
        return trim($value);
    }
}
