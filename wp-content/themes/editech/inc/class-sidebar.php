<?php

class editech_setup_sidebar {
    public function __construct() {
        add_action('widgets_init', array($this, 'init_sidebar'), 9);
        add_filter('body_class', array($this, 'body_class'));
        add_filter('opal_theme_sidebar', array($this, 'set_sidebar'));
    }

    public function body_class($classes) {
        if (is_active_sidebar('sidebar-blog') && !is_404()) {
            $classes[] = 'opal-content-layout-2cr';
        }

        $classes[] = 'opal-default-content-layout';

        return $classes;
    }

    public function init_sidebar() {
        register_sidebar(array(
            'name'          => esc_html__('Blog Sidebar', 'editech'),
            'id'            => 'sidebar-blog',
            'description'   => esc_html__('Add widgets here to appear in your sidebar on blog posts and archive pages.', 'editech'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ));
    }

    public function set_sidebar($sidebar) {
        if (is_active_sidebar('sidebar-blog') && !is_404()) {
            $sidebar = 'sidebar-blog';
        }

        return $sidebar;
    }
}

return new editech_setup_sidebar();