<?php

namespace ModelHistory\Model\Transform;

class HashTransform extends Transform
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
        $return = '';
        foreach ($value as $k => $v) {
            $return .= $k . ': ' . $v . '</br>';
        }

        return $return;
    }
}
