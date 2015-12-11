<?php

namespace Speedwork\Config\Loader;

use Speedwork\Config\ResourceCollection;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;

abstract class AbstractLoader extends FileLoader
{
    protected $locator;
    private $resources;

    public function __construct(
        FileLocatorInterface $locator,
        ResourceCollection $resources
    ) {
        $this->locator   = $locator;
        $this->resources = $resources;
    }

    public function load($resource, $associate = null)
    {
        $resource = $this->locator->locate($resource);
        $this->resources->add(new FileResource($resource));

        if ($associate) {
            if (is_string($associate)) {
                $filename = $associate;
            } else {
                $ext      = strrchr($resource, '.');
                $filename = basename($resource, $ext);
            }

            return [
                $filename => $this->parse($this->read($resource), $resource),
            ];
        }

        return $this->parse($this->read($resource), $resource);
    }

    abstract protected function read($resource);

    private function parse(array $parameters, $resource)
    {
        if (!isset($parameters['@import'])) {
            return $parameters;
        }

        $imports   = (array) $parameters['@import'];
        $inherited = [];

        unset($parameters['@import']);

        foreach ($imports as $import) {
            $inherited = $this->parseImport($import, $resource, $inherited);
        }

        return array_replace($inherited, $parameters);
    }

    private function parseImport($import, $resource, $inherited)
    {
        $this->setCurrentDir(dirname($import));

        return array_replace($inherited, $this->import($import, null, false, $resource));
    }
}
