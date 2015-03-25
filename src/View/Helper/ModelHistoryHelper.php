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
        if ($commentBox) {
             $commentBox = 'on';
        } else {
            $commentBox = 'off';
        }
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
                $action = __d('model_history', 'created');
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
                        $action = $customAction['translation'];
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

    /**
     * Returns the badge displayed in the widget
     *
     * @return string
     */
    public function historyBadge($history)
    {
        $customActions = TableRegistry::get($history->model)->getCustomActions();
        $action = '';
        switch ($history->action) {
            case ModelHistory::ACTION_CREATE:
                $style = 'success';
                $icon = 'fa-plus-circle';
                $color = '';
                break;
            case ModelHistory::ACTION_UPDATE:
                $style = 'info';
                $icon = 'fa-refresh';
                $color = '';
                break;
            case ModelHistory::ACTION_DELETE:
                $style = 'danger';
                $icon = 'fa-minus-circle';
                $color = '';
                break;
            case ModelHistory::ACTION_COMMENT:
                $style = 'primary';
                $icon = 'fa-comments';
                $color = '';
                break;
            default:
                $style = '';
                foreach ($customActions as $customAction) {
                    if ($customAction['action'] == $history->action) {
                        $color = $customAction['color'];
                        $icon = 'fa fa-' . $customAction['icon'];
                    }
                }
        }
        return '<div class="timeline-badge ' . $style . '" style="background-color:' . $color . '"><i class="fa ' . $icon . '"></i></div>';
    }
}
