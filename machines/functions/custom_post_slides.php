<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_slides');
	function register_post_type_slides(){

		$labels = array(
			'name' => 'Slides',
			'singular_name' => 'Slide',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Slide',
			'edit_item' => 'Edit Slide',
			'new_item' => 'New Slide',
			'view_item' => 'View Slide',
			'search_items' => 'Search Slides',
			'not_found' => 'Nothing found',
			'not_found_in_trash' => 'Nothing found in trash',
			'parent_item_colon' => ''
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title', 'editor', 'thumbnail')
		);

		register_post_type( 'slides', $args);

	}

// DEFINE META BOXES
	$slidesMetaBoxArray = array(
	    "slides_slide_text_meta" => array(
	    	"id" => "slides_slide_text_meta",
	        "name" => "Slide Text",
	        "post_type" => "slides",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_text",
	        	"input_name" => "slide_text"
	        )
	    ),
	);

// ADD META BOXES
	add_action( "admin_init", "admin_init_slides" );
	function admin_init_slides(){
		global $slidesMetaBoxArray;
		generateMetaBoxes($slidesMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_slides');
	function save_slides(){
		global $slidesMetaBoxArray;
		savePostData($slidesMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_slides_submenu');

	function register_sortable_slides_submenu() {
		add_submenu_page('edit.php?post_type=slides', 'Sort Slides', 'Sort', 'edit_pages', 'slides_sort', 'sort_slides');
	}

	function sort_slides() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort Slides</h2>';
		echo '</div>';

		listSlides('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "slides_custom_columns");
	// add_filter("manage_edit-slides_columns", "slides_edit_columns");

	// function slides_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Slide Name",
	// 	);

	// 	return $columns;
	// }
	// function slides_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listSlides($context, $idArray = null){
		global $post;
		global $slidesMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'slides',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				$loop = new WP_Query($args);

				echo '<ul class="sortable">';
				while ($loop->have_posts()) : $loop->the_post(); 
					$output = get_post_meta($post->ID, 'first_name', true) . " " . get_post_meta($post->ID, 'last_name', true);
					include(get_template_directory() . '/views/item_sortable.php');
				endwhile;
				echo '</ul>';
			break;
			
			case 'json':
				$args = array(
					'post_type'  => 'slides',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $slidesMetaBoxArray, 'json', 'slides_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'slides',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				return returnData($args, $slidesMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'slides',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $slidesMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'slides',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $slidesMetaBoxArray, 'array');

				$field_options = array();
				foreach ($outputArray as $key => $value) {
					$checkBoxOption = array(
						"id" => $value['post_id'],
						"name" => $value['the_title'],
					);
					$field_options[] = $checkBoxOption;
				}

				return $field_options;

			break;

			case 'select':
				$args = array(
					'post_type'  => 'slides',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $slidesMetaBoxArray, 'array');

				$field_options = array();
				foreach ($outputArray as $key => $value) {
					$checkBoxOption = array(
						"id" => $value['post_id'],
						"name" => html_entity_decode($value['the_title'])
					);
					$field_options[] = $checkBoxOption;
				}

				return $field_options;

			break;
		}
	}

?>
