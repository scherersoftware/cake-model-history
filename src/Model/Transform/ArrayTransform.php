<?php

namespace ModelHistory\Model\Transform;

class ArrayTransform extends Transform
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
        if (is_array($value)) {
            $return = '';
            foreach ($value as $v) {
                if ($return == '') {
                    $return .= $v;
                } else {
                    $return .= ', ' . $v;
                }
            }
            $value = $return;
        }

        return $value;
    }
}
