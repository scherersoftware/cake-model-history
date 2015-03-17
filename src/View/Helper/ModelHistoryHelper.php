<?php
namespace ModelHistory\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\View;
use ModelHistory\Model\Entity\ModelHistory;

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
    public function modelHistoryArea($entity, $panel = null, $commentBox = null)
    {
        $entity = [
            'id' => $entity->id,
            'repository' => $entity->source(),
            'comment-box' => $commentBox
        ];
        if ($panel) {
            return $this->_View->element('ModelHistory.model_history_area_panel', array('data' => $entity));
        } else {
            return $this->_View->element('ModelHistory.model_history_area', array('data' => $entity));
        }
    }

    /**
     * Returns the text displayed in the widget
     *
     * @return string
     */
    public function historyText($history)
    {
        $customActions = TableRegistry::get($history->model)->getCustomActions();
        $action = '';
        switch ($history->action) {
            case ModelHistory::ACTION_CREATE:
                $action = __d('model_history', 'model_history');
                break;
            case ModelHistory::ACTION_UPDATE:
                $action = __d('model_history', 'updated');
                break;
            case ModelHistory::ACTION_DELETE:
                $action = __d('model_history', 'deleted');
                break;
            case ModelHistory::ACTION_COMMENT:
                $action = __d('model_history', 'commented');
                break;
            default:
                foreach ($customActions as $customAction) {
                    if ($customAction['action'] == $history->action) {
                        $action = __d('model_history', $customAction['action']);
                    }
                }
        }
        if (empty($history->user_id)) {
            $username = 'Anonymous';
        } else {
            $username = $history->user->full_name;
        }
        return ucfirst($action) . ' ' . __d('model_history', 'by') . ' ' . $username;
    }
}
