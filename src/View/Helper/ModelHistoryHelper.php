<?php
namespace ModelHistory\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\View;

/**
 * ContactPersons helper
 */
class ModelHistoryHelper extends Helper
{

    public $helpers = ['Html'];

    /**
     * Render an Contact Perosns area for the given entity
     * @return contact_persons_area View
     */
    public function modelHistoryArea($entity)
    {
        $entity = [
            'id' => $entity->id,
            'repository' => $entity->source()
        ];
        return $this->_View->element('ModelHistory.model_history_area', array('data' => $entity));
    }
}
