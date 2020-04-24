<?php
/**
 * 24.04.2020.
 */

declare(strict_types=1);

namespace srr\ServiceLoader\Service\PathFinder;

/**
 * Class PathFinder.
 *
 * @package srr\ServiceLoader\Service
 */
class PathFinder implements PathFinderInterface
{
    /**
     * @param array $basePaths
     * @return array
     */
    public function buildPaths(array $basePaths): array
    {
        $newTree = [];
        $this->createTree($basePaths, $newTree);

        return $this->preparePaths($newTree);
    }

    /**
     * @param array $newTree
     * @return array
     */
    protected function preparePaths(array $newTree): array
    {
        $newPath = [];
        foreach ($newTree as $item) {
            $path = \explode('.', $item);
            $path = \array_reverse($path);

            $this->createNewPath($path, $newPath);
        }

        return $newPath;
    }

    /**
     * @param $path
     * @param $node
     */
    protected function createNewPath(&$path, &$node)
    {
        if (\count($path) > 1) {
            $key = \array_shift($path);
            if (!\key_exists($key, $node)) {
                $node[$key] = [];
            }
            $this->createNewPath($path, $node[$key]);
        } else {
            $key = \array_shift($path);
            $node[] = $key;
        }
    }

    /**
     * @param $value
     * @param array $root
     * @param array $parentKeys
     * @return string
     */
    protected function createTree($value, array &$root, array $parentKeys = [])
    {
        if (\is_array($value)) {
            foreach ($value as $key => $item) {
                if (!\is_numeric($key)) {
                    $parentKeys[] = $key;
                }
                $result = $this->createTree($item, $root, $parentKeys);
                if ($result === null) {
                    \array_pop($parentKeys);
                } else {
                    $root[] = $result;
                }
            }
        } else {
            $result = '';
            foreach ($parentKeys as $item) {
                $result .= $item . '.';
            }

            return \sprintf('%s%s', $result, $value);
        }
    }
}
