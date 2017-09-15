<?php
namespace ModelHistory\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Http\Response;
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
     * @param  string $model           name of the model
     * @param  string $foreignKey      id of the entity
     * @param  int    $limit           Items to show
     * @param  int    $page            Current page
     * @param  bool   $showFilterBox   Show Filter Box
     * @param  bool   $showCommentBox  Show comment Box
     * @param  string  $columnClass     div classes for column
     * @return void
     */
    public function index(
        $model = null,
        $foreignKey = null,
        $limit = null,
        $page = null,
        $showFilterBox = null,
        $showCommentBox = null,
        $includeAssociated = null,
        $columnClass = ''
    ): void {
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
        $modelHistory = $this->ModelHistory->getModelHistory($model, $foreignKey, $limit, $page, [], ['includeAssociated' => (bool)$includeAssociated]);

        $entries = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistoryCount($model, $foreignKey, [], ['includeAssociated' => (bool)$includeAssociated]);
        $showNextEntriesButton = $entries > 0 && $limit * $page < $entries;
        $showPrevEntriesButton = $page > 1;

        $contexts = [];
        if (method_exists($entity, 'getContexts')) {
            $contexts = $entity::getContexts();
        }
        $this->FrontendBridge->setBoth('contexts', $contexts);

        $this->FrontendBridge->setBoth('modelHistory', $modelHistory);
        $this->FrontendBridge->setBoth('showNextEntriesButton', $showNextEntriesButton);
        $this->FrontendBridge->setBoth('showPrevEntriesButton', $showPrevEntriesButton);
        $this->FrontendBridge->setBoth('showFilterBox', $showFilterBox);
        $this->FrontendBridge->setBoth('showCommentBox', $showCommentBox);
        $this->FrontendBridge->setBoth('includeAssociated', $includeAssociated);
        $this->FrontendBridge->setBoth('model', $model);
        $this->FrontendBridge->setBoth('foreignKey', $foreignKey);
        $this->FrontendBridge->setBoth('limit', $limit);
        $this->FrontendBridge->setBoth('columnClass', $columnClass);
        $this->FrontendBridge->setBoth('page', $page);
        $this->FrontendBridge->setBoth('searchableFields', TableRegistry::get($entity->source())->getTranslatedFields());
    }

    /**
     * Load entries and filter them if necessary.
     *
     * @param  string  $model           Model name
     * @param  string  $foreignKey      Model's foreign key
     * @param  int     $limit           Entries limit
     * @param  int     $page            Current page to view
     * @param  bool    $showFilterBox   Show Filter Box
     * @param  bool    $showCommentBox  Show comment Box
     * @param  string  $columnClass     div classes for column
     * @return \Cake\Http\Response      Index View
     */
    public function filter(
        $model = null,
        $foreignKey = null,
        $limit = null,
        $page = null,
        $showFilterBox = null,
        $showCommentBox = null,
        $includeAssociated = null,
        $columnClass = ''
    ): Response {
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
                            '`data` LIKE' => Text::insert('%:fieldName%', ['fieldName' => $filterValue])
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
                                ->day($searchValue['from']['day'])
                                ->hour(0)
                                ->minute(0)
                                ->second(0);

                            $searchConditions = Hash::merge([
                                'ModelHistory.created >=' => $fromDate
                            ], $searchConditions);
                        }
                        if (!empty($searchValue['to']['year']) && !empty($searchValue['to']['month']) && !empty($searchValue['to']['day'])) {
                            $toDate = Time::now()
                                ->year($searchValue['to']['year'])
                                ->month($searchValue['to']['month'])
                                ->day($searchValue['to']['day'])
                                ->hour(23)
                                ->minute(59)
                                ->second(59);
                            $searchConditions = Hash::merge([
                                'ModelHistory.created <=' => $toDate
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
        $modelHistory = $this->ModelHistory->getModelHistory($model, $foreignKey, $limit, $page, $conditions, ['includeAssociated' => (bool)$includeAssociated]);

        $entries = TableRegistry::get('ModelHistory.ModelHistory')->getModelHistoryCount($model, $foreignKey, $conditions, ['includeAssociated' => (bool)$includeAssociated]);
        $showNextEntriesButton = $entries > 0 && $limit * $page < $entries;
        $showPrevEntriesButton = $page > 1;

        $this->FrontendBridge->setBoth('showPrevEntriesButton', $showPrevEntriesButton);
        $this->FrontendBridge->setBoth('showNextEntriesButton', $showNextEntriesButton);
        $this->FrontendBridge->setBoth('showFilterBox', $showFilterBox);
        $this->FrontendBridge->setBoth('showCommentBox', $showCommentBox);
        $this->FrontendBridge->setBoth('includeAssociated', $includeAssociated);
        $this->FrontendBridge->setBoth('modelHistory', $modelHistory);
        $this->FrontendBridge->setBoth('limit', $limit);
        $this->FrontendBridge->setBoth('model', $model);
        $this->FrontendBridge->setBoth('page', $page);
        $this->FrontendBridge->setBoth('columnClass', $columnClass);
        $this->FrontendBridge->setBoth('foreignKey', $foreignKey);
        $this->FrontendBridge->setBoth('searchableFields', TableRegistry::get($model)->getTranslatedFields());
        $this->FrontendBridge->set('filter', isset($this->request->data['filter']) ? $this->request->data['filter'] : []);

        $entity = $this->ModelHistory->getEntityWithHistory($model, $foreignKey);

        $contexts = [];
        if (method_exists($entity, 'getContexts')) {
            $contexts = $entity::getContexts();
        }
        $this->FrontendBridge->setBoth('contexts', $contexts);

        return $this->render('index');
    }

    /**
     * Build diff for a given modelHistory entry
     *
     * @param  string   $currentId   UUID of ModelHistory entry to get diff for
     * @return void
     */
    public function diff($currentId = null): void
    {
        $historyEntry = $this->ModelHistory->get($currentId);
        $this->FrontendBridge->setBoth('diffOutput', $this->ModelHistory->buildDiff($historyEntry));
    }
}
