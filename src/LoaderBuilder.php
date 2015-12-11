<?php

namespace Speedwork\Config;

use Speedwork\Config\Loader\CacheLoader;
use Speedwork\Config\Loader\EnvFileLoader;
use Speedwork\Config\Loader\IniFileLoader;
use Speedwork\Config\Loader\JsonFileLoader;
use Speedwork\Config\Loader\NormalizerLoader;
use Speedwork\Config\Loader\PhpFileLoader;
use Speedwork\Config\Loader\ProcessorLoader;
use Speedwork\Config\Loader\YamlFileLoader;
use Speedwork\Config\Normalizer\ChainNormalizer;
use Speedwork\Config\Normalizer\ContainerNormalizer;
use Speedwork\Config\Normalizer\EnvfileNormalizer;
use Speedwork\Config\Normalizer\EnvironmentNormalizer;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Finder\Finder;

/**
 * Helps building a Loader with Cache, Normalization etc.
 */
final class LoaderBuilder
{
    private $normalizer;
    private $resolver;
    private $resources;
    private $configuration;
    private $locator;
    private $debug;
    private $cacheDir;
    private $normalizerConfigured = false;
    private $resolverConfigured   = false;
    private $app;

    public function __construct(array $paths, $cacheDir = null, $debug = false)
    {
        $this->cacheDir   = $cacheDir;
        $this->debug      = $debug;
        $this->locator    = new FileLocator($paths);
        $this->normalizer = new ChainNormalizer();
        $this->resolver   = new LoaderResolver();
        $this->resources  = new ResourceCollection();
    }

    public static function create(array $paths, $cacheDir = null, $debug = false)
    {
        return new self($paths, $cacheDir, $debug);
    }

    public function configureLocator(callable $callable)
    {
        $callable($this->locator);

        return $this;
    }

    public function configureNormalizers(callable $callable)
    {
        $this->normalizerConfigured = true;

        $callable($this->normalizer);

        return $this;
    }

    public function configureLoaders(callable $callable)
    {
        $this->resolverConfigured = true;

        $callable($this->resolver, $this->locator, $this->resources);

        return $this;
    }

    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;

        return $this;
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    public function setContainer($app)
    {
        $this->app = $app;

        return $this;
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function build()
    {
        if (false == $this->resolverConfigured) {
            $this->addDefaultLoaders();
        }

        if (false == $this->normalizerConfigured) {
            $this->addDefaultNormalizers();
        }

        $loader = new CacheLoader($this->createLoaderGraph(), $this->resources);
        $loader->setCacheDir($this->cacheDir);
        $loader->setDebug($this->debug);

        return $loader;
    }

    public function addDefaultLoaders()
    {
        $this->resolverConfigured = true;

        if (class_exists('Symfony\Component\Yaml\Yaml')) {
            $this->resolver->addLoader(new YamlFileLoader($this->locator, $this->resources));
        }

        $this->resolver->addLoader(new EnvFileLoader($this->locator, $this->resources));
        $this->resolver->addLoader(new JsonFileLoader($this->locator, $this->resources));
        $this->resolver->addLoader(new PhpFileLoader($this->locator, $this->resources));
        $this->resolver->addLoader(new IniFileLoader($this->locator, $this->resources));

        return $this;
    }

    public function addDefaultNormalizers()
    {
        $this->normalizerConfigured = true;

        $this->normalizer->add(new ContainerNormalizer($this->app));
        $this->normalizer->add(new EnvironmentNormalizer());
        $this->normalizer->add(new EnvfileNormalizer($this->locator));

        return $this;
    }

    private function createLoaderGraph()
    {
        $loader = $this->createNormalizerLoader();

        if ($this->configuration) {
            return new ProcessorLoader($loader, $this->configuration);
        }

        return $loader;
    }

    private function createNormalizerLoader()
    {
        return new NormalizerLoader(new DelegatingLoader($this->resolver), $this->normalizer);
    }

    /**
     * Load the configuration items from all of the folders.
     *
     * @param array $config
     */
    public function load($paths = [], $associate = null)
    {
        $loader = $this->build();

        if (is_string($paths)) {
            $paths = [$paths];
        }

        foreach ($paths as $path) {
            if (is_dir($path)) {
                foreach (Finder::create()->files()->name('/\.(php|yml|json|ini)$/')->in($path) as $file) {
                    $this->app['config']->set($loader->load($file->getRealPath(), $associate));
                }
            } else {
                $this->app['config']->set($loader->load($path, $associate));
            }
        }

        return $this;
    }
}
