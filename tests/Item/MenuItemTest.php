<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item;

use Becklyn\Menu\Item\MenuItem;
use Becklyn\Menu\Sorter\MenuItemSorter;
use Becklyn\Menu\Target\LazyRoute;
use PHPUnit\Framework\TestCase;

class MenuItemTest extends TestCase
{


    /**
     *
     */
    public function testConstructorOptions () : void
    {
        $listItemAttributes = ["list" => "listItemAttributes"];
        $linkAttributes = ["list" => "linkAttributes"];
        $childListAttributes = ["list" => "childListAttributes"];
        $extras = ["list" => "extras"];

        $item = new MenuItem("item", [
            "priority" => 5,
            "listItemAttributes" => $listItemAttributes,
            "linkAttributes" => $linkAttributes,
            "childListAttributes" => $childListAttributes,
            "target" => "abc",
            "visible" => false,
            "current" => true,
            "extras" => $extras,
            "key" => "key",
            "security" => "security",
            "sort" => true,
        ]);

        self::assertSame("item", $item->getLabel());
        self::assertSame(5, $item->getPriority());
        self::assertSame($listItemAttributes, $item->getListItemAttributes());
        self::assertSame($linkAttributes, $item->getLinkAttributes());
        self::assertSame($childListAttributes, $item->getChildListAttributes());
        self::assertSame("abc", $item->getTarget());
        self::assertSame(false, $item->isVisible());
        self::assertSame(true, $item->isCurrent());
        self::assertSame($extras, $item->getExtras());
        self::assertSame("key", $item->getKey());
        self::assertSame("security", $item->getSecurity());
        self::assertSame(true, $item->getSort());
    }


    /**
     *
     */
    public function testAncestors () : void
    {
        $parent = new MenuItem("parent", ["current" => true]);
        $child = $parent->createChild("child", ["current" => true]);
        $grandchild = $child->createChild("grandchild");

        $parent->resolveTree();

        self::assertStringContainsString("ancestor", $parent->getListItemAttributes()["class"]);
        self::assertStringContainsString("current", $parent->getListItemAttributes()["class"]);

        self::assertStringNotContainsString("ancestor", $child->getListItemAttributes()["class"]);
        self::assertStringContainsString("current", $child->getListItemAttributes()["class"]);

        self::assertStringNotContainsString("ancestor", $grandchild->getListItemAttributes()["class"]);
        self::assertStringNotContainsString("current", $grandchild->getListItemAttributes()["class"]);
    }


    /**
     *
     */
    public function testLevel () : void
    {
        $parent = new MenuItem("parent");
        $child = $parent->createChild("child");
        $grandchild = $child->createChild("grandchild");
        $youngest = $grandchild->createChild("grandchild");

        self::assertSame(0, $parent->getLevel());
        self::assertSame(1, $child->getLevel());
        self::assertSame(2, $grandchild->getLevel());
        self::assertSame(3, $youngest->getLevel());

        // remove grandchild from the tree and make it a new root
        $grandchild->setParent(null);

        // these should be unchanged
        self::assertSame(0, $parent->getLevel());
        self::assertSame(1, $child->getLevel());
        // these should start from 0 again
        self::assertSame(0, $grandchild->getLevel());
        self::assertSame(1, $youngest->getLevel());
    }


    /**
     * @param array $config
     * @param       $expected
     */
    public function testTarget () : void
    {
        $url = new MenuItem(null, ["target" => "abc"]);
        self::assertSame("abc", $url->getTarget());


        $routeWithout = new MenuItem(null, ["route" => "route"]);
        self::assertInstanceOf(LazyRoute::class, $routeWithout->getTarget());
        self::assertSame("route", $routeWithout->getTarget()->getRoute());
        self::assertSame([], $routeWithout->getTarget()->getParameters());

        $routeWith = new MenuItem(null, ["route" => "route2", "routeParameters" => ["test" => 123]]);
        self::assertInstanceOf(LazyRoute::class, $routeWith->getTarget());
        self::assertSame("route2", $routeWith->getTarget()->getRoute());
        self::assertSame(["test" => 123], $routeWith->getTarget()->getParameters());
    }


    /**
     *
     */
    public function testFindSimple () : void
    {
        $parent = new MenuItem();
        $parent->createChild("test 1");
        $toFind = $parent->createChild("test 2", ["key" => "ohai"]);
        $parent->createChild("test 3");

        self::assertSame($toFind, $parent->find("ohai"));
        self::assertSame($toFind, $toFind->find("ohai"));
    }


    /**
     * Tests depth first search for `find()`.
     */
    public function testFindAmbiguous () : void
    {
        $parent = new MenuItem();
        $child = $parent->createChild("test 1");
        $toFind = $child->createChild("should find", ["key" => "ohai"]);
        $parent->createChild("should not find", ["key" => "ohai"]);

        self::assertSame($toFind, $parent->find("ohai"));
    }


    /**
     * Tests depth first search for `find()`.
     */
    public function testFindNoMatch () : void
    {
        $parent = new MenuItem();
        $parent
            ->createChild("test 1")
            ->createChild("test 2");
        $parent->createChild("test 3", ["key" => "ohai"]);

        self::assertNull($parent->find("missing"));
    }


    /**
     *
     */
    public function testVisibilityExplicitly () : void
    {
        $item = new MenuItem("test", [
            "visible" => false,
        ]);

        self::assertFalse($item->isVisible());
        $item->setLabel(null);
        self::assertFalse($item->isVisible());
        $item->setLabel("not empty");
        self::assertFalse($item->isVisible());
    }


    /**
     *
     */
    public function testVisibilityImplicitly () : void
    {
        $item = new MenuItem("test");

        self::assertTrue($item->isVisible());
        $item->setLabel(null);
        self::assertFalse($item->isVisible());
        $item->setLabel("not empty");
        self::assertTrue($item->isVisible());
    }


    /**
     *
     */
    public function testStablePrioritySort () : void
    {
        $root = new MenuItem();
        $expectedLabels = [];
        for ($i = 0; $i < 20; $i++)
        {
            $label = (string) $i;

            $expectedLabels[] = $label;
            $root->createChild($label, ["priority" => 0]);
        }

        $root->resolveTree();

        $actualLabels = \array_map(function (MenuItem $item) { return $item->getLabel(); }, $root->getChildren());
        self::assertSame($expectedLabels, $actualLabels);
    }


    /**
     *
     */
    public function testCloneChildrenReferences () : void
    {
        $root = new MenuItem();
        $root->createChild("test");

        self::assertSame($root, $root->getChildren()[0]->getParent());

        // clone should update the children and leave the old ones intact
        $clone = clone $root;
        self::assertSame($clone, $clone->getChildren()[0]->getParent());
        self::assertSame($root, $root->getChildren()[0]->getParent());
    }


    /**
     *
     */
    public function testCloneParentReferences () : void
    {
        $root = new MenuItem();
        $child = $root->createChild("test");

        self::assertSame($child, $root->getChildren()[0]);
        self::assertNotNull($child->getParent());

        // clone should update the children and leave the old ones intact
        $clone = clone $child;
        self::assertNotSame($clone, $child);
        // the old relation stays intact
        self::assertSame($child, $root->getChildren()[0]);
        self::assertNull($clone->getParent());
    }


    /**
     *
     */
    public function testWithSort () : void
    {
        $root = new MenuItem(null, ["sort" => true]);
        $root->createChild("y");
        $root->createChild("z");
        $root->createChild("x");
        $root->resolveTree();

        $labels = \array_map(function (MenuItem $item) { return $item->getLabel(); }, $root->getChildren());
        self::assertSame(["x", "y", "z"], $labels);
    }


    /**
     *
     */
    public function testWithoutSort () : void
    {
        $root = new MenuItem(null);
        $root->createChild("y");
        $root->createChild("z");
        $root->createChild("x");
        $root->resolveTree();

        $labels = \array_map(function (MenuItem $item) { return $item->getLabel(); }, $root->getChildren());
        self::assertSame(["y", "z", "x"], $labels);
    }


    /**
     * Tests that the resolving of the ancestor works correctly
     */
    public function testCurrentAncestor ()
    {
        $root = new MenuItem();
        $child1 = $root->createChild("label1");
        $child2 = $root->createChild("label2");

        $child1_1 = $child1->createChild("label1.2");
        $child1_1->setCurrent(true);

        $root->resolveTree();

        self::assertTrue($child1_1->isCurrent());
        self::assertFalse($child1_1->isCurrentAncestor());

        self::assertFalse($child1->isCurrent());
        self::assertTrue($child1->isCurrentAncestor());

        self::assertFalse($child2->isCurrent());
        self::assertFalse($child2->isCurrentAncestor());

        self::assertFalse($root->isCurrent());
        self::assertTrue($root->isCurrentAncestor());
    }


    /**
     * Tests the `isAnyCurrent()` variations
     */
    public function testAnyCurrent () : void
    {
        $root = new MenuItem();
        $child = $root->createChild("child");

        $child->setCurrent(true);
        $root->resolveTree();

        self::assertTrue($child->isCurrent());
        self::assertFalse($child->isCurrentAncestor());
        self::assertTrue($child->isAnyCurrent());

        self::assertFalse($root->isCurrent());
        self::assertTrue($root->isCurrentAncestor());
        self::assertTrue($root->isAnyCurrent());


        $separate = new MenuItem();
        self::assertFalse($separate->isCurrent());
        self::assertFalse($separate->isCurrentAncestor());
        self::assertFalse($separate->isAnyCurrent());

        $both = new MenuItem();
        $both->createChild("child")->setCurrent(true);
        $both->setCurrent(true);
        $both->resolveTree();
        self::assertTrue($both->isCurrent());
        self::assertTrue($both->isCurrentAncestor());
        self::assertTrue($both->isAnyCurrent());
    }
}
