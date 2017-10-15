<?php

namespace Docnamic\Visitor;

use DOMNode;
use Docnamic\NodeParser;

/**
 * Base class for all visitors
 */
abstract class AbstractVisitor
{
    /** @var array */
    private $data;
    /** @var NodeParser */
    private $nodeParser;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data       = $data;
        $this->nodeParser = new NodeParser();
    }

    /**
     * Will be called if the traverser enters a specific node
     *
     * @param DOMNode $node
     */
    abstract public function enterNode(DOMNode $node);

    /**
     * Will be called if the traverser leaves a specific node
     *
     * @param DOMNode $node
     */
    abstract public function leaveNode(DOMNode $node);

    /**
     * Data access method
     *
     * @param $key
     * @return mixed
     */
    protected function getDataValue($key)
    {
        if (!is_string($key) || $key === '') {
            return null;
        }

        $parts = explode('.', $key);
        $result = $this->data;
        foreach ($parts as $keyPart) {
            if (!isset($result[$keyPart])) {
                return null;
            }
            $result = $result[$keyPart];
        }

        return $result;
    }

    /**
     * @return NodeParser
     */
    protected function getNodeParser()
    {
        return $this->nodeParser;
    }
}
