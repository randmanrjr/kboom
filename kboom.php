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

function ajax_search_enqueues() {
    if (is_page('knowledge-base')) :
        wp_enqueue_script( 'ajax-search', plugin_dir_url(__FILE__) . 'ajax-search.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'ajax-search', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    endif;
}

add_action('wp_enqueue_scripts','ajax_search_enqueues');

//handle search request via admin-ajax.php

add_action('wp_ajax_kb_search','kb_search');
add_action('wp_ajax_nopriv_kb_search', 'kb_search');

function kb_search() {
    //conduct search
    $query      = esc_attr($_POST['query'],'foundationpress');
    $searchAll  = $_POST['checkBox'];
    if ($searchAll == 'true') {$pt = 'any';} else {$pt = 'kaboom';}

    $args       = array(
        'post_type'     => $pt,
        'post_status'   => 'publish',
        'sentence'      => 1,
        's'             => $query
    );

    $searchKB = new WP_Query($args);

    ob_start();

    if ($searchKB->have_posts()) :

    ?>

    <header class="page-header">
        <h2 class="search"><?php printf( __( 'Search Results: %s', 'foundationpress' ), get_search_query() ); ?></h2>
    </header>

    <?php
    while ( $searchKB->have_posts() ) : $searchKB->the_post();
        echo '<div>'; ?>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title('', ''); ?></a></h3>
        <?php the_excerpt();
        echo '</div>';
    endwhile;
    else : ?>
    <p>No Matching Results</p>
    <?php endif;
    wp_reset_query();

    $content = ob_get_clean();

    echo $content;
    die();
}