<?php
namespace ModelHistory\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use FrontendBridge\Lib\ServiceResponse;
use ModelHistory\Controller\AppController;

class ModelHistoryController extends AppController
{

    /**
     * Paginator settings
     *
     * @var string limit
     */
    public $paginate = [
        'limit' => 5
    ];

    /**
     * Initializer
     *
     * @return void
     */
    public function initialize()
    {
        $this->loadModel('ModelHistory.ModelHistory');
        parent::initialize();
    }

    /**
     * index function
     *
     * @param string $model       name of the model
     * @param string $foreignKey  id of the entity
     * @param int    $limit       Items to show
     * @param int    $page        Current page
     * @return void
     */
    public function index($model = null, $foreignKey = null, $limit = null, $page = null)
    {
        $this->ModelHistory->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $entity = $this->ModelHistory->getEntityWithHistory($model, $foreignKey);
        if ($this->request->is('post')) {
            $return = TableRegistry::get($model)->addCommentToHistory($entity, $this->request->data['data']);
            if (empty($return->errors())) {
                $this->Flash->success(__('forms.data_saved'));
            } else {
                $this->Flash->error(__('forms.data_not_saved'));
            }
        }
        $modelHistory = $this->ModelHistory->getModelHistory($model, $foreignKey, $limit, $page);

        $entries = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistoryCount($model, $foreignKey);
        $showNextEntriesButton = $entries > 0 && $limit * $page < $entries;
        $showPrevEntriesButton = $page > 1;

        $this->FrontendBridge->setBoth('modelHistory', $modelHistory);
        $this->FrontendBridge->setBoth('showNextEntriesButton', $showNextEntriesButton);
        $this->FrontendBridge->setBoth('showPrevEntriesButton', $showPrevEntriesButton);
        $this->FrontendBridge->setBoth('id', $foreignKey);
        $this->FrontendBridge->setBoth('model', $model);
        $this->FrontendBridge->setBoth('foreignKey', $foreignKey);
        $this->FrontendBridge->setBoth('limit', $limit);
        $this->FrontendBridge->setBoth('page', $page);
        $this->FrontendBridge->setBoth('searchableFields', TableRegistry::get($entity->source())->getSearchableFields());
    }

    /**
     * AJAX method to load more entries
     *
     * @param  string $model       Model name
     * @param  string $foreignKey  Foreign key
     * @param  int    $limit       Limit of entries to show
     * @param  int    $page        Page to get
     * @return string
     */
    public function loadEntries($model, $foreignKey, $limit, $page)
    {
        $modelHistory = $this->ModelHistory->getModelHistory($model, $foreignKey, $limit, $page);

        $entries = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistoryCount($model, $foreignKey);
        $showNextEntriesButton = $entries > 0 && $limit * $page < $entries;
        $showPrevEntriesButton = $page > 1;

        $this->FrontendBridge->setBoth('showPrevEntriesButton', $showPrevEntriesButton);
        $this->FrontendBridge->setBoth('showNextEntriesButton', $showNextEntriesButton);
        $this->FrontendBridge->setBoth('modelHistory', $modelHistory);
        $this->FrontendBridge->setBoth('limit', $limit);
        $this->FrontendBridge->setBoth('model', $model);
        $this->FrontendBridge->setBoth('page', $page);
        $this->FrontendBridge->setBoth('id', $foreignKey);

        return $this->render('/Element/model_history_rows', false);
    }
}
