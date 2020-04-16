2.x to 3.0
==========

*   There are no automatically registered voters anymore. To have the same behavior as before, create a class that
    extends `SimpleRouteVoter`.


2.x to 2.1.1
============

*   The template block `text_element` was renamed to `text_item`.
*   The template block `text_element_wrap` was removed. It was merged into `item`.
*   The template block `link_item_wrap` was removed. It was merged into `item`.
*   The `attributes` in template block `item_wrap` no correctly the link attributes instead of the list item attributes.  


1.x to 2.0
==========

*   The main theme template has changed. You need to adapt the new naming convention, and you can possibly simplify your template.
    Read the comments in `@BecklynMenu/menu.html.twig` for details.
