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
     * Render the model history area where needed
     *
     * @return string History area
     */
    public function modelHistoryArea($entity, array $options = [])
    {
        $modelHistory = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistory($entity->source(), $entity->id);
        return $this->_View->element('ModelHistory.model_history_area', ['modelHistory' => $modelHistory]);
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
            $userNameFields = TableRegistry::get($history->model)->getUserNameFields();
            $firstname = $history->user->{$userNameFields['firstname']};
            $lastname = $history->user->{$userNameFields['lastname']};
            $username = $firstname . ' ' . $lastname;
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
                $icon = 'plus-circle';
                $color = '';
                break;
            case ModelHistory::ACTION_UPDATE:
                $style = 'info';
                $icon = 'refresh';
                $color = '';
                break;
            case ModelHistory::ACTION_DELETE:
                $style = 'danger';
                $icon = 'minus-circle';
                $color = '';
                break;
            case ModelHistory::ACTION_COMMENT:
                $style = 'primary';
                $icon = 'comments';
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
        return '<div class="timeline-badge ' . $style . '" style="background-color:' . $color . '"><i class="fa fa-' . $icon . '"></i></div>';
    }
}
