<?php

namespace ModelHistory\Model\Entity;

use Cake\Console\Shell;
use Cake\Network\Request;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use InvalidArgumentException;

/**
 * HistoryContextTrait
 */
trait HistoryContextTrait
{
    protected $_context = null;
    protected $_contextSlug = null;
    protected $_contextType = null;

    /**
     * Sets a context given through a request to identify the creation
     * point of the revision.
     *
     * @param string  $type        Context type
     * @param object  $dataObject  Optional dataobject to get additional data from
     * @param string  $slug        Optional slug. Must implement getContexts() when using
     */
    public function setHistoryContext($type, $dataObject = null, $slug = null)
    {
        if (!in_array($type, array_keys(ModelHistory::getContextTypes()))) {
            throw new InvalidArgumentException("$type is not allowed as context type. Allowed types are: " . implode(', ', ModelHistory::getContextTypes()));
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
                $contextSlug = null;
                if ($slug !== null) {
                    $contextSlug = $slug;
                }
                break;
            case ModelHistory::CONTEXT_TYPE_CONTROLLER:
                if (!$dataObject instanceof Request) {
                    throw new InvalidArgumentException('You have to specify a Request data object for this context type.');
                }
                $context = [
                    'params' => $dataObject->params,
                    'method' => $dataObject->method()
                ];
                if ($slug !== null) {
                    $contextSlug = $slug;
                } else {
                    $contextSlug = Text::insert(':plugin/:controller/:action', [
                        'plugin' => $context['params']['plugin'],
                        'controller' => $context['params']['controller'],
                        'action' => $context['params']['action']
                    ]);
                }
                break;
            case ModelHistory::CONTEXT_TYPE_SLUG:
            default:
                $context = [];
                if ($slug === null) {
                    throw new InvalidArgumentException('You have to specify a slug for this context type.');
                }
                $contextSlug = $slug;
                break;
        }

        $this->_context = Hash::merge([
            'type' => $type
        ], $context);
        $this->_contextSlug = $contextSlug;
        $this->_contextType = $type;
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
     * Retrieve context slug
     *
     * @return string
     */
    public function getHistoryContextSlug()
    {
        return $this->_contextSlug;
    }

    /**
     * Retrieve context type
     *
     * @return string
     */
    public function getHistoryContextType()
    {
        return $this->_contextType;
    }
}
