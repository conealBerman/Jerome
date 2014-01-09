<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_projects');
	function register_post_type_projects(){

		$labels = array(
			'name' => 'Projects',
			'singular_name' => 'Project',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Project',
			'edit_item' => 'Edit Project',
			'new_item' => 'New Project',
			'view_item' => 'View Project',
			'search_items' => 'Search Projects',
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
			'supports' => array('title', 'editor')
		);

		register_post_type( 'projects', $args);

	}

// DEFINE META BOXES
	$projectsMetaBoxArray = array(
	);

// ADD META BOXES
	add_action( "admin_init", "admin_init_projects" );
	function admin_init_projects(){
		global $projectsMetaBoxArray;
		generateMetaBoxes($projectsMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_projects');
	function save_projects(){
		global $projectsMetaBoxArray;
		savePostData($projectsMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_projects_submenu');

	function register_sortable_projects_submenu() {
		add_submenu_page('edit.php?post_type=projects', 'Sort Projects', 'Sort', 'edit_pages', 'projects_sort', 'sort_projects');
	}

	function sort_projects() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort Projects</h2>';
		echo '</div>';

		listProjects('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "projects_custom_columns");
	// add_filter("manage_edit-projects_columns", "projects_edit_columns");

	// function projects_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Project Name",
	// 	);

	// 	return $columns;
	// }
	// function projects_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listProjects($context, $idArray = null){
		global $post;
		global $projectsMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'projects',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				$loop = new WP_Query($args);

				echo '<ul class="sortable">';
				while ($loop->have_posts()) : $loop->the_post(); 
					$output = get_the_title($post->ID);
					include(get_template_directory() . '/views/item_sortable.php');
				endwhile;
				echo '</ul>';
			break;
			
			case 'json':
				$args = array(
					'post_type'  => 'projects',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $projectsMetaBoxArray, 'json', 'projects_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'projects',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				return returnData($args, $projectsMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'projects',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $projectsMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'projects',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $projectsMetaBoxArray, 'array');

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
					'post_type'  => 'projects',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $projectsMetaBoxArray, 'array');

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
