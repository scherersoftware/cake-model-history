<?php

namespace ModelHistory\Model\Transform;

class BoolTransform extends Transform
{
    /**
     * {@inheritDoc}
     */
    public function save($fieldname, $cfg, $entity)
    {
         return $entity->$fieldname;
    }

    /**
     * {@inheritDoc}
     */
    public function display($fieldname, $value, $model = null)
    {
        $value = $value === true ? 'true' : 'false';

        return $value;
    }
}
