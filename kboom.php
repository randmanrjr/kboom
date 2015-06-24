<?php
/*
Plugin Name: KBoom
Description: Knowledge Base Custom Post Type
Version: 1.0
Author: randmanrjr
Author URI: https://github.com/randmanrjr
*/


add_action('init','kboom_post_types');

function kboom_post_types() {

    register_post_type('kaboom_post_type',
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