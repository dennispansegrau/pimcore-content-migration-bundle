<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Model\Document\Email;

class EmailBuilder extends PageSnippetBuilder
{
    protected static function getDocumentClass(): string
    {
        return Email::class;
    }

    public function setSubject(string $subject): static
    {
        $this->document->setSubject($subject);
        return $this;
    }

    public function setTo(string $to): static
    {
        $this->document->setTo($to);
        return $this;
    }

    public function setFrom(string $from): static
    {
        $this->document->setFrom($from);
        return $this;
    }

    public function setReplyTo(string $replyTo): static
    {
        $this->document->setReplyTo($replyTo);
        return $this;
    }

    public function setCc(string $cc): static
    {
        $this->document->setCc($cc);
        return $this;
    }

    public function setBcc(string $bcc): static
    {
        $this->document->setBcc($bcc);
        return $this;
    }
}
