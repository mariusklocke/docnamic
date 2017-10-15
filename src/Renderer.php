<?php

namespace Docnamic;

use DOMDocument;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use ZipArchive;
use Docnamic\Visitor\ConditionVisitor;
use Docnamic\Visitor\LoopVisitor;
use Docnamic\Visitor\VariableVisitor;

/**
 * The main template rendering utility class
 */
class Renderer
{
    /** @var ZipArchive */
    private $template;

    /** @var ZipArchive */
    private $target;

    /** @var array */
    private $data;

    /**
     * Loads the template ODT file
     *
     * @param string $path
     *
     * @throws InvalidArgumentException if the template file cannot be opened by ZipArchive
     *
     * @return $this
     */
    public function loadTemplate($path)
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new InvalidArgumentException('Template does not seem to be valid ODT file.');
        }

        $this->template = $zip;
        return $this;
    }

    /**
     * Renders the final document and saves it to given path
     *
     * @param string $targetPath
     *
     * @throws LogicException if the template has not been loaded or data has not been set
     * @throws RuntimeException if the specified target path is not writable
     *
     * @return $this
     */
    public function render($targetPath)
    {
        if (null === $this->template) {
            throw new LogicException('Template has not been loaded');
        }
        if (null === $this->data) {
            throw new LogicException('Cannot render a template without data');
        }
        if (false === copy($this->template->filename, $targetPath)) {
            throw new RuntimeException('Cannot write to target path');
        }
        $this->target = new ZipArchive();
        $this->target->open($targetPath);

        $fileCount = $this->target->numFiles;
        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = $this->target->getNameIndex($i);
            if (preg_match('/\.xml$/i', $fileName)) {
                $fileContent = $this->target->getFromIndex($i);
                if (empty($fileContent)) {
                    continue;
                }

                $doc = new DOMDocument();
                $doc->loadXML($fileContent);
                $this->createTraverser($doc)->traverse();
                $this->target->addFromString($fileName, $doc->saveXML());
            }
        }

        $this->target->close();
        return $this;
    }

    /**
     * Set the data which should be present in the final document
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param DOMDocument $document
     * @return DomTraverser
     */
    private function createTraverser(DOMDocument $document)
    {
        $traverser = new DomTraverser($document);
        $traverser->addVisitor(new LoopVisitor($this->data));
        $traverser->addVisitor(new VariableVisitor($this->data));
        $traverser->addVisitor(new ConditionVisitor($this->data));

        return $traverser;
    }
}
