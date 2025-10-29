<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Email;

class EmailBuilder extends PageSnippetBuilder
{
    protected static function getDocumentClass(): string
    {
        return Email::class;
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
