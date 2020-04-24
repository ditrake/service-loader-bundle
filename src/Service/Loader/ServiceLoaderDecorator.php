<?php
/**
 * 22.04.2020.
 */

declare(strict_types=1);

namespace srr\ServiceLoader\Service\Loader;

/**
 * Class ServiceLoaderDecorator.
 *
 * @package srr\ServiceLoader\Service
 */
class ServiceLoaderDecorator implements ServiceLoaderInterface
{
    /**
     * @var ServiceLoaderInterface
     */
    protected $decorated;

    /**
     * ServiceLoaderDecorator constructor.
     * @param ServiceLoaderInterface $decorated
     */
    public function __construct(ServiceLoaderInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getServiceId(array $id): string
    {
        return $this->decorated->getServiceId($id);
    }

    public function loadService(array $id, string $interface): object
    {
        return $this->decorated->loadService($id, $interface);
    }
}
