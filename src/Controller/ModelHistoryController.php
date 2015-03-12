<?php
namespace ModelHistory\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
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
     * showModelHistory method shows the modelhistory for one entity ind model
     *
     * @return void
     */
    public function showModelHistory($repository, $foreignKey)
    {
        $modelHistory = $this->ModelHistory->find()
            ->where([
                'model' => $repository,
                'foreign_key' => $foreignKey
            ])
            ->contain(['Users']);
        $this->set('modelHistory', $this->paginate($modelHistory));
        $this->FrontendBridge->setJson('success', true);
    }

    /**
     * index function
     *
     * @param string $model name of the model
     * @param string $foreignKey id of the entity
     * @return void
     */
    public function index($model = null, $foreignKey = null)
    {
        $this->ModelHistory->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $entity = $this->ModelHistory->getEntityWithHistory($model, $foreignKey);
        $this->set(compact('entity'));
    }

    /**
     * saves a comment to the model history of the given entity function
     *
     * @return void
     */
    public function saveComment($repository, $entityId)
    {
        $this->request->allowMethod('post');
        $this->loadModel($repository);
        $entity = $this->$repository->get($entityId);
        $data = $this->request->data['comment'];
        if ($this->$repository->addCommentToHistory($entity, $data)) {
            $this->FrontendBridge->setJson('success', true);
        } else {
            $this->Flash->error(__('forms.data_not_saved'));
        }
        $this->render(false);
    }
}
