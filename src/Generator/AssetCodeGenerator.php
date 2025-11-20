<?php

namespace PimcoreContentMigration\Generator;

use InvalidArgumentException;

use function is_string;

use Pimcore\Model\Asset;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Loader\ObjectLoaderInterface;
use PimcoreContentMigration\Writer\AssetWriter;

class AssetCodeGenerator extends AbstractElementCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private readonly AssetWriter $assetWriter,
        private readonly CodeGenerator $codeGenerator,
        AbstractElementToMethodNameConverter $methodNameConverter,
        ObjectLoaderInterface $objectLoader
    ) {
        parent::__construct(
            $methodNameConverter,
            $objectLoader
        );
    }

    /**
     * @param object $abstractElement
     * @param Settings $settings
     * @param string[] $existingMethodNames
     * @return string
     */
    public function generateCode(object $abstractElement, Settings $settings, array &$existingMethodNames = []): string
    {
        if (!$abstractElement instanceof Asset) {
            throw new InvalidArgumentException();
        }

        $methodName = $this->methodNameConverter->convert($abstractElement);
        if (empty($existingMethodNames)) {
            $existingMethodNames[] = $methodName;
        }

        $data = $abstractElement->getData();
        $dataPath = null;
        if (is_string($data) && !empty($data)) {
            $dataPath = $this->assetWriter->write($abstractElement, $settings->getNamespace(), $abstractElement->getFilename() ?? '', $data);
        }

        return $this->codeGenerator->generate('asset_template', [
            'asset' => $abstractElement,
            'type' => $abstractElement->getType(),
            'methodName' => $methodName,
            'settings' => $settings,
            'dependencies' => $this->getDependencies($settings, $abstractElement, $existingMethodNames),
            'dataPath' => $dataPath,
        ]);
    }
}
