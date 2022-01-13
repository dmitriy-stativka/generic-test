<?php
class Walker_Module_Menu extends Walker_Nav_Menu {

    // Set the properties of the element which give the ID of the current item and its parent
    //var $db_fields = array( 'parent' => 'parent_id', 'id' => 'object_id' );

//    // Displays start of a level. E.g '<ul>'
//    // @see Walker::start_lvl()
//    function start_lvl(&$output, $depth=0, $args=array()) {
//        $output .= "\n<ul>\n";
//    }
//
//    // Displays end of a level. E.g '</ul>'
//    // @see Walker::end_lvl()
//    function end_lvl(&$output, $depth=0, $args=array()) {
//        $output .= "</ul>\n";
//    }

    // Displays start of an element. E.g '<li> Item Name'
    // @see Walker::start_el()

    private $counter = 1;

    function start_lvl(&$output, $depth=0, $args=array())
    {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat( $t, $depth );

        // Default class.
        $classes = array( 'sub-menu' );

        /**
         * Filters the CSS class(es) applied to a menu list element.
         *
         * @since 4.8.0
         *
         * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
         * @param stdClass $args    An object of `wp_nav_menu()` arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= "{$n}{$indent}<ul$class_names>{$n}";
    }

    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 )
    {
        global $wp_query;

        $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

        // Depth-dependent classes.
        $depth_classes = array(
            ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
            ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
            ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
            'menu-item-depth-' . $depth
        );
        $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

        // Passed classes.
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

        // Build HTML.
        $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="menu-col-'. $this->counter .' ' . $depth_class_names . ' ' . $class_names . '">';

        // Link attributes.
        $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
        $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

        if( $depth == 0 )
            $this->counter++;

        $image_html = '';
        $image = get_field( 'image', $item );
        if( !empty( $image ) && $depth == 0 ){
            $image_alt = ( empty( $image[ 'alt' ] ) ) ? $image[ 'name' ] : $image[ 'alt' ] ;
            $image_html = '<img class="" src="'. $image[ 'url' ] .'" alt="'. $image_alt .'">';
        }

        $description_html = '';
        $description = get_field( 'menu_description', $item );
        if( !empty( $description && $depth == 0 ) ){
            $description_html = '<span class="menu-item-title">' .$description. '</span>';
        }


        // Build HTML output and pass through the proper filter.
        $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s%6$s%7$s</a>%8$s',
            $args->before,
            $attributes,
            $image_html,
            $args->link_before,
            '<span class="menu-item-title">'.apply_filters( 'the_title', $item->title, $item->ID ).'</span>',
            $args->link_after,
            $description_html,
            $args->after
        );
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }

//    // Displays end of an element. E.g '</li>'
//    // @see Walker::end_el()
//    function end_el(&$output, $item, $depth=0, $args=array()) {
//        $output .= "</li>\n";
//    }

}