<?php

namespace PimcoreContentMigration\Builder\Document;

use Pimcore\Bundle\NewsletterBundle\Model\Document\Newsletter;

class NewsletterBuilder extends EmailBuilder
{
    protected static function getDocumentClass(): string
    {
        return Newsletter::class;
    }

    public function setPlaintext(string $plaintext): static
    {
        $this->document->setPlaintext($plaintext);
        return $this;
    }

    public function setEnableTrackingParameters(bool $enableTrackingParameters): static
    {
        $this->document->setEnableTrackingParameters($enableTrackingParameters);
        return $this;
    }

    public function setTrackingParameterSource(string $trackingParameterSource): static
    {
        $this->document->setTrackingParameterSource($trackingParameterSource);
        return $this;
    }

    public function setTrackingParameterMedium(string $trackingParameterMedium): static
    {
        $this->document->setTrackingParameterMedium($trackingParameterMedium);
        return $this;
    }

    public function setTrackingParameterName(string $trackingParameterName): static
    {
        $this->document->setTrackingParameterName($trackingParameterName);
        return $this;
    }

    public function setSendingMode(string $sendingMode): static
    {
        $this->document->setSendingMode($sendingMode);
        return $this;
    }
}
