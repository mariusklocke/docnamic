<?php

namespace Docnamic\Visitor;

use DOMElement;
use DOMNode;

/**
 * Visitor for resolving conditions
 *
 * Supports the following OpenDocument features concerning conditions:
 * - Conditional texts (text:conditional-text)
 * - Hidden texts (text:hidden-text)
 * - Sections (text:section)
 *
 * Not supported:
 * - Hidden Paragraphs (text:hidden-paragraph)
 * - anything else not mentioned here ...
 */
class ConditionVisitor extends AbstractVisitor
{
    const XML_NAMESPACE = 'urn:oasis:names:tc:opendocument:xmlns:text:1.0';

    public function enterNode(DOMNode $node)
    {
        if (!($node instanceof DOMElement)) {
            return;
        }

        /** @var $node DOMElement */
        $conditionText = $this->stripNamespacePrefix(
            $node->getAttributeNS(self::XML_NAMESPACE, 'condition')
        );
        if ($conditionText == '') {
            return;
        }

        switch ($node->nodeName) {
            case 'text:hidden-text':
                if ($this->resolveCondition($conditionText)) {
                    $node->nodeValue = $node->getAttributeNS(self::XML_NAMESPACE, 'string-value');
                } else {
                    $node->nodeValue = '';
                }
                $node->removeAttributeNS(self::XML_NAMESPACE, 'condition');
                break;

            case 'text:conditional-text':
                $node->nodeValue = $node->getAttributeNS(
                    self::XML_NAMESPACE,
                    $this->resolveCondition($conditionText) ? 'string-value-if-true' : 'string-value-if-false'
                );
                break;

            case 'text:section':
                $node->setAttributeNS(
                    self::XML_NAMESPACE,
                    'text:display',
                    $this->resolveCondition($conditionText) ? 'true' : 'none'
                );
                $node->removeAttributeNS(self::XML_NAMESPACE, 'condition');
                break;
        }
    }

    public function leaveNode(DOMNode $node)
    {
        // Nothing to do
    }

    /**
     * @param string $text
     * @return bool
     */
    private function resolveCondition($text)
    {
        return false;
    }

    /**
     * @param string $text
     * @return string
     */
    private function stripNamespacePrefix($text)
    {
        $parts = explode(':', $text, 2);
        return count($parts) > 1 ? $parts[1] : $parts[0];
    }
}
