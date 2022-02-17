3.1.0
=====

*   (feature) Add support for PHP 8.
*   (internal) Fix Symfony deprecations.
*   (internal) Replace TravisCI with GitHub Actions.
*   (improvement) Add missing property types and return types.
*   (internal) Remove support for Symfony 4.4.
*   (improvement) Add support for Symfony 6.


3.0.1
=====

*   (improvement) Added `LazyRoute::withParameters()` to simplify adding parameters.


3.0.0
=====

*   (bc) Disable all predefined route voters by default. See `UPGRADE` for details.
*   (improvement) Added ability for `SimpleRouteVoter` to also compare route parameters.
*   (improvement) Also store the route parameters for lazy routes in the `_route_params` extra.


2.1.5
=====

*   (improvement) Add `MenuItem::hasChildren()` convenience getter.


2.1.4
=====

*   (bug) The `CoreVisitor` now has no (hidden) hard dependency on `symfony/security-core` anymore.
*   (bug) The `TranslationVisitor` now has no (hidden) hard dependency on `symfony/translation-contracts` anymore.


2.1.3
=====

*   (improvement) Made `$name` in `MenuItem::createChild()` nullable.
*   (improvement) Added empty default state for `current` in MenuItems.
 

2.1.2
=====

*   (bug) Fix block name typo in base theme.
*   (improvement) Optimize the menu template.


2.1.1
=====

*   (improvement) Added `MenuItem::removeAllChildren()`.


2.1.0
=====

*   (feature) Add convenience getter to get the resolved tree from an item at `MenuRenderer::getResolvedItem()`
*   (bug) Fix invalid branch alias.
*   (improvement) Add `isCurrentAncestor()` getter in (resolved) `MenuItem`s.
*   (improvement) Add `isAnyCurrent()` getter in (resolved) `MenuItem`s.


2.0.0
=====

*   (bc) Refactored main template to ease overwriting specific parts.
*   (feature) Add `rootClass` option, to easily set class on only root.


1.1.2
=====

*   Allow Symfony 5.


1.1.1
=====

*   Reintroduce the `sort` option, but as boolean flag to toggle the sorting on/off. Default is off.


1.1.0
=====

*   Removed the `sort` option, the items are now automatically sorted: first desc by priority. 
    If the priority is the same, then asc by label.


1.0.1
=====

*   Added PhpStorm autocompletion annotation on `LazyRoute`.
*   Clean up imports in `LazyRoute`.


1.0.0
=====

Initial Release `\o/`
