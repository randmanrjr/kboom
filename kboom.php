<?php
/*
Plugin Name: KBoom
Description: Knowledge Base Custom Post Type
Version: 1.0
Author: randmanrjr
Author URI: https://github.com/randmanrjr
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

add_action('init','kboom_post_types');

function kboom_post_types() {

    register_post_type('kaboom',
        array(
            'labels'                => array(
                'name'              => 'Knowledge Base Articles',
                'menu_name'         => 'KB Articles',
                'name_admin_bar'    => 'KB Article',
                'add_new'           => 'New KB Article',
                'add_new_item'      => 'Add New KB Article'
            ),
            'singular_label'        => 'Knowledge Base Article',
            'public'                => true,
            'show_ui'               => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-welcome-learn-more',
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => true,
            'show_in_menu'          => true,
            'rewrite'               => array('slug' => 'kbarticles'),
            'supports'              => array('title', 'editor', 'author', 'revisions', 'comments', 'thumbnail', 'excerpt', 'page-attributes'),
            'taxonomies'            => array('post_tag', 'category')
        ));
}


//flush the rewrite rules after plugin activation

function kboom_rewrite_flush() {
    kboom_post_types();
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'kboom_rewrite_flush');

//ajax search enqueues

add_action('wp_enqueue_scripts','ajax_search_enqueues');

function ajax_search_enqueues() {
    if (is_page('knowledge-base')) :
        $ajax_nonce = wp_create_nonce('kb-ajax-request');
        wp_enqueue_script( 'ajax-search', plugin_dir_url(__FILE__) . 'ajax-search.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'ajax-search', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'ajaxnonce' => $ajax_nonce ) );
    endif;
}

//handle search request via admin-ajax.php

add_action('wp_ajax_kb_search','kb_search');
add_action('wp_ajax_nopriv_kb_search', 'kb_search');

function kb_search() {
    check_ajax_referer('kb-ajax-request', 'security');
    //set $debug to true for verbose query output
    $debug = false;
    //conduct search
    $query      = esc_attr($_POST['query'],'foundationpress');
    $searchAll  = $_POST['checkBox'];
    if ($searchAll == 'true') {$pt = 'any';} else {$pt = 'kaboom';}
    //search post content
    $args       = array(
        'post_type'     => $pt,
        'post_status'   => 'publish',
        'sentence'      => 1,
        's'             => $query
    );
    //args for searching ACF sub fields for the flexible content layout
    $args2      = array(
        'post_type'     => $pt,
        'post_status'   => 'publish',
        'meta_query'    => array(
            'relation'  => 'OR',
            array(
                'key'   => 'content_%_text',
                'compare' => 'LIKE',
                'value' => $query
            ),
            array(
                'key'   => 'content_%_accordion_entry_%_title',
                'compare' => 'LIKE',
                'value' => $query
            ),
            array(
                'key'   => 'content_%_accordion_entry_%_content',
                'compare' => 'LIKE',
                'value' => $query
            )
        )
    );
    //query posts
    $searchKB = new WP_Query($args);
    //query ACF meta fields
    $searchKBmeta = new WP_Query($args2);
    //merge queries for complete site content search
    $results = new WP_Query();
    $results->posts = array_unique( array_merge($searchKB->posts, $searchKBmeta->posts), SORT_REGULAR);
    $results->post_count = count($results->posts);

    ob_start();

    if ($results->have_posts()) :

    ?>
    <div class="panel">

    <header class="page-header">
        <h2 class="search"><?php printf( __( 'Search Results: %s', 'foundationpress' ), get_search_query() ); ?></h2>
    </header>

    <?php
    while ( $results->have_posts() ) : $results->the_post();
        echo '<div>'; ?>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title('', ''); ?></a></h3>
        <?php //checks to see if post contains an ACF flexible content layout ?>
        <?php if (have_rows('content')) :
            $incr = 0;
            while (have_rows('content')) : the_row();
                if (get_row_layout() == 'text') :
                    if ($incr < 1) : //only displays the first text subfield encountered
                        $text = wp_strip_all_tags(get_sub_field('text'));
                        $length = 200;
                         if (strlen($text) > $length) :
                            $text = substr($text, 0, strpos($text, ' ', $length));//create and excerpt ?>
                            <p><?php echo $text;?><a href="<?php the_permalink(); ?>"> Read more...</a></p>
                        <?php else : ?>
                            <p><?php echo $text;?><a href="<?php the_permalink(); ?>"> Read more...</a></p>
                    <?php endif;
                    endif;
                endif;
            $incr++;
            endwhile;
            else :
                //if no ACF flexible content layout use post excerpt
                the_excerpt();
            endif;
        echo '</div>';
    endwhile; ?>
        </div>
   <?php else : ?>
        <div class="panel">
            <h2 style="color: #FF8300">No Matching Results</h2>
            <p>Modify your query and please try searching again.</p>
        </div>
    <?php endif;

    $content = ob_get_clean();

    echo $content;
    if ($debug == true) :
    echo '<pre>';
    var_dump($results);
    echo '</pre>';
        endif;
    wp_reset_query();
    die();
}

//custom filter for querying ACF sub fields that accounts for the unknown row number(s)
function my_meta_posts_where($where) {
    $where = str_replace("meta_key = 'content_%", "meta_key LIKE 'content_%", $where);
    return $where;
}
add_filter('posts_where','my_meta_posts_where');