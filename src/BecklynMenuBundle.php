<?php declare(strict_types=1);

namespace Becklyn\Menu;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BecklynMenuBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension ()
    {
        return new class() extends Extension {
            /**
             * @inheritdoc
             */
            public function load (array $configs, ContainerBuilder $container) : void
            {
                // load services
                $loader = new YamlFileLoader(
                    $container,
                    new FileLocator(__DIR__ . "/Resources/config")
                );
                $loader->load("services.yaml");
            }

            /**
             * @inheritDoc
             */
            public function getAlias ()
            {
                return "becklyn_menu";
            }
        };
    }
}
