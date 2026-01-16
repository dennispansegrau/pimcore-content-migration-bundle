<?php

namespace PimcoreContentMigration\Builder\DataObject;

use function array_key_exists;
use function is_string;

use LogicException;
use Pimcore\Model\Asset;
use Pimcore\Model\DataObject\Data\Video;

class VideoBuilder
{
    private ?Video $video = null;

    final protected function __construct()
    {
    }

    public static function create(): static
    {
        $builder = new static();
        $builder->video = new Video();
        return $builder;
    }

    public function getObject(): Video
    {
        if (!$this->video instanceof Video) {
            throw new LogicException('Video object has not been set');
        }
        return $this->video;
    }

    /**
     * @param array{
     *     type: string,
     *     data: int|string|Asset,
     *     poster: int|string|Asset,
     *     title: string,
     *     description: string
     * } $videoData
     * @return static
     */
    public function setData(array $videoData): static
    {
        if (array_key_exists('type', $videoData) && is_string($videoData['type'])) {
            $this->getObject()->setType($videoData['type']);
        }

        if (array_key_exists('data', $videoData) && !empty($videoData['data'])) {
            $this->getObject()->setData($videoData['data']);
        }

        if (array_key_exists('poster', $videoData) && !empty($videoData['poster'])) {
            $this->getObject()->setPoster($videoData['poster']);
        }

        if (array_key_exists('title', $videoData) && is_string($videoData['title'])) {
            $this->getObject()->setTitle($videoData['title']);
        }

        if (array_key_exists('description', $videoData) && is_string($videoData['description'])) {
            $this->getObject()->setDescription($videoData['description']);
        }

        return $this;
    }
}
