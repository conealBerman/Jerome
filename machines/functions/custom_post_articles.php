<?php

// REGISTER CUSTOM POST TYPE
	add_action( 'init', 'register_post_type_articles');
	function register_post_type_articles(){

		$labels = array(
			'name' => 'Articles',
			'singular_name' => 'Article',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New Article',
			'edit_item' => 'Edit Article',
			'new_item' => 'New Article',
			'view_item' => 'View Article',
			'search_items' => 'Search Articles',
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

		register_post_type( 'articles', $args);

	}

// DEFINE META BOXES
	$articlesMetaBoxArray = array(
	    "articles_article_date_meta" => array(
	    	"id" => "articles_article_date_meta",
	        "name" => "Article Date",
	        "post_type" => "articles",
	        "position" => "side",
	        "priority" => "low",
	        "callback_args" => array(
	        	"input_type" => "input_date",
	        	"input_name" => "article_date"
	        )
	    ),
	);

// ADD META BOXES
	add_action( "admin_init", "admin_init_articles" );
	function admin_init_articles(){
		global $articlesMetaBoxArray;
		generateMetaBoxes($articlesMetaBoxArray);
	}

// SAVE POST TO DATABASE
	add_action('save_post', 'save_articles');
	function save_articles(){
		global $articlesMetaBoxArray;
		savePostData($articlesMetaBoxArray, $post, $wpdb);
	}

// SORTING CUSTOM SUBMENU

	add_action('admin_menu', 'register_sortable_articles_submenu');

	function register_sortable_articles_submenu() {
		add_submenu_page('edit.php?post_type=articles', 'Sort Articles', 'Sort', 'edit_pages', 'articles_sort', 'sort_articles');
	}

	function sort_articles() {
		
		echo '<div class="wrap"><div id="icon-tools" class="icon32"></div>';
			echo '<h2>Sort Articles</h2>';
		echo '</div>';

		listArticles('sort');
	}

// CUSTOM COLUMNS

	// add_action("manage_posts_custom_column",  "articles_custom_columns");
	// add_filter("manage_edit-articles_columns", "articles_edit_columns");

	// function articles_edit_columns($columns){
	// 	$columns = array(
	// 		"full_name" => "Article Name",
	// 	);

	// 	return $columns;
	// }
	// function articles_custom_columns($column){
	// 	global $post;

	// 	switch ($column) {
	// 		case "full_name":
	// 			$custom = get_post_custom();
	// 			echo "<a href='post.php?post=" . $post->ID . "&action=edit'>" . $custom["first_name"][0] . " " . $custom["last_name"][0] . "</a>";
	// 		break;
	// 	}
	// }

// LISTING FUNCTION
	function listArticles($context, $idArray = null){
		global $post;
		global $articlesMetaBoxArray;
		
		switch ($context) {
			case 'sort':
				$args = array(
					'post_type'  => 'articles',
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
					'post_type'  => 'articles',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				returnData($args, $articlesMetaBoxArray, 'json', 'articles_data');
			break;

			case 'array':
				$args = array(
					'post_type'  => 'articles',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);
				return returnData($args, $articlesMetaBoxArray, 'array');
			break;

			case 'rest':
				$args = array(
					'post_type'  => 'articles',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true,
					'post__in' => $idArray
				);
				return returnData($args, $articlesMetaBoxArray, 'array');
			break;

			case 'checkbox':
				$args = array(
					'post_type'  => 'articles',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $articlesMetaBoxArray, 'array');

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
					'post_type'  => 'articles',
					'order'   => 'ASC',
					'meta_key'  => 'custom_order',
					'orderby'  => 'meta_value_num',
					'nopaging' => true
				);

				$outputArray = returnData($args, $articlesMetaBoxArray, 'array');

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
