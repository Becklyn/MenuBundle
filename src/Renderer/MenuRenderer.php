<?php declare(strict_types=1);

namespace Becklyn\Menu\Renderer;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Visitor\ItemVisitor;
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
     * @param ItemVisitor[] $visitors
     */
    public function __construct (Environment $twig, iterable $visitors)
    {
        $this->twig = $twig;
        $this->visitors = $visitors;
    }


    /**
     * @param MenuItem $root
     */
    public function render (?MenuItem $root, array $options = []) : string
    {
        if (null === $root)
        {
            return "";
        }

        // don't modify the original
        $root = clone $root;

        // resolve options
        $template = $options["template"] ?? "@BecklynMenu/menu.html.twig";
        unset($options["template"]);

        $options = \array_replace([
            "translationDomain" => null,
            "currentClass" => "is-current",
            "ancestorClass" => "is-current-ancestor",
            "depth" => null,
            "key" => null,
            "rootClass" => null,
        ], $options);

        // set root class
        if (null !== $options["rootClass"])
        {
            $root->addChildListClass($options["rootClass"]);
        }

        // apply external visitors
        // must be applied before voters, as they can generate new nodes
        $visitors = $this->getSupportedVoters($options);

        if (!empty($visitors))
        {
            $this->applyVisitors($visitors, $root, $options);
        }

        // resolve the ancestors
        $root->resolveTree($options["currentClass"], $options["ancestorClass"]);

        return $this->twig->render($template, [
            "options" => $options,
            "root" => $root,
        ]);
    }


    /**
     *
     */
    private function getSupportedVoters (array $options) : array
    {
        $result = [];

        foreach ($this->visitors as $visitor)
        {
            if ($visitor->supports($options))
            {
                $result[] = $visitor;
            }
        }

        return $result;
    }


    /**
     * Applies the visitors to the item and all children.
     *
     * @param ItemVisitor[] $visitors
     */
    private function applyVisitors (array $visitors, MenuItem $item, array $options) : void
    {
        foreach ($visitors as $visitor)
        {
            $visitor->visit($item, $options);
        }

        foreach ($item->getChildren() as $child)
        {
            $this->applyVisitors($visitors, $child, $options);
        }
    }
}
