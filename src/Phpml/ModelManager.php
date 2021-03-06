<?php

declare(strict_types=1);

namespace Phpml;

use Phpml\Exception\FileException;
use Phpml\Exception\SerializeException;

class ModelManager
{
    public function saveToFile(Estimator $estimator, string $filepath)
    {
        if (!is_writable(dirname($filepath))) {
            throw FileException::cantSaveFile(basename($filepath));
        }

        $serialized = serialize($estimator);
        if (empty($serialized)) {
            throw SerializeException::cantSerialize(gettype($estimator));
        }

        $result = file_put_contents($filepath, $serialized, LOCK_EX);
        if ($result === false) {
            throw FileException::cantSaveFile(basename($filepath));
        }
    }

    public function restoreFromFile(string $filepath) : Estimator
    {
        if (!file_exists($filepath) || !is_readable($filepath)) {
            throw FileException::cantOpenFile(basename($filepath));
        }

        $object = unserialize(file_get_contents($filepath));
        if ($object === false) {
            throw SerializeException::cantUnserialize(basename($filepath));
        }

        return $object;
    }
}
