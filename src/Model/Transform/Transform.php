<?php
declare(strict_types = 1);

namespace ModelHistory\Model\Transform;

use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;
use InvalidArgumentException;

abstract class Transform
{

    /**
     * Amend the data before saving.
     *
     * @param string $fieldname Field name
     * @param array $config field config
     * @param \Cake\Datasource\EntityInterface $entity entity
     * @return mixed
     */
    abstract public function save(string $fieldname, array $config, EntityInterface $entity);

    /**
     * Amend the data before displaying.
     *
     * @param string $fieldname Field name
     * @param mixed  $value Value to be amended
     * @param string $model Optional model to be used
     * @return mixed
     */
    abstract public function display(string $fieldname, $value, string $model = null);

    /**
     * Transform factory
     *
     * @param  string  $type  Transform type
     * @return Transform
     */
    public static function get(string $type): Transform
    {
        $namespace = '\\ModelHistory\\Model\\Transform\\';
        $transformClass = $namespace . Inflector::camelize($type) . 'Transform';
        if (class_exists($transformClass)) {
            return new $transformClass();
        }
        throw new InvalidArgumentException('Transform ' . $transformClass . ' not found!');
    }
}
