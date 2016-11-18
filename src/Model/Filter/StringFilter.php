<?php

namespace ModelHistory\Model\Filter;

class StringFilter extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $value) {
        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value)
    {
        return trim($value);
    }
}
