<?php

namespace PimcoreContentMigration\Builder\Document;

abstract class PageSnippetBuilder extends DocumentBuilder
{
    public function setController(?string $controller): self
    {
        $this->document->setController($controller);
        return $this;
    }

    public function setTemplate(?string $template): self
    {
        $this->document->setTemplate($template);
        return $this;
    }

    public function setRawEditable(string $name, string $type, mixed $data): self
    {
        $this->document->setRawEditable($name, $type, $data);
        return $this;
    }

    public function setRawEditableFromJson(string $name, string $type, string $json): self
    {
        $decodedData = json_decode($json, true);
        $this->document->setRawEditable($name, $type, $decodedData);
        return $this;
    }

    public function loadWysiwygFromPath(string $name, string $path): self
    {
        $data = file_get_contents($path);
        if ($data === false) {
            throw new \RuntimeException("Could not read file: $path");
        }
        $this->document->setRawEditable($name, 'wysiwyg', $data);
        return $this;
    }
}
