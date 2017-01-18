<?php

namespace ModelHistory\Model\Transform;

class NumberTransform extends Transform
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $config, $entity)
    {
         return $entity->$fieldname;
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value, $model = null)
    {
        return $value;
    }
}
