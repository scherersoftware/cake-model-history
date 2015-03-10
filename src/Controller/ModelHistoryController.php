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
     * @param string $model 
     * @param string $foreignKey 
     * @return void
     * @author Michael Hoffmann
     */
    public function index($model = null, $foreignKey = null)
    {
        $this->ModelHistory->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $entity = $this->ModelHistory->getEntityWithHistory($model, $foreignKey);
        $this->set(compact('entity'));
    }
}


