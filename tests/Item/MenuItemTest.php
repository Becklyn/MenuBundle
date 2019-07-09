<?php declare(strict_types=1);

namespace Tests\Becklyn\Menu\Item;

use Becklyn\Menu\Item\MenuItem;
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

        self::assertContains("ancestor", $parent->getListItemAttributes()["class"]);
        self::assertContains("current", $parent->getListItemAttributes()["class"]);

        self::assertNotContains("ancestor", $child->getListItemAttributes()["class"]);
        self::assertContains("current", $child->getListItemAttributes()["class"]);

        self::assertNotContains("ancestor", $grandchild->getListItemAttributes()["class"]);
        self::assertNotContains("current", $grandchild->getListItemAttributes()["class"]);
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
    public function testPriority () : void
    {
        $parent = new MenuItem();
        $parent->createChild("10", ["priority" => 10]);
        $parent->createChild("100", ["priority" => 100]);
        $parent->createChild("-10", ["priority" => -10]);
        $parent->createChild("50", ["priority" => 50]);
        $parent->createChild("none");

        $parent->resolveTree();

        $children = $parent->getChildren();
        self::assertSame("100", $children[0]->getLabel());
        self::assertSame("50", $children[1]->getLabel());
        self::assertSame("10", $children[2]->getLabel());
        self::assertSame("none", $children[3]->getLabel());
        self::assertSame("-10", $children[4]->getLabel());
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
}
