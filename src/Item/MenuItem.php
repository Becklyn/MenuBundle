<?php declare(strict_types=1);

namespace Becklyn\Menu\Item;

use Becklyn\Menu\Exception\InvalidTargetException;
use Becklyn\Menu\Sorter\MenuItemSorter;
use Becklyn\Menu\Target\LazyRoute;

class MenuItem
{
    //region Fields
    /**
     * The label to display.
     * Will be translated using the translation domain given in the renderer.
     *
     * @var string|null
     */
    private $label;


    /**
     * A key to find this item in the hierarchy.
     *
     * @var mixed
     */
    private $key;


    /**
     * The parent menu item.
     *
     * @var MenuItem|null
     */
    private $parent;


    /**
     * The priority of this menu item. Menu items will be ordered by descending priority.
     *
     * @var int
     */
    private $priority = 0;


    /**
     * The attributes of the list item.
     *
     * @var array
     */
    private $listItemAttributes = [];


    /**
     * @var string[]
     */
    private $listItemClasses = [];


    /**
     * The attributes of the link / the label.
     *
     * @var array
     */
    private $linkAttributes = [];


    /**
     * @var string[]
     */
    private $linkClasses = [];


    /**
     * The attributes of the list of children.
     *
     * @var array
     */
    private $childListAttributes = [];


    /**
     * @var string[]
     */
    private $childListClasses = [];


    /**
     * The target of this item.
     *
     * RouteTarget  -> route
     * string       -> direct URI
     * null         -> no link
     *
     * @var LazyRoute|string|null
     */
    private $target;


    /**
     * @var string|null
     */
    private $security;


    /**
     * The extra attributes on the menu item.
     *
     * @var array
     */
    private $extras = [];


    /**
     * Whether the item is displayed.
     *
     * @var bool
     */
    private $visible = true;


    /**
     * Whether the item is the currently selected menu item.
     *
     * @var bool
     */
    private $current = false;


    /**
     * The children of the menu item.
     *
     * @var MenuItem[]
     */
    private $children = [];
    //endregion


    /**
     * @param string|null $label
     * @param array       $options
     */
    public function __construct (?string $label = null, array $options = [])
    {
        $this->label = $label;

        if (isset($options["priority"]))
        {
            $this->setPriority($options["priority"]);
        }

        if (isset($options["listItemAttributes"]))
        {
            $this->setListItemAttributes($options["listItemAttributes"]);
        }

        if (isset($options["linkAttributes"]))
        {
            $this->setLinkAttributes($options["linkAttributes"]);
        }

        if (isset($options["childListAttributes"]))
        {
            $this->setChildListAttributes($options["childListAttributes"]);
        }

        if (isset($options["target"]))
        {
            $this->setTarget($options["target"]);
        }
        elseif (isset($options["route"]))
        {
            $this->setTarget(new LazyRoute($options["route"], $options["routeParameters"] ?? []));
        }
        elseif (isset($options["uri"]))
        {
            $this->setTarget($options["uri"]);
        }

        if (isset($options["visible"]))
        {
            $this->setVisible($options["visible"]);
        }

        if (isset($options["current"]))
        {
            $this->setCurrent($options["current"]);
        }

        if (isset($options["extras"]))
        {
            $this->setExtras($options["extras"]);
        }

        if (isset($options["key"]))
        {
            $this->setKey($options["key"]);
        }

        if (isset($options["security"]))
        {
            $this->setSecurity($options["security"]);
        }
    }


    //region Accessors
    //region $this->label
    /**
     * @return string|null
     */
    public function getLabel () : ?string
    {
        return $this->label;
    }


    /**
     * @param string|null $label
     *
     * @return MenuItem
     */
    public function setLabel (?string $label) : self
    {
        $this->label = $label;
        return $this;
    }
    //endregion


    //region $this->parent
    /**
     * @return MenuItem|null
     */
    public function getParent () : ?self
    {
        return $this->parent;
    }


    /**
     * @param MenuItem|null $parent
     *
     * @return MenuItem
     */
    public function setParent (?self $parent) : self
    {
        // remove child from previous parent
        if (null !== $this->parent)
        {
            $this->parent->removeChild($this);
        }

        // update parent
        $this->parent = $parent;

        // add to parent children, if parent is not null
        if (null !== $parent)
        {
            $parent->addChild($this);
        }

        return $this;
    }
    //endregion



    //region $this->priority
    /**
     * @return int
     */
    public function getPriority () : int
    {
        return $this->priority;
    }


    /**
     * @param int $priority
     */
    public function setPriority (int $priority) : void
    {
        $this->priority = $priority;
    }
    //endregion


    //region $this->listItemAttributes
    /**
     * @return array
     */
    public function getListItemAttributes () : array
    {
        $attributes = $this->listItemAttributes;

        if (!empty($this->listItemClasses))
        {
            $attributes["class"] = \trim(($attributes["class"] ?? "") . " " . \implode(" ", $this->listItemClasses));
        }

        return $attributes;
    }


    /**
     * @param array $listItemAttributes
     */
    public function setListItemAttributes (array $listItemAttributes) : self
    {
        $this->listItemAttributes = $listItemAttributes;
        return $this;
    }


    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
    public function setListItemAttribute (string $name, $value) : self
    {
        $this->listItemAttributes[$name] = $value;
        return $this;
    }


    /**
     * Convenience setter to set a child list class.
     *
     * @param string $className
     *
     * @return MenuItem
     */
    public function addListItemClass (string $className) : self
    {
        $this->listItemClasses[] = $className;
        return $this;
    }
    //endregion


    //region $this->linkAttributes
    /**
     * @return array
     */
    public function getLinkAttributes () : array
    {
        $attributes = $this->linkAttributes;

        if (!empty($this->linkClasses))
        {
            $attributes["class"] = \trim(($attributes["class"] ?? "") . " " . \implode(" ", $this->linkClasses));
        }

        return $attributes;
    }


    /**
     * @param array $linkAttributes
     */
    public function setLinkAttributes (array $linkAttributes) : self
    {
        $this->linkAttributes = $linkAttributes;
        return $this;
    }


    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
    public function setLinkAttribute (string $name, $value) : self
    {
        $this->linkAttributes[$name] = $value;
        return $this;
    }


    /**
     * Convenience setter to set a link class.
     *
     * @param string $className
     *
     * @return MenuItem
     */
    public function addLinkClass (string $className) : self
    {
        $this->linkClasses[] = $className;
        return $this;
    }
    //endregion


    //region $this->childListAttributes
    /**
     * @return array
     */
    public function getChildListAttributes () : array
    {
        $attributes = $this->childListAttributes;

        if (!empty($this->childListClasses))
        {
            $attributes["class"] = \trim(($attributes["class"] ?? "") . " " . \implode(" ", $this->childListClasses));
        }

        return $attributes;
    }


    /**
     * @param array $childListAttributes
     */
    public function setChildListAttributes (array $childListAttributes) : self
    {
        $this->childListAttributes = $childListAttributes;
        return $this;
    }


    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
    public function setChildListAttribute (string $name, $value) : self
    {
        $this->childListAttributes[$name] = $value;
        return $this;
    }


    /**
     * Convenience setter to set a child list class.
     *
     * @param string $className
     *
     * @return MenuItem
     */
    public function addChildListClass (string $className) : self
    {
        $this->childListClasses[] = $className;
        return $this;
    }
    //endregion


    //region $this->target
    /**
     * @param LazyRoute|string|mixed|null $target
     *
     * @return MenuItem
     */
    public function setTarget ($target) : self
    {
        if ($target instanceof LazyRoute || \is_string($target) || null === $target)
        {
            $this->target = $target;
            return $this;
        }

        throw new InvalidTargetException($target);
    }


    /**
     * @return LazyRoute|string|null
     */
    public function getTarget ()
    {
        return $this->target;
    }
    //endregion


    //region $this->extras
    /**
     * @return array
     */
    public function getExtras () : array
    {
        return $this->extras;
    }


    /**
     * @param array $extras
     */
    public function setExtras (array $extras) : void
    {
        $this->extras = $extras;
    }


    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return MenuItem
     */
    public function setExtra (string $name, $value) : self
    {
        $this->extras[$name] = $value;
        return $this;
    }


    /**
     * @param string $name
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getExtra (string $name, $defaultValue = null)
    {
        return $this->extras[$name] ?? $defaultValue;
    }
    //endregion


    //region $this->visible
    /**
     * @return bool
     */
    public function isVisible () : bool
    {
        return $this->visible && null !== $this->label;
    }


    /**
     * @param bool $visible
     *
     * @return MenuItem
     */
    public function setVisible (bool $visible) : self
    {
        $this->visible = $visible;
        return $this;
    }
    //endregion


    //region $this->current
    /**
     * @return bool
     */
    public function isCurrent () : bool
    {
        return $this->current;
    }


    /**
     * @param bool $current
     *
     * @return MenuItem
     */
    public function setCurrent (bool $current) : self
    {
        $this->current = $current;
        return $this;
    }
    //endregion


    //region $this->children
    /**
     * @param string $name
     * @param array  $options
     */
    public function createChild (string $name, array $options = []) : self
    {
        $child = new self($name, $options);
        $child->parent = $this;
        $this->children[] = $child;

        return $child;
    }


    /**
     * @return MenuItem[]
     */
    public function getChildren () : array
    {
        return $this->children;
    }


    /**
     * @return MenuItem
     */
    public function clearChildren () : self
    {
        $this->children = [];
        return $this;
    }


    /**
     * @param MenuItem $child
     *
     * @return MenuItem
     */
    public function addChild (self $child) : self
    {
        if (null !== $child->parent)
        {
            $child->parent->removeChild($child);
        }

        $child->parent = $this;
        $this->children[] = $child;
        return $this;
    }


    /**
     * @param MenuItem $child
     *
     * @return MenuItem
     */
    public function removeChild (self $child) : self
    {
        $index = \array_search($child, $this->children, true);

        if (false !== $index)
        {
            \array_splice($this->children, $index, 1, null);
        }

        return $this;
    }
    //endregion


    //region $this->key
    /**
     * @return mixed
     */
    public function getKey ()
    {
        return $this->key;
    }


    /**
     * @param mixed $key
     *
     * @return MenuItem
     */
    public function setKey ($key) : self
    {
        $this->key = $key;
        return $this;
    }
    //endregion


    //region $this->security
    /**
     * @return string|null
     */
    public function getSecurity () : ?string
    {
        return $this->security;
    }


    /**
     * @param string|null $security
     */
    public function setSecurity (?string $security) : void
    {
        $this->security = $security;
    }
    //endregion
    //endregion


    /**
     * @return int
     */
    public function getLevel () : int
    {
        return null !== $this->parent
            ? $this->parent->getLevel() + 1
            : 0;
    }


    /**
     * Resolves the ancestor state for this item and all sub items.
     *
     * @param string $currentClass
     * @param string $ancestorClass
     * @param int    $level
     *
     * @return bool
     *
     * @internal should not be called externally
     */
    public function resolveTree (string $currentClass = "current", string $ancestorClass = "ancestor", int $level = 0) : bool
    {
        $isCurrentAncestor = false;

        // resolve all children
        foreach ($this->children as $child)
        {
            $subTreeCurrent = $child->resolveTree($currentClass, $ancestorClass, $level + 1);

            if ($subTreeCurrent)
            {
                $isCurrentAncestor = true;
            }
        }

        $this
            ->addListItemClass("menu-item")
            ->addChildListClass("menu-list")
            ->addChildListClass("menu-level-{$level}");

        if ($this->current)
        {
            $this->addListItemClass($currentClass);
        }

        if ($isCurrentAncestor)
        {
            $this->addListItemClass($ancestorClass);
        }

        // sort children
        $this->children = MenuItemSorter::sort($this->children);

        return $this->current || $isCurrentAncestor;
    }


    /**
     *
     */
    public function __clone ()
    {
        // Remove the parent link when cloning, as it wouldn't even be in the list of children.
        // If the user wants to add it to the same parent, they can do it themselves.
        // This has the added bonus that if used with `find()` and rendering, that it will reset
        // the level calculation on this node.
        $this->parent = null;

        // Explicitly deep clone children.
        $oldChildren = $this->children;
        $this->children = [];

        foreach ($oldChildren as $child)
        {
            $this->addChild(clone $child);
        }
    }


    /**
     * @return MenuItem[]
     */
    public function getVisibleChildren () : array
    {
        $result = [];

        foreach ($this->children as $child)
        {
            if ($child->isVisible())
            {
                $result[] = $child;
            }
        }

        return $result;
    }


    /**
     * Finds a node inside the tree.
     *
     * @param mixed $key
     *
     * @return MenuItem|null
     */
    public function find ($key) : ?self
    {
        if (null !== $this->key && $this->key === $key)
        {
            return $this;
        }

        foreach ($this->children as $child)
        {
            $result = $child->find($key);

            if (null !== $result)
            {
                return $result;
            }
        }

        return null;
    }


    /**
     * Returns the hierarchy, whereas the first item is the root and the last item is the current one.
     *
     * @return MenuItem[]
     */
    public function getHierarchy () : array
    {
        $hierarchy = [$this];
        $pointer = $this->parent;

        while (null !== $pointer)
        {
            $hierarchy[] = $pointer;
            $pointer = $pointer->parent;
        }

        return \array_reverse($hierarchy);
    }
}
