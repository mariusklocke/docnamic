<?php

namespace Docnamic\Visitor;

use DOMNode;
use DOMText;

/**
 * Visitor for resolving simple placeholder variables
 */
class VariableVisitor extends AbstractVisitor
{

    public function enterNode(DOMNode $node)
    {
        if ($node instanceof DOMText) {
            $tokenMap = [];
            foreach ($this->getNodeParser()->parse($node) as $token) {
                $tokenMap[$token] = (string) $this->getDataValue($token);
            }
            $this->getNodeParser()->replaceTokens($node, $tokenMap);
        }
    }

    public function leaveNode(DOMNode $node)
    {
        // Nothing to do
    }
}
