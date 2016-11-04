<?php

namespace ModelHistory\Model\Entity;

use Cake\Network\Request;

/**
 * HistoryContextTrait
 */
trait HistoryContextTrait
{
    protected $_context = null;

    /**
     * Sets a context given through a request to identify the creation
     * point of the revision.
     *
     * @param Request  $request  Request object
     */
    public function setHistoryContext(Request $request, $type)
    {
        $this->_context = [
            'type' => $type,
            'params' => $request->params,
            'method' => $request->method()
        ];
    }

    /**
     * Retrieve context
     *
     * @return array
     */
    public function getHistoryContext()
    {
        return $this->_context;
    }
}
