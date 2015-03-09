<?php
namespace ModelHistory\Controller;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Utility\Text;
use FrontendBridge\Lib\ServiceResponse;

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

    public function index($model = null, $foreignKey = null)
    {
        $this->ModelHistory->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $entity = $this->ModelHistory->getEntityWithHistory($model, $foreignKey);
        $this->set(compact('entity'));
    }
}