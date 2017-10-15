<?php

namespace Docnamic\Visitor;

use DOMNode;

/**
 * Simple Interface for implementing the visitor pattern
 */
interface VisitorInterface
{
    public function enterNode(DOMNode $node);

    public function leaveNode(DOMNode $node);
}
