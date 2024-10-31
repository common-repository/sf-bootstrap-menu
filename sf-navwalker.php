<?php
if (!class_exists('sf_bootstrap_walker_page')) {
class sf_bootstrap_walker_page extends Walker_Page {

	private $curItem, $activeItem, $closedLi, $expanded;

	function __construct($init_parameter) {
        $this->expanded = $init_parameter;
    }

	/**
 * @see Walker::start_lvl()
 * @since 2.1.0
 *
 * @param string $output Passed by reference. Used to append additional content.
 * @param int $depth Depth of page. Used for padding.
 * @param array $args
 */
function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);

	if($this->expanded == true) {
		$output .= "\n$indent<ul class='sub-menu always-open' role='menu'>\n";
	} else {
		$output .= "\n$indent<ul class='dropdown-menu sub-menu collapse' role='menu'>\n";
	}
}

function end_lvl(&$output, $depth=0, $args=array()) {
        $output .= "</ul>\n";
	
	$this->closedLi = $this->curItem;
}

/**
 * @see Walker::start_el()
 * @since 2.1.0
 *
 * @param string $output Passed by reference. Used to append additional content.
 * @param object $page Page data object.
 * @param int $depth Depth of page. Used for padding.
 * @param int $current_page Page ID.
 * @param array $args
 */
function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
    if ( $depth )
        $indent = str_repeat("\t", $depth);
    else
        $indent = '';
	
	$this->curItem = $current_page;

    extract($args, EXTR_SKIP);
    $css_class = array();
    $arrow = "";
    $arrow_link = "";

    if( isset( $args['pages_with_children'][ $page->ID ] ) ) {
 		if($depth > 0) {
			$css_class[] = 'dropdown-submenu';
		} else {
			if($this->expanded != true) {
				$arrow = "<b class='caret sf-menu-caret'></b>";
				$arrow_link=" data-toggle='dropdown'";
			}
		}
	}

    if ( !empty($current_page) ) {
        $_current_page = get_post( $current_page );
			
		if ( $page->ID == $current_page ) {
				$css_class[] = 'active';
			$this->activeItem = $page->ID;
		}
			elseif ( $_current_page && $page->ID == $_current_page->post_parent ) {
				$css_class[] = 'active';
			$this->activeItem = $current_page;
		}
		elseif (!empty($_current_page->post_parent) ) {
			$parent_page = get_post( $_current_page->post_parent );
			if (!empty($parent_page->post_parent) && $page->ID == $parent_page->post_parent ) {
				//$css_class[] = 'active';
				$this->activeItem = $current_page;
			}
		}
    } elseif ( $page->ID == get_option('page_for_posts') ) {
        $css_class[] = '';
    }

	$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $page->ID, $page, $args );
	$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
	
	$classes = empty( $css_class ) ? array() : (array) $css_class;
	$classes[] = 'menu-item-' . $page->ID;	
	$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $page, $args ) );
	

	if ( in_array( 'current-menu-item', $classes ) )
		$class_names .= ' active';
	
	$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
	
    /**
     * Filter the list of CSS classes to include with each page item in the list.
     *
     * @since 2.8.0
     *
     * @see wp_list_pages()
     *
     * @param array   $css_class    An array of CSS classes to be applied
     *                             to each list item.
     * @param WP_Post $page         Page data object.
     * @param int     $depth        Depth of page, used for padding.
     * @param array   $args         An array of arguments.
     * @param int     $current_page ID of the current page.
     */

     $css_class = implode( ' ',  (array)apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ));

    if ( '' === $page->post_title )
        $page->post_title = sprintf( __( '#%d (no title)' ), $page->ID );

    
    $output .= $indent . '<li ' . $id . $class_names . '><a href="' . get_permalink($page->ID) . '"' . $arrow_link . '>' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after .  $arrow .'</a>';
    

    if ( !empty($show_date) ) {
        if ( 'modified' == $show_date )
            $time = $page->post_modified;
        else
            $time = $page->post_date;

        $output .= " " . mysql2date($date_format, $time);
    }
}

function end_el(&$output, $item, $depth=0, $args = array(), $current_page = 0) {
	$output .= "</li>\n";
	$this->curItem = "";
	$this->activeItem = "";
}
}
}
?>