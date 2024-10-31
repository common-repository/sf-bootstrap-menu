<?php 

class SfMenuWidget extends WP_Widget
{
	protected $defaults;
 
  function __construct(){
	$tdom = 'sf-bootstrap-menu';
	$this->defaults = array(
			'title'         	=> '',
			'alignment' 		=> '1',
			'show_root'			=> true,
			'show_root_only'	=> false,
			'expanded'			=> false,
			'child_of'        	=> '',
			'exclude'    		=> '',
			'sort_column'   	=> 'menu_order, post_title',
			'sort_order' 		=> 'ASC',
			'post_status' 		=> '',
			'title_color' 		=> '#505050',
			'title_font' 		=> '#ffffff',
			'items_color' 		=> '#f1f1f1',
			'items_color_tr' 	=> false,
			'items_font' 		=> '#f1f1f1',
			'items_active' 		=> '#505050',
			'items_active_font'	=> '#ffffff',
		);

    $widget_ops = array('classname' => 'sfmenuwidget',
			'description' => __( "SF Bootstrap Menu Widget", $tdom) );
    $control_ops = array('width' => 300, 'height' => 300);
	
	parent::__construct( 'sfmenu', __('SF Bootstrap Menu', $tdom), $widget_ops, $control_ops);
	
  }
    
function list_vertical($args = '') {
     
    $my_includes = Array();
      
      if (isset($args['child_of']) && $args['child_of']) {
        $childs = $args['child_of'];
		$child_arr = explode(",", $childs);
		
		foreach($child_arr as $child) {
			$my_includes[] = $child;
		}
      } else {
		// Query pages.  NOTE: The array is sorted in alphabetical, or menu, order.
		$pages = get_pages($args);
		
 		foreach ( $pages as $page ) {
		  $my_includes[] = $page->ID;
		  }
	  }
	
	$pageids = array();
	if (!empty($my_includes)) {	  
		foreach($my_includes as $parent) {
			if ((isset($args['show_root']) && $args['show_root'] == 'yes') 
				|| (isset($args['show_root_only']) && $args['show_root_only'] == 'yes')){
				array_push($pageids, $parent);
			}
			
			if(!isset($args['show_root_only']) || $args['show_root_only'] == 'no') {
				$args_child=array(
					'post_type'   => 'page',
					'post_parent' => $parent,
					'orderby'     => 'menu_order',
					'order'   => 'ASC',
				);
				$child_pages = new WP_Query( $args_child );
				
				while ( $child_pages->have_posts() ) {
					$child_pages->the_post();
					$r = get_the_ID();
					//echo "pp: $r, ";
					array_push($pageids, $r);
				}
			}
			
		}
	}
	
	$output = "";
	
	  
	if($args['expanded'] == true) {
		$this->show_expanded($pageids);
				
		return $output;
	}

	if (!empty($pageids)) {	  		
		// List pages, if any. Blank title_li suppresses unwanted elements.
		$output .= wp_list_pages( Array('title_li' => '',
					'sort_column' => $args['sort_column'],
					'sort_order' => $args['sort_order'],
					'include' => $pageids,
					'exclude' => $args['exclude'],
					'walker'  => new sf_bootstrap_walker_page()
				) );									
		
	} else {
		$output .= wp_list_pages( Array('title_li' => '',
					'sort_column' => $args['sort_column'],
					'sort_order' => $args['sort_order'],
					'exclude' => $args['exclude'],
					'walker'  => new sf_bootstrap_walker_page()
				) );
	}
	
	return $output;
}

function show_expanded($pageids) {
	echo '<ul class="child_page_row">';
	foreach($pageids as $p) {
		echo $this->show_children_of_expanded($p);
	} 
	echo '</ul>';
}

function show_children_of_expanded($id) {
		
	$this->list_page($id);
	
	global $post;

	$child_pages_query_args = array(
		'post_type'   => 'page',
		'child_of' => $id,
		'sort_column'     => 'menu_order',
		'order'   => 'DESC',
	);
	 
	$child_pages = get_pages( $child_pages_query_args );
	
	$c =  count($child_pages);
    if ( $child_pages && $c > 0) :
        
        echo '<ul class="always-open">';
        foreach($child_pages as $page) {
            $this->list_page($page->ID);
			//$this->show_children_of_expanded($page);
        }
        echo '</ul>';
    endif;
    wp_reset_postdata();
}
     
function list_page($p) {
	global $post;
	$class_names = "";
	
	$r = $post->ID;
	$rr = $p;
	//echo "r: $r, rr: $rr";
	
	if($post->ID == $p) {
		$class_names .= ' active';
	}
	
    echo '<li class="'. $class_names .'"><a href="';
	echo  get_permalink($p); 
	echo  '">';
    echo  get_the_title($p);
    echo  '</a></li>';
}
 

function list_horizontal($args = '') {
      
    // Add pages that were selected
    $my_includes = Array();

	if (isset($args['child_of']) && $args['child_of']) {
		$childs = $args['child_of'];
		$child_arr = explode(",", $childs);
		
		foreach($child_arr as $child) {
			$my_includes[] = $child;
		}
	} else {
			// Query pages.  NOTE: The array is sorted in alphabetical, or menu, order.
			$pages = get_pages($args);
			foreach ( $pages as $page ) {
				$my_includes[] = $page->ID;
			}
	}
	
	$pageids = array();
	if (!empty($my_includes)) {	  
		foreach($my_includes as $parent) {
		
			if ((isset($args['show_root']) && $args['show_root'] == 'yes')
				|| (isset($args['show_root_only']) && $args['show_root_only'] == 'yes')){
				array_push($pageids, $parent);
			}
			
			if(!isset($args['show_root_only']) || $args['show_root_only'] == 'no') {
				$args_child=array(
					'child_of' => $parent
				);
				$pages = get_pages($args_child);
				
				foreach ($pages as $page) {
					array_push($pageids, $page->ID);
				}
			}
		}
	}
	
	$output = "";
	
	if (!empty($pageids)) {	  
		// List pages, if any. Blank title_li suppresses unwanted elements.
		$output .= wp_list_pages( Array('title_li' => '',
					'sort_column' => $args['sort_column'],
					'sort_order' => $args['sort_order'],
					'include' => $pageids,
					'exclude' => $args['exclude'],
					'walker'  => new sf_bootstrap_walker_horizontal_menu()
				) );									
		
	} else {
		$output .= wp_list_pages( Array('title_li' => '',
					'sort_column' => $args['sort_column'],
					'sort_order' => $args['sort_order'],
					'exclude' => $args['exclude'],
					'walker'  => new sf_bootstrap_walker_horizontal_menu()
				) );
	}
	
	return $output;
}

function sf_list_pages($args = '') {
    $output = '';
 	  
	if(!isset($args['exclude']) ) {
		$args['exclude'] = '';
	}
	
	if(!isset($args['sort_column'])) {
		$args['sort_column'] = '';
	}
	
      
	$output = "";
	if (isset($args['alignment']) && $args['alignment'] == '1') {
		$output = $this->list_vertical($args);
	} else {
		$output = $this->list_horizontal($args);
	 }

	$output = apply_filters('wp_list_pages', $output);
	    
  }
 
  /**
   * Displays the Widget
   *
   */
  function widget($args, $instance){
	
    $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title']);
    $known_params = $this->known_params(0);
    foreach ($known_params as $param) {
      if (isset($instance[$param]) && strlen($instance[$param])) {
		$page_options[$param] = $instance[$param];
      }
    }

	$current_widget = 'sf_dynamic_style_'.$this->id;
	wp_register_style($current_widget, plugin_dir_url( __FILE__ ) . 'css/dynamic_style_'.$this->id.'.css');
	wp_enqueue_style($current_widget);

	//Horizontal
	if(isset($instance['alignment']) && $instance['alignment'] == 2) { 
    ?>
	<nav id="dynamic_menu_row" class="navbar navbar-default dynamic_menu_row-<?php echo "$this->id"; ?>" >
		<button type="button" class="navbar-toggle pull-right" data-toggle="collapse" data-target=".nav-horizontal-menu-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span >Menu</span>
		</button>
		<div class="navbar-collapse nav-horizontal-menu-collapse collapse">
			<ul id="menu-horizontal" class="nav navbar-nav">					   
			<?php $this->sf_list_pages($page_options); ?>
			</ul>
		</div>
	</nav>
	<?php
	} else {
	//Vertical sidemenu
	?>
	<div class="nav-side-menu nav-side-menu-<?php echo "$this->id"; ?>">
		<div class="brand"><?php echo "$title"; ?></div>
		<i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
		<div class="menu-list">
			<ul id="menu-content" class="menu-content collapse out">					   
			<?php $this->sf_list_pages($page_options); ?>
			</ul>
		</div>
	</div>
	<?php
	}
  }

  function known_params ($options = 0) {
	$tdom = 'sf-bootstrap-menu';
    $option_menu = array(
			'title' => array('title' => __("Title:", $tdom)),
			'alignment' => array('title' => __("Menu alignment", $tdom),
						'type' => 'select'),
			'child_of' => array('title' => __("Root pages:", $tdom),
						'desc' => __("List of root page IDs to show", $tdom)),
			'show_root' => array('title' => __("Show top-level pages:", $tdom),
					      'type' => 'checkbox'),
			'show_root_only' => array('title' => __("Show top-level pages only:", $tdom),
					      'type' => 'checkbox'),
			'expanded' => array('title' => __("Always expanded:", $tdom),
					      'type' => 'checkbox'),
			'exclude' => array('title' => __("Exclude pages:", $tdom),
					    'desc' => __("List of page IDs to exclude", $tdom)),
			'sort_column' => array('title' => __("Sort field:", $tdom),
						'desc' => __("Comma-separated list: <em>menu_order,post_title,post_date, post_modified, ID, post_author, post_name</em>", $tdom)),
			'sort_order' => array('title' => __("Sort direction:", $tdom),
				       'desc' => __("(default: ASC)", $tdom)),
			'post_status' => array('title' => __("Post status:", $tdom),
						'desc' => __("(default: publish)", $tdom)),
			'title_color' => array('title' => __("Title background color:", $tdom),
						'type' => 'color',
						'value' => '#505050'),
			'title_font' => array('title' => __("Title font color:", $tdom),
						'type' => 'color',
						'value' => '#ffffff'),
			'items_color' => array('title' => __("Menu items background color:", $tdom),
						'type' => 'color',
						'value' => '#ffffff'),
			'items_color_tr' => array('title' => __("Transparent background:", $tdom),
					      'type' => 'checkbox'),
			'items_font' => array('title' => __("Menu items font color:", $tdom),
						'type' => 'color',
						'value' => '#000000'),
			'items_active' => array('title' => __("Active menu item background color:", $tdom),
						'type' => 'color',
						'value' => '#505050'),
			'items_active_font' => array('title' => __("Active menu item font color:", $tdom),
						'type' => 'color',
						'value' => '#ffffff'),
			 );
    return ($options ? $option_menu : array_keys($option_menu));
  }

  /**
   * Saves the widget's settings.
   *
   */
  function update($new_instance, $old_instance){
    $instance = $old_instance;
    $known_params = $this->known_params();
    unset($instance['menu_order']);
	unset($instance['error']);
	
    foreach ($known_params as $param) {
      $instance[$param] = strip_tags(stripslashes($new_instance[$param]));
	  
	  if($new_instance[$param] && $new_instance[$param]['value'] && ($param == 'items_color' 
		|| $param == 'title_color'
		|| $param == 'title_font'
		|| $param == 'items_color'
		|| $param == 'items_font'
		|| $param == 'items_active'
		|| $param == 'items_active_font')) {
		$instance[$param]['value'] = $new_instance[$param]['value'];
	  }
	  
	  if($param == 'alignment') {
		$instance[$param] = $new_instance[$param];
	  }
	  
    }
    $instance['sort_order'] = strtolower($instance['sort_order']) == 'desc'?'DESC':'ASC';
	
	$instance = $this->generateDynamicCss($instance);
	 
    return $instance;
  }
  
  function generateDynamicCss($instance) {
	$css = $this->generate_css( $instance );
    $upload_dir = wp_upload_dir();
    $path = plugin_dir_path( __FILE__ ) . 'css/dynamic_style_'.$this->id.'.css';
	$result = file_put_contents($path, $css, LOCK_EX);
	if ( $result === false )
	{
	   $instance['error'] = "Could not save dynamic css file into path $path";
	}
	
	if(!is_writable($path)) {
		$instance['error'] = "Could not save dynamic css file. Required plugin path ($path) is not writable. We need it to store the dynamic CSS file.";
	}
	
	return $instance;
  }
  
  function generate_css( $args ) {
	$css ="";
	
	if($args['items_color_tr'] == true) {
		$args['items_color'] = 'transparent';
	}
	
	//horizontal
	if($args['alignment'] == 1) {
		$css .="
			.nav-side-menu-" . $this->id ." {font-size: 12px;font-weight: 200;background-color: {$args['items_color']}}
			.nav-side-menu-" . $this->id ." .brand {background-color: {$args['title_color']};line-height: 50px;display: block;text-align: center;font-size: 14px; color:{$args['title_font']}}
			.nav-side-menu-" . $this->id ." li .active,.nav-side-menu-" . $this->id ." ul .active {background-color: {$args['items_active']}}
			.nav-side-menu-" . $this->id ." li.active a {color:{$args['items_active_font']}}
			.nav-side-menu-" . $this->id ." li .sub-menu li.active,.nav-side-menu-" . $this->id ." ul .sub-menu li.active,.nav-side-menu-" . $this->id ." li .sub-menu li.active a,.nav-side-menu-" . $this->id ." ul .sub-menu li.active a {color: {$args['items_active_font']};background-color: {$args['items_active']}}";
	} 
	//vertical
	else {
		$css .="
			.dynamic_menu_row-" . $this->id ." {background-color: {$args['items_color']}}
			.dynamic_menu_row-" . $this->id ." .navbar-nav>.active>a,.dynamic_menu_row-" . $this->id ." .navbar-nav>.active>a:hover,
			.dynamic_menu_row-" . $this->id ." .navbar-nav>.open>a,.dynamic_menu_row-" . $this->id ." .navbar-nav>.open>a:focus,
			.dynamic_menu_row-" . $this->id ." .navbar-nav>.open>a:hover,.dynamic_menu_row-" . $this->id ." .navbar-nav>li>a:hover,
			.dynamic_menu_row-" . $this->id .".navbar-nav>.active>a:focus {background-color: {$args['items_active']}}
			.dynamic_menu_row-" . $this->id ." li a,.dynamic_menu_row-" . $this->id ." li ul li a,
			.dynamic_menu_row-" . $this->id ." .navbar-nav > li > a{color: {$args['items_font']}}
			.dynamic_menu_row-" . $this->id ." li.active a {color:{$args['items_active_font']}}
			.dynamic_menu_row-" . $this->id ." .active a:focus,.dynamic_menu_row-" . $this->id ." .navbar-nav>.open>a,
			.dynamic_menu_row-" . $this->id ." .navbar-nav>.open>a:focus,.dynamic_menu_row-" . $this->id ." a:hover,#dynamic_menu_row li.active>a {color: {$args['items_active_font']}}
		";
	}
	
    return $css;
   }

  /**
   * Creates the edit form for the widget.
   *
   */
  function form($instance){
	$instance = wp_parse_args( (array) $instance, $this->defaults );

    if (isset($instance['menu_order'])) {
      $instance['sort_column'] = 'menu_order,post_title';
    }

    $option_menu = $this->known_params(1);
	$tdom = 'sf-bootstrap-menu';

	
	foreach (array_keys($option_menu) as $param) {
		$param_display[$param] = htmlspecialchars($instance[$param]);
	}

	foreach ($option_menu as $option_name => $option) {
	
		$name = $this->get_field_name($option_name);
		
		$checkval='';
		$desc = '';
		$h = '';
		$v = '';
		if (isset($option['desc']) && $option['desc']) {
		  $desc = '<br /><small>' . __($option['desc'], $tdom) . '</small>';
		}
		
		if (!isset($option['type'])) {
			$option['type'] = '';
		}
		
		switch ($option['type']) {
			case 'checkbox':
			  if ($instance[$option_name]) // special HTML and override value
				$checkval = 'checked="yes" ';
				$param_display[$option_name] = 'yes';
			  break;
			case '':
			  $option['type'] = 'text';
			  break;
		}
			
		if($option['type'] == "select") {	
?>			
			
			<p style="text-align:right;">
			<label for="<?php echo $this->get_field_name($option_name); ?>"><?php echo __($option['title'], $tdom); ?>
			<select style="width: 200px;" id="<?php echo $this->get_field_id($option_name); ?>" name="<?php echo $this->get_field_name($option_name); ?>">
			<?php for ($i=1;$i<=2;$i++) {
				echo '<option value="'.$i.'"';
				if ($i==$instance['alignment']) echo ' selected="selected"';
				if($i==1) {
					echo '>Vertical</option>';
				} else {
					echo '>Horizontal</option>';
				}
			 } ?>
			</select></label><?php echo $desc; ?></p>
<?php
		} else {
		print '<p style="text-align:right;">
			<label for="' . $this->get_field_name($option_name) . '">' . 
		  __($option['title'], $tdom) . ' 
			<input style="width: 200px;" id="' . $this->get_field_id($option_name) . '" name="' . $name . "\" type=\"{$option['type']}\" {$checkval}value=\"{$param_display[$option_name]}\" />
			</label>$desc</p>";
		  }
	}

	if(isset($instance['error'])){
		echo "<div style='color:red'>";
		echo $instance['error'];
		echo "</div>";
	}
  }
	
}

?>
