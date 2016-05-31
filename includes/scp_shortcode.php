<?php
/**
 * scp-list shortcode definition
 * Produces a list of portfolio items
 */
function scp_list_shortcode($atts) {
  $html = ""; // Buffer for generated html
  wp_enqueue_style('simple-clean-portfolio-css', plugins_url('../css/simple-clean-portfolio.css', __FILE__));

  $args = array(
    'post_type' => 'sc_portfolio',
    'showposts' => 10,
    'orderby' => 'title',
    'order' => 'ASC'
  );
  if (is_array($atts) && array_key_exists('category', $atts)) {
    // Filtra gli articoli per categoria
    $args['scp_category'] = $atts['category'];
  }
  $_query = new WP_Query($args);

  $html .= '<section class="main clearfix">' . "\n";
  while ($_query->have_posts()) {
    $_query->the_post();
    $html .= '<div class="work">';
    $html .= sprintf('<a href="%s">', get_permalink());
    $thumbnail_data = wp_get_attachment_image_src(get_post_thumbnail_id(), 'small');
    $html .= sprintf('<img src="%s" class="media" alt=""/>', $thumbnail_data[0]);
    $html .= '<div class="caption">';
    $html .= '<div class="work_title">';
    $html .= sprintf('<h1>%s</h1>', get_the_title());
    //         <p>Descrizione lunga dell'oggetto,</p>
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</a>';
    $html .= '</div>';
    $html .= "\n";
  }
  $html .= "</section>\n";
  wp_reset_query();
  wp_reset_postdata();
  return $html;
}
add_shortcode('scp-list', 'scp_list_shortcode');
