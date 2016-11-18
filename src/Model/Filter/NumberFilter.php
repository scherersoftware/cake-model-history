<?php

namespace ModelHistory\Model\Filter;

class NumberFilter extends Filter
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
        return $value;
    }
}
