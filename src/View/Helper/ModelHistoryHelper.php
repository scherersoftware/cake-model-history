<?php
namespace ModelHistory\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
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
        $options = Hash::merge([
            'showCommentBox' => false,
            'showFilterBox' => false,
            'columnClass' => 'col-md-12'
        ], $options);

        $page = 1;
        $limit = TableRegistry::get($entity->source())->getEntriesLimit();

        $modelHistory = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistory($entity->source(), $entity->id, $limit, $page);

        $entries = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistoryCount($entity->source(), $entity->id);
        $showNextEntriesButton = $entries > 0 && $limit * $page < $entries;
        $showPrevEntriesButton = $page > 1;

        $contexts = [];
        if (method_exists($entity, 'getContexts')) {
            $contexts = $entity::getContexts();
        }

        return $this->_View->element('ModelHistory.model_history_area', [
            'modelHistory' => $modelHistory,
            'showNextEntriesButton' => $showNextEntriesButton,
            'showPrevEntriesButton' => $showPrevEntriesButton,
            'page' => $page,
            'model' => $entity->source(),
            'foreignKey' => $entity->id,
            'limit' => $limit,
            'searchableFields' => TableRegistry::get($entity->source())->getTranslatedFields(),
            'showCommentBox' => $options['showCommentBox'],
            'showFilterBox' => $options['showFilterBox'],
            'columnClass' => $options['columnClass'],
            'contexts' => $contexts
        ]);
    }

    /**
     * Convert action to bootstrap class
     *
     * @param  string  $action  History Action
     * @return string
     */
    public function actionClass($action)
    {
        switch ($action) {
            case ModelHistory::ACTION_CREATE:
                $class = 'success';
                break;
            case ModelHistory::ACTION_DELETE:
                $class = 'danger';
                break;
            case ModelHistory::ACTION_COMMENT:
                $class = 'active';
                break;
            case ModelHistory::ACTION_UPDATE:
            default:
                $class = 'info';
                break;
        }

        return $class;
    }

    /**
     * Returns the text displayed in the widget
     *
     * @return string
     */
    public function historyText($history)
    {
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
        }
        if (empty($history->user_id)) {
            $username = 'Anonymous';
        } else {
            $userNameFields = TableRegistry::get($history->model)->getUserNameFields(true);
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
        $action = '';
        switch ($history->action) {
            case ModelHistory::ACTION_UPDATE:
                $icon = 'refresh';
                break;
            case ModelHistory::ACTION_DELETE:
                $icon = 'minus-circle';
                break;
            case ModelHistory::ACTION_COMMENT:
                $icon = 'comments';
                break;
            default:
            case ModelHistory::ACTION_CREATE:
                $icon = 'plus-circle';
                break;
        }

        return '<i class="fa fa-' . $icon . '"></i>';
    }

    /**
     * Retrieve field names as localized, comma seperated string.
     *
     * @param  ModelHistory  $historyEntry  A History entry
     * @return string
     */
    public function getLocalizedFieldnames(ModelHistory $historyEntry)
    {
        $fields = join(', ', array_map(function ($value) use ($historyEntry) {
            if (!is_string($value)) {
                return $value;
            }

            // Get pre configured translations and return it if found
            $fields = TableRegistry::get($historyEntry->model)->getFields();
            if (isset($fields[$value]['translation'])) {
                return $fields[$value]['translation'];
            }

            // Try to get the generic model.field translation string
            $localeSlug = strtolower(Inflector::singularize(Inflector::delimit($historyEntry->model))) . '.' . strtolower($value);
            $translatedString = __($localeSlug);

            // Return original value when no translation was made
            if ($localeSlug == $translatedString) {
                return $value;
            }

            return $translatedString;
        }, array_keys($historyEntry->data)));

        return $fields;
    }
}
