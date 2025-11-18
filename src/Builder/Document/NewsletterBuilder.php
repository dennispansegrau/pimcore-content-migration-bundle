<?php

namespace PimcoreContentMigration\Builder\Document;

use LogicException;
use Pimcore\Bundle\NewsletterBundle\Model\Document\Newsletter;

class NewsletterBuilder extends EmailBuilder
{
    protected static function getDocumentClass(): string
    {
        return Newsletter::class;
    }

    public function setPlaintext(string $plaintext): static
    {
        $this->getObject()->setPlaintext($plaintext);
        return $this;
    }

    public function setEnableTrackingParameters(bool $enableTrackingParameters): static
    {
        $this->getObject()->setEnableTrackingParameters($enableTrackingParameters);
        return $this;
    }

    public function setTrackingParameterSource(string $trackingParameterSource): static
    {
        $this->getObject()->setTrackingParameterSource($trackingParameterSource);
        return $this;
    }

    public function setTrackingParameterMedium(string $trackingParameterMedium): static
    {
        $this->getObject()->setTrackingParameterMedium($trackingParameterMedium);
        return $this;
    }

    public function setTrackingParameterName(string $trackingParameterName): static
    {
        $this->getObject()->setTrackingParameterName($trackingParameterName);
        return $this;
    }

    public function setSendingMode(string $sendingMode): static
    {
        $this->getObject()->setSendingMode($sendingMode);
        return $this;
    }

    public function getObject(): Newsletter
    {
        if (!$this->document instanceof Newsletter) {
            throw new LogicException('Newsletter object has not been set');
        }
        return $this->document;
    }
}
