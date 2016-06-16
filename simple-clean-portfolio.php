<?php
/**
* Plugin Name: Simple clean portfolio
* Version: 0.1-alpha
* Description: Simple and clean portfolio presenter
* Author: Massimiliano Masserelli
* Author URI: http://www.photomarketing.it/
* Plugin URI: http://www.photomarketing.it/simple-clean-portfolio/
* Text Domain: simple-clean-portfolio
* Domain Path: /languages
* @package Simple clean portfolio
*/
defined('ABSPATH') or die('No script kiddies please!');

// Register Custom Post Type
function simple_clean_portfolio_post_type() {

  $labels = array(
    'name' => _x('Portfolio', 'Post Type General Name', 'simple_clean_portfolio'),
    'singular_name' => _x('Portfolio item', 'Post Type Singular Name', 'simple_clean_portfolio'),
    'menu_name' => __('Simple Portfolio', 'simple_clean_portfolio'),
    'name_admin_bar' => __('Simple Portfolio', 'simple_clean_portfolio'),
    'parent_item_colon' => __('Parent Item:', 'simple_clean_portfolio'),
    'all_items' => __('All items', 'simple_clean_portfolio'),
    'add_new_item' => __('New Portfolio item', 'simple_clean_portfolio'),
    'add_new' => __('New', 'simple_clean_portfolio'),
    'new_item' => __('New item', 'simple_clean_portfolio'),
    'edit_item' => __('Edit item', 'simple_clean_portfolio'),
    'update_item' => __('Update item', 'simple_clean_portfolio'),
    'view_item' => __('View item', 'simple_clean_portfolio'),
    'search_items' => __('Search item', 'simple_clean_portfolio'),
    'not_found' => __('No portfolio items found.', 'simple_clean_portfolio'),
    'not_found_in_trash' => __('No items found in trash.', 'simple_clean_portfolio'),
  );
  $args = array(
    'label' => __('Portfolio', 'simple_clean_portfolio'),
    'description' => __('Portfolio Type', 'simple_clean_portfolio'),
    'labels' => $labels,
    'supports' => array('title', 'editor','thumbnail', 'page-attributes'),
    // 'supports'            => array( 'title', 'editor', 'custom-fields', ),
    'taxonomies' => array('scp_category'),
    // 'taxonomies'          => array( 'category', 'post_tag' ),
    'hierarchical' => false,
    'public' => true,
    'show_ui' => true,
    'show_in_menu' => true,
    'menu_position' => 5,
    'menu_icon' => 'dashicons-slides',
    'show_in_admin_bar' => true,
    'show_in_nav_menus' => false,
    'can_export' => true,
    'has_archive' => 'portfolio',
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'capability_type' => 'post',
  );

  register_post_type('sc_portfolio', $args);

  register_taxonomy('scp_category', 'sc_portfolio', array(
    'label' => __('Categories'),
    'rewrite' => array('slug' => 'portfolio'),
    'hierarchical' => true,
    'show_ui' => true,
    'query_var' => true
    //        'capabilities' => array(
    //            'assign_terms' => 'edit_guides',
    //            'edit_terms' => 'publish_guides'
    //        ),
  ));

  /**
  * Add default sort order for events
  */
  //  add_filter('parse_query', 'simple_clean_portfolio_sort');
  //
  //  function simple_clean_portfolio_sort($query) {
  //      if (!is_admin() && isset($query->query_vars) && $query->query_vars['post_type'] == 'simple_clean_portfolio') {
  //          $query->query_vars['orderby'] = 'meta_value';
  //          $query->query_vars['meta_key'] = 'iomn_pre_data';
  //          $query->query_vars['order'] = 'ASC';
  //      }
  //  }
  //
}
add_action('init', 'simple_clean_portfolio_post_type', 0);

function add_scp_columns($columns) {
  return array_merge($columns,
  array('scp_category' => 'Categoria', 'menu_order' => 'Ordinamento')
);
}
add_filter('manage_sc_portfolio_posts_columns', 'add_scp_columns');

function custom_sc_portfolio_column($column, $post_id) {
  switch ($column) {
    case 'scp_category':
      $terms = get_the_term_list( $post_id, 'scp_category', '', ',', '');
      if (is_string($terms)) {
        echo $terms;
      } else {
        echo "--";
      }
      break;
    case 'menu_order':
      echo get_post_field( 'menu_order', $post_id);
      break;
  }
  return;
}
add_action('manage_sc_portfolio_posts_custom_column', 'custom_sc_portfolio_column', 10, 2);

add_action('restrict_manage_posts','restrict_listings_by_category');
function restrict_listings_by_category() {
    global $typenow;
    global $wp_query;
    if ($typenow=='sc_portfolio') {
        $taxonomy = 'scp_category';
        $business_taxonomy = get_taxonomy($taxonomy);
        return wp_dropdown_categories(array(
            'show_option_all' =>  __("Show All {$business_taxonomy->label}"),
            'taxonomy'        =>  $taxonomy,
            'name'            =>  'scp_category',
            'orderby'         =>  'name',
            'selected'        =>  array_key_exists('scp_category', $wp_query->query) ? $wp_query->query['scp_category'] : '',
            'hierarchical'    =>  true,
            'depth'           =>  3,
            'show_count'      =>  true, // Show # listings in parens
            'hide_empty'      =>  true, // Don't show businesses w/o listings
        ));
    }
}

add_filter('parse_query','convert_scp_id_to_taxonomy_term_in_query');
function convert_scp_id_to_taxonomy_term_in_query($query) {
    global $pagenow;
    $qv = &$query->query_vars;
    // var_dump($qv);
    if ($pagenow=='edit.php' &&
            // isset($qv['taxonomy']) && $qv['taxonomy']=='scp_category' &&
            isset($qv['scp_category']) && is_numeric($qv['scp_category'])) {
        $term = get_term_by('id',$qv['scp_category'],'scp_category');
        $qv['scp_category'] = $term->slug;
    }
}


/**
* Include code for shortcodes
*/
include(__DIR__ . '/includes/scp_shortcode.php');
