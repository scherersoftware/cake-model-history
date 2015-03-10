<?php
namespace ModelHistory\Controller;

use ModelHistory\Controller\AppController;

class ModelHistoryController extends AppController
{
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
}
