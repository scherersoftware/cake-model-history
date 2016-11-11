<?php
namespace ModelHistory\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
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
    public function loadEntries($model = null, $foreignKey = null, $limit = null, $page = null)
    {
        $modelHistory = $this->ModelHistory->getModelHistory($model, $foreignKey, $limit, $page);

        $entries = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistoryCount($model, $foreignKey);
        $showNextEntriesButton = $entries > 0 && $limit * $page < $entries;
        $showPrevEntriesButton = $page > 1;

        $this->FrontendBridge->setBoth('showPrevEntriesButton', $showPrevEntriesButton);
        $this->FrontendBridge->setBoth('showNextEntriesButton', $showNextEntriesButton);
        $this->FrontendBridge->setBoth('modelHistory', $modelHistory);
        $this->FrontendBridge->setBoth('page', $page);

        return $this->render('/Element/model_history_rows', false);
    }

    /**
     * [filter description]
     * @param  [type] $model      [description]
     * @param  [type] $foreignKey [description]
     * @param  [type] $limit      [description]
     * @param  [type] $page       [description]
     * @return [type]             [description]
     */
    public function filter($model = null, $foreignKey = null, $limit = null, $page = null)
    {
        $this->request->allowMethod(['post']);

        $filterConditions = [];
        if (isset($this->request->data['filter'])) {
            foreach ($this->request->data['filter'] as $filterName => $filterValue) {
                if (empty($filterValue)) {
                    continue;
                }
                switch ($filterName) {
                    case 'fields':
                        $filterConditions = Hash::merge([
                            'data LIKE' => Text::insert('%:fieldName%', ['fieldName' => $filterValue])
                        ], $filterConditions);
                    break;
                }
            }
        }

        // Prepare conditions
        $searchConditions = [];
        if (isset($this->request->data['search'])) {
            foreach ($this->request->data['search'] as $searchName => $searchValue) {
                if (empty($searchValue)) {
                    continue;
                }
                switch ($searchName) {
                    case 'date':
                        if (!empty($searchValue['from']['year']) && !empty($searchValue['from']['month']) && !empty($searchValue['from']['day'])) {
                            $fromDate = Time::now()
                                ->year($searchValue['from']['year'])
                                ->month($searchValue['from']['month'])
                                ->day($searchValue['from']['day']);

                            $searchConditions = Hash::merge([
                                'created >=' => $fromDate
                            ], $searchConditions);
                        }
                        if (!empty($searchValue['to']['year']) && !empty($searchValue['to']['month']) && !empty($searchValue['to']['day'])) {
                            $toDate = Time::now()
                                ->year($searchValue['to']['year'])
                                ->month($searchValue['to']['month'])
                                ->day($searchValue['to']['day']);

                            $searchConditions = Hash::merge([
                                'created <=' => $toDate
                            ], $searchConditions);
                        }
                        break;
                    case 'context_type':
                        $searchConditions = Hash::merge([
                            'context_type' => $searchValue
                        ], $searchConditions);
                        break;
                    case 'context_slug':
                        $searchConditions = Hash::merge([
                            'context_slug' => $searchValue
                        ], $searchConditions);
                        break;
                }
            }
        }
        $conditions = Hash::merge($filterConditions, $searchConditions);
        // debug($conditions);
        $modelHistory = $this->ModelHistory->getModelHistory($model, $foreignKey, $limit, 1, $conditions);

        $entries = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistoryCount($model, $foreignKey, $conditions);
        $showNextEntriesButton = $entries > 0 && $limit * $page < $entries;
        $showPrevEntriesButton = $page > 1;

        $this->FrontendBridge->setBoth('showPrevEntriesButton', $showPrevEntriesButton);
        $this->FrontendBridge->setBoth('showNextEntriesButton', $showNextEntriesButton);
        $this->FrontendBridge->setBoth('modelHistory', $modelHistory);
        $this->FrontendBridge->setBoth('limit', $limit);
        $this->FrontendBridge->setBoth('model', $model);
        $this->FrontendBridge->setBoth('page', $page);
        $this->FrontendBridge->setBoth('foreignKey', $foreignKey);
        $this->FrontendBridge->setBoth('searchableFields', TableRegistry::get($model)->getSearchableFields());
        $this->FrontendBridge->set('filter', $this->request->data['filter']);

        return $this->render('index');
    }
}
