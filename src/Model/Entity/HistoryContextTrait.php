<?php

namespace ModelHistory\Model\Entity;

use Cake\Console\Shell;
use Cake\Network\Request;
use Cake\Utility\Hash;
use InvalidArgumentException;

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
     * @param object  $dataObject  Data object to get context infos from
     */
    public function setHistoryContext($dataObject, $type)
    {
        if (!in_array($type, array_keys($this->_getTypes()))) {
            throw new InvalidArgumentException("$type is not allowed as context type. Allowed types are: " . implode(', ', $this->_getTypes()));
        }

        switch ($type) {
            case ModelHistory::CONTEXT_TYPE_SHELL:
                if (!$dataObject instanceof Shell) {
                    throw new InvalidArgumentException('You have to specify a Shell data object for this context type.');
                }
                $context = [
                    'OptionParser' => $dataObject->OptionParser,
                    'interactive' => $dataObject->interactive,
                    'params' => $dataObject->params,
                    'command' => $dataObject->command,
                    'args' => $dataObject->args,
                    'name' => $dataObject->name,
                    'plugin' => $dataObject->plugin,
                    'tasks' => $dataObject->tasks,
                    'taskNames' => $dataObject->taskNames
                ];
                break;
            case ModelHistory::CONTEXT_TYPE_CONTROLLER:
            default:
                if (!$dataObject instanceof Request) {
                    throw new InvalidArgumentException('You have to specify a Request data object for this context type.');
                }
                $context = [
                    'params' => $dataObject->params,
                    'method' => $dataObject->method()
                ];
                break;
        }

        $this->_context = Hash::merge([
            'type' => $type
        ], $context);
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

    /**
     * Retrieve available context types
     *
     * @return array
     */
    protected function _getTypes()
    {
        return [
            ModelHistory::CONTEXT_TYPE_CONTROLLER => __d('model_history', 'context.type.controller'),
            ModelHistory::CONTEXT_TYPE_SHELL => __d('model_history', 'context.type.shell')
        ];
    }
}
