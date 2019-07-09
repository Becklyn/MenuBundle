<?php declare(strict_types=1);

namespace Becklyn\Menu\Renderer;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Visitor\ItemVisitor;
use Psr\Container\ContainerInterface;
use Twig\Environment;

class MenuRenderer
{
    /**
     * @var Environment
     */
    private $twig;


    /**
     * @var ItemVisitor[]|iterable
     */
    private $visitors;


    /**
     * @param ContainerInterface $locator
     * @param ItemVisitor[]      $visitors
     */
    public function __construct (Environment $twig, iterable $visitors)
    {
        $this->twig = $twig;
        $this->visitors = $visitors;
    }


    /**
     * @param MenuItem $root
     * @param array    $options
     *
     * @return string
     */
    public function render (?MenuItem $root, array $options = []) : string
    {
        if (null === $root)
        {
            return "";
        }

        // don't modify the original
        $root = clone $root;

        // apply external visitors
        // must be applied before voters, as they can generate new nodes
        if (!empty($this->visitors))
        {
            $this->applyVisitors($root);
        }

        // resolve options
        $template = $options["template"] ?? "@BecklynMenu/menu.html.twig";
        unset($options["template"]);

        $options = \array_replace([
            "translationDomain" => false,
            "currentClass" => "is-current",
            "ancestorClass" => "is-current-ancestor",
            "depth" => null,
        ], $options);

        // resolve the ancestors
        $root->resolveTree($options["currentClass"], $options["ancestorClass"]);

        return $this->twig->render($template, [
            "options" => $options,
            "root" => $root,
        ]);
    }


    /**
     * Applies the visitors to the item and all children.
     *
     * @param MenuItem $item
     */
    private function applyVisitors (MenuItem $item) : void
    {
        foreach ($this->visitors as $visitor)
        {
            $visitor->visit($item);
        }

        foreach ($item->getChildren() as $child)
        {
            $this->applyVisitors($child);
        }
    }
}
