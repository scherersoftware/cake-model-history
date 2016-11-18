<?php

namespace ModelHistory\Model\Filter;

class BoolFilter extends Filter
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
        $value = $value === true ? 'true' : 'false';
        return $value;
    }
}
