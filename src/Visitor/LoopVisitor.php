<?php

namespace Docnamic\Visitor;

use DOMNode;
use DOMText;

/**
 * Visitor for resolving loops
 */
class LoopVisitor extends AbstractVisitor
{
    /** @var DOMNode Reference to the element which should be iterated over */
    private $iterableNode;
    /** @var array current loop context */
    private $context;
    /** @var int|null */
    private $index;

    public function enterNode(DOMNode $node)
    {
        if ($this->isIterable($node)) {
            $this->iterableNode = $node;
            return;
        }

        if ($this->iterableNode !== null && $node instanceof DOMText) {
            $tokens = $this->getLoopVars($node);
            if (empty($tokens)) {
                return;
            }

            if (!is_array($this->context)) {
                $context = $this->getLoopContext($tokens);
                if (!is_array($context)) {
                    return;
                }

                $this->initLoop($context);
            }

            $this->replaceLoopVars($node, $tokens);
        }
    }

    public function leaveNode(DOMNode $node)
    {
        if ($node === $this->iterableNode) {
            $this->iterableNode = null;

            if (is_array($this->context)) {
                $this->iterate();
            }
        }
    }

    /**
     * Determines whether the node is iterable
     *
     * @param DOMNode $node
     * @return bool
     */
    private function isIterable(DOMNode $node)
    {
        return ($node->nodeName == 'table:table-row');
    }

    /**
     * @param DOMNode $node
     * @return array
     */
    private function getLoopVars(DOMNode $node)
    {
        return array_filter($this->getNodeParser()->parse($node), function ($token) {
            return (false !== strstr($token, '[]'));
        });
    }

    /**
     * @param DOMNode $node
     * @param array   $nodeVars
     */
    private function replaceLoopVars(DOMNode $node, array $nodeVars)
    {
        $tokenMap = [];
        foreach ($nodeVars as $nodeVar) {
            list(,$property) = explode('[]', $nodeVar);
            $property = substr($property, 1);
            if (isset($this->context[$this->index][$property])) {
                $tokenMap[$nodeVar] = (string) $this->context[$this->index][$property];
            } else {
                $tokenMap[$nodeVar] = '';
            }
        }
        $this->getNodeParser()->replaceTokens($node, $tokenMap);
    }

    /**
     * @param array $tokens
     * @return array|null
     */
    private function getLoopContext(array $tokens)
    {
        $varText = reset($tokens);
        list($dataKey) = explode('[]', $varText);
        $context = $this->getDataValue($dataKey);
        if (!is_array($context)) {
            return null;
        }

        return $context;
    }

    /**
     * @param array $context
     */
    private function initLoop(array $context)
    {
        $this->context = $context;
        $this->index   = 0;

        $referenceNode = $this->iterableNode->nextSibling;
        // Clone the iterable node n-1 times
        for ($i = 1; $i < count($this->context); $i++) {
            $clone = $this->iterableNode->cloneNode(true);
            $this->iterableNode->parentNode->insertBefore($clone, $referenceNode);
        }
    }

    /**
     * Performs an iteration in the loop context
     */
    private function iterate()
    {
        $this->index++;
        if ($this->index == count($this->context)) {
            // Loop finished, reset loop state
            $this->context = null;
            $this->index   = null;
        }
    }
}
