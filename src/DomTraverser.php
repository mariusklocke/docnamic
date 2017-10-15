<?php

namespace Docnamic;

use DOMDocument;
use DOMNode;
use Docnamic\Visitor\AbstractVisitor;

/**
 * Recursive Traverser for visiting nodes of a DOMDocument
 */
class DomTraverser
{
    /** @var DOMDocument */
    private $document;
    /** @var AbstractVisitor[] */
    private $visitors;

    /**
     * DomTraverser constructor
     *
     * @param DOMDocument $document
     */
    public function __construct(DOMDocument $document)
    {
        $this->document = $document;
        $this->visitors = [];
    }

    /**
     * @param AbstractVisitor $visitor
     * @return $this
     */
    public function addVisitor(AbstractVisitor $visitor)
    {
        $this->visitors[] = $visitor;
        return $this;
    }

    /**
     * Traverses the document for all registered visitors
     */
    public function traverse()
    {
        foreach ($this->visitors as $visitor) {
            $this->doTraverse($this->document, $visitor);
        }
    }

    /**
     * Recursively traverses the node for the given visitor
     *
     * @param DOMNode         $node
     * @param AbstractVisitor $visitor
     */
    private function doTraverse(DOMNode $node, AbstractVisitor $visitor)
    {
        $visitor->enterNode($node);

        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $childNode) {
                $this->doTraverse($childNode, $visitor);
            }
        }

        $visitor->leaveNode($node);
    }
}
