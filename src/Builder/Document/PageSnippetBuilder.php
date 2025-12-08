<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Editable;
use function file_get_contents;

use LogicException;
use Pimcore\Model\Document\PageSnippet;
use RuntimeException;

abstract class PageSnippetBuilder extends DocumentBuilder
{
    public function setController(?string $controller): static
    {
        $this->getObject()->setController($controller);
        return $this;
    }

    public function setTemplate(?string $template): static
    {
        $this->getObject()->setTemplate($template);
        return $this;
    }

    public function setRawEditable(string $name, string $type, mixed $data): static
    {
        $this->getObject()->setRawEditable($name, $type, $data);
        return $this;
    }

    public function setEditable(Editable $editable): static
    {
        $this->getObject()->setEditable($editable);
        return $this;
    }

    public function loadWysiwygFromPath(string $name, string $path): static
    {
        $data = file_get_contents($path);
        if ($data === false) {
            throw new RuntimeException("Could not read file: $path");
        }
        $this->getObject()->setRawEditable($name, 'wysiwyg', $data);
        return $this;
    }

    public function getObject(): PageSnippet
    {
        if (!$this->document instanceof PageSnippet) {
            throw new LogicException('PageSnippet object has not been set');
        }
        return $this->document;
    }
}
