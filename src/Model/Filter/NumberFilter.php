<?php

namespace ModelHistory\Model\Filter;

class NumberFilter extends Filter
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
        return $value;
    }
}
