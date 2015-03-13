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
        if ($this->request->is('post')) {
            $return = TableRegistry::get($model)->addCommentToHistory($entity, $this->request->data['data']);
            if (empty($return->errors())) {
                $entity = $this->ModelHistory->getEntityWithHistory($model, $foreignKey);
                $this->Flash->success(__('forms.data_saved'));
                $this->FrontendBridge->setJson('success', true);
            }   else {
                $this->Flash->error(__('forms.data_not_saved'));
            }
        }
        $this->set(compact('entity'));
        $this->FrontendBridge->setJson('model', $model);
        $this->FrontendBridge->setJson('foreignKey', $foreignKey);
    }
}