<?php

namespace PimcoreContentMigration\Generator;

use function get_class;

use InvalidArgumentException;

use function is_string;

use Pimcore\Model\Asset;
use PimcoreContentMigration\Builder\Asset\ArchiveBuilder;
use PimcoreContentMigration\Builder\Asset\AudioBuilder;
use PimcoreContentMigration\Builder\Asset\DocumentBuilder;
use PimcoreContentMigration\Builder\Asset\FolderBuilder;
use PimcoreContentMigration\Builder\Asset\ImageBuilder;
use PimcoreContentMigration\Builder\Asset\TextBuilder;
use PimcoreContentMigration\Builder\Asset\UnknownBuilder;
use PimcoreContentMigration\Builder\Asset\VideoBuilder;
use PimcoreContentMigration\Converter\AbstractElementToMethodNameConverter;
use PimcoreContentMigration\Generator\Dependency\DependencyCollector;
use PimcoreContentMigration\Writer\AssetWriter;

class AssetCodeGenerator implements CodeGeneratorInterface
{
    public function __construct(
        private readonly AssetWriter $assetWriter,
        private readonly CodeGenerator $codeGenerator,
        public DependencyCollector $dependencyCollector,
        private readonly AbstractElementToMethodNameConverter $methodNameConverter,
    ) {
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
            'classname' => '\\' . get_class($abstractElement),
            'settings' => $settings,
            'dependencies' => $this->dependencyCollector->getDependencies($settings, $abstractElement, $existingMethodNames),
            'dataPath' => $dataPath,
            'builder' => $this->getBuilderClass($abstractElement),
        ]);
    }

    private function getBuilderClass(Asset $asset): ?string
    {
        if ($asset instanceof Asset\Archive) {
            return '\\' . ArchiveBuilder::class ;
        }

        if ($asset instanceof Asset\Audio) {
            return '\\' . AudioBuilder::class ;
        }

        if ($asset instanceof Asset\Document) {
            return '\\' . DocumentBuilder::class ;
        }

        if ($asset instanceof Asset\Folder) {
            return '\\' . FolderBuilder::class ;
        }

        if ($asset instanceof Asset\Image) {
            return '\\' . ImageBuilder::class ;
        }

        if ($asset instanceof Asset\Text) {
            return '\\' . TextBuilder::class ;
        }

        if ($asset instanceof Asset\Unknown) {
            return '\\' . UnknownBuilder::class ;
        }

        if ($asset instanceof Asset\Video) {
            return '\\' . VideoBuilder::class ;
        }

        return null;
    }
}
