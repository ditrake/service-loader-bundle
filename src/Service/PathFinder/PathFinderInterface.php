<?php
/**
 * 24.04.2020.
 */

namespace srr\ServiceLoader\Service\PathFinder;

/**
 * Interface PathFinderInterface.
 *
 * @package srr\ServiceLoader\Service
 */
interface PathFinderInterface
{
    /**
     * @param array $basePaths
     *
     * @return array
     */
    public function buildPaths(array $basePaths): array;
}
