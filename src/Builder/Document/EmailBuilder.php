<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Email;

class EmailBuilder extends DocumentBuilder
{
    private ?Email $document = null;

    /**
     * @throws \Exception
     */
    public static function createOrUpdate(string $path): self
    {
        $builder = new self();
        $builder->document = Email::getByPath($path);
        if (!$builder->document instanceof Email) {
            $builder->document = new Email();
            $key = basename($path);
            $builder->document->setKey($key);
            $parentPath = dirname($path);
            $parent = Email::getByPath($parentPath);
            $builder->document->setParentId($parent);
        }
        return $builder;
    }

    public function setSubject(string $subject): self
    {
        $this->document->setSubject($subject);
        return $this;
    }

    public function setTo(string $to): self
    {
        $this->document->setTo($to);
        return $this;
    }

    public function setFrom(string $from): self
    {
        $this->document->setFrom($from);
        return $this;
    }

    public function setReplyTo(string $replyTo): self
    {
        $this->document->setReplyTo($replyTo);
        return $this;
    }

    public function setCc(string $cc): self
    {
        $this->document->setCc($cc);
        return $this;
    }

    public function setBcc(string $bcc): self
    {
        $this->document->setBcc($bcc);
        return $this;
    }
}
