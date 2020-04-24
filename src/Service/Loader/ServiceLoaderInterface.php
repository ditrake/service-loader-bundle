<?php
/**
 * 22.04.2020.
 */

namespace srr\ServiceLoader\Service\Loader;

/**
 * Interface ServiceLoaderInterface.
 *
 * @package srr\ServiceLoader\Service
 */
interface ServiceLoaderInterface
{
    public function getServiceId(array $id): string;

    public function loadService(array $id, string $interface): object;
}
