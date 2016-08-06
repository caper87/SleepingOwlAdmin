<?php

namespace SleepingOwl\Admin;

use BadMethodCallException;
use Illuminate\Contracts\Container\Container;
use SleepingOwl\Admin\Contracts\AssetsInterface;
use SleepingOwl\Admin\Structures\AssetPackage;

class AliasBinder
{
    /**
     * @var array
     */
    protected static $routes = [];

    /**
     * @return array
     */
    public static function routes()
    {
        return self::$routes;
    }

    /**
     * @var array
     */
    protected $aliases = [];

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var PackageManager
     */
    protected $packageManager;

    /**
     * AliasBinder constructor.
     * @param Container $container
     * @param PackageManager $packageManager
     */
    public function __construct(Container $container, PackageManager $packageManager)
    {
        $this->container = $container;
        $this->packageManager = $packageManager;
    }

    /**
     * Register new alias.
     *
     * @param string $alias
     * @param string $class
     *
     * @return $this
     */
    public function add($alias, $class)
    {
        $this->aliases[$alias] = $class;

        if (method_exists($class, 'registerRoutes')) {
            self::$routes[] = [$class, 'registerRoutes'];
        }

        return $this;
    }

    /**
     * @param array $classes
     *
     * @return $this
     */
    public function register(array $classes)
    {
        foreach ($classes as $key => $class) {
            $this->add($key, $class);
        }

        return $this;
    }

    /**
     * Get class by alias.
     *
     * @param string $alias
     *
     * @return string
     */
    public function getAlias($alias)
    {
        return $this->aliases[$alias];
    }

    /**
     * Check if alias is registered.
     *
     * @param string $alias
     *
     * @return bool
     */
    public function hasAlias($alias)
    {
        return array_key_exists($alias, $this->aliases);
    }

    /**
     * Create new instance by alias.
     *
     * @param string $name
     * @param        $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (! $this->hasAlias($name)) {
            throw new BadMethodCallException($name);
        }

        $this->container->when($this->getAlias($name))
            ->needs(AssetPackage::class)
            ->give(function (Container $app) use ($name) {
                if ($app->resolved($this->getAlias($name))) {
                    return $app->make('sleeping_owl.package.' . $this->getAlias($name));
                }

                return new AssetPackage($this->getAlias($name));
            });

        $object = $this->container->make($this->getAlias($name), $arguments);

        if ($object instanceof AssetsInterface) {
            $this->container->singleton('sleeping_owl.package.' . $this->getAlias($name), function () use ($object) {
                return $object->loadPackage();
            });
            $this->packageManager->add($object);
        }

        return $object;
    }
}
