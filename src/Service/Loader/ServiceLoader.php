<?php
/**
 * 17.02.2020.
 */

declare(strict_types=1);

namespace srr\ServiceLoader\Service\Loader;

use Psr\Log\LoggerInterface;
use srr\ServiceLoader\Exception\ServiceLoaderException;
use srr\ServiceLoader\Service\PathFinder\PathFinderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractServiceLoader.
 *
 * @package srr\ServiceLoader\Service
 */
class ServiceLoader implements ServiceLoaderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    protected $servicePath = [];

    /**
     * @var PathFinderInterface
     */
    private $pathFinder;

    /**
     * @var array
     */
    private $services;

    /**
     * AbstractServiceLoader constructor.
     *
     * @param ContainerInterface  $container
     * @param PathFinderInterface $pathFinder
     * @param LoggerInterface     $logger
     */
    public function __construct(
        ContainerInterface $container,
        PathFinderInterface $pathFinder,
        LoggerInterface $logger
    ) {
        $this->container = $container;
        $this->logger = $logger;
        $this->pathFinder = $pathFinder;
        $this->services = $this->getServicesConfig();

        $this->servicePath = $this->pathFinder->buildPaths($this->services);
    }

    /**
     * @return array
     */
    protected function getServicesConfig(): array
    {
        if (!$this->container->hasParameter('srr_service_loader')) {
            throw new ServiceLoaderException('For usage you need configure `srr_service_loader` container param');
        }
        return $this->container->getParameter('srr_service_loader');
    }

    protected function makePath(array $id): string
    {
        return  $this->makeFullPath($id, $this->servicePath);
    }

    /**
     * @param array  $keys
     * @param array  $node
     * @param string $path
     *
     * @return string
     */
    private function makeFullPath(array $keys, array $node, &$path = ''): string
    {
        if (empty($keys)) {
            if (\count($node) > 1) {
                $message = \sprintf('Too few input variables, may you add key: %s', \implode(', ', \array_keys($node)));
                $this->logger->warning($message, [
                    'request' => $node,
                ]);
                throw new ServiceLoaderException($message);
            } else {
                $subKey = \key($node);
                $element = \array_shift($node);
                if (\is_array($element)) {
                    $path .= $this->makeFullPath($keys, $element, $path) . '.' . $subKey;

                    return $path;
                } else {
                    return $element;
                }
            }
        }
        $key = \array_shift($keys);
        if (isset($node[$key])) {
            if (\count($node[$key]) > 1) {
                return  $this->makeFullPath($keys, $node[$key], $path) . '.' . $key;
            } else {
                $subKey = (\key($node[$key]));
                $element = \array_shift($node[$key]);
                if (\is_array($element)) {
                    return $this->makeFullPath($keys, $element, $path) . '.' . $subKey . '.' . $key;
                } else {
                    return $element . '.' . $key;
                }
            }
        }

        if (\in_array($key, $node)) {
            return $node[\array_search($key, $node)];
        } else {
            $message = \sprintf('Key `%s` not found in path', $key);
            $this->logger->warning($message, [
                'request' => $key,
            ]);
            throw new ServiceLoaderException($message);
        }
    }

    /**
     * get FQDN service's.
     *
     * @param array<string> $id
     *
     * @return string
     */
    public function getServiceId(array $id): string
    {
        return $this->getFQDNServices($id);
    }

    /**
     * @param array<string> $id
     *
     * @return string
     */
    protected function getFQDNServices(array $id): string
    {
        // In case of ID already with prefix.
        $providerId = $this->makePath($id);
        if (!$this->container->has($providerId)) {
            $this->logger->warning('Attempt to load not-existing service', [
                'request' => $providerId,
            ]);

            throw new ServiceLoaderException(sprintf('Attempt to load not-existing service %s', $providerId));
        }

        return $providerId;
    }

    /**
     * load instance of service.
     *
     * @param string        $interface
     * @param array<string> $id
     *
     * @return object
     */
    public function loadService(array $id, string $interface): object
    {
        return $this->getServiceInstance($id, $interface);
    }

    /**
     * @param array<string> $id
     * @param string        $interfaceName
     *
     * @return object
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    protected function getServiceInstance(array $id, string $interfaceName): object
    {
        // In case of ID already with prefix.
        $providerId = $this->makePath($id);

        if (!$this->container->has($providerId)) {
            $this->logger->warning('Attempt to load not-existing service', [
                'id' => $id,
                'request' => $providerId,
            ]);
            throw new ServiceLoaderException(sprintf('Attempt to load not-existing service'));
        }

        $service = $this->container->get($providerId);
        if ($service === null || !is_a($service, $interfaceName, false)) {
            $this->logger->error(sprintf('Loaded service must implements \'%s\' interface', $interfaceName));

            throw new ServiceLoaderException(sprintf('Loaded service must implements \'%s\' interface', $interfaceName));
        }

        return $service;
    }
}
