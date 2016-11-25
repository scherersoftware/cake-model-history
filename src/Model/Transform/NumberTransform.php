<?php

namespace ModelHistory\Model\Transform;

class NumberTransform extends Transform
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $value, $model = null)
    {
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
