<?php
/**
 * 07.02.2020.
 */

declare(strict_types=1);

namespace srr\ServiceLoader\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @package srr\PdfToHtmlBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /** @var string */
    public const CONFIGURATION_ROOT = 'service_loader';

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(static::CONFIGURATION_ROOT);

        return $treeBuilder;
    }
}
