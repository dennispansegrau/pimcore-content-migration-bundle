<?php

namespace PimcoreContentMigration\Builder\Document;

use LogicException;
use Pimcore\Model\Document\Email;

class EmailBuilder extends PageSnippetBuilder
{
    protected static function getDocumentClass(): string
    {
        return Email::class;
    }

    public function setSubject(string $subject): static
    {
        $this->getObject()->setSubject($subject);
        return $this;
    }

    public function setTo(string $to): static
    {
        $this->getObject()->setTo($to);
        return $this;
    }

    public function setFrom(string $from): static
    {
        $this->getObject()->setFrom($from);
        return $this;
    }

    public function setReplyTo(string $replyTo): static
    {
        $this->getObject()->setReplyTo($replyTo);
        return $this;
    }

    public function setCc(string $cc): static
    {
        $this->getObject()->setCc($cc);
        return $this;
    }

    public function setBcc(string $bcc): static
    {
        $this->getObject()->setBcc($bcc);
        return $this;
    }

    public function getObject(): Email
    {
        if (!$this->document instanceof Email) {
            throw new LogicException('Email object has not been set');
        }
        return $this->document;
    }
}
