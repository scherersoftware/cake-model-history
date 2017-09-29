<?php
declare(strict_types = 1);

namespace ModelHistory\Model\Transform;

use Cake\Datasource\EntityInterface;

class BoolTransform extends Transform
{
    /**
     * Amend the data before saving.
     *
     * @param string $fieldname Field name
     * @param array $config field config
     * @param \Cake\Datasource\EntityInterface $entity entity
     * @return mixed
     */
    public function save(string $fieldname, array $config, EntityInterface $entity)
    {
         return $entity->$fieldname;
    }

    /**
     * Amend the data before displaying.
     *
     * @param string $fieldname Field name
     * @param mixed  $value Value to be amended
     * @param string $model Optional model to be used
     * @return mixed
     */
    public function display(string $fieldname, $value, string $model = null)
    {
        $value = $value === true ? 'true' : 'false';

        return $value;
    }
}
