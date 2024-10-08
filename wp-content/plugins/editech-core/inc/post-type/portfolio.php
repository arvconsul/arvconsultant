<?php


if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class OSF_Custom_Post_Type_Portfolio
 */
class OSF_Custom_Post_Type_Portfolio extends OSF_Custom_Post_Type_Abstract {
    public $post_type = 'osf_portfolio';
    public $prefix    = 'osf_portfolio_';
    public $taxonomy  = 'osf_portfolio_category';
    static $instance;

    public static function getInstance() {
        if (!isset(self::$instance) && !(self::$instance instanceof OSF_Custom_Post_Type_Portfolio)) {
            self::$instance = new OSF_Custom_Post_Type_Portfolio();
        }

        return self::$instance;
    }


    public function create_post_type() {

        $labels = array(
            'name'               => __('Portfolios', 'editech-core'),
            'singular_name'      => __('Portfolios', 'editech-core'),
            'add_new'            => __('Add Portfolio', 'editech-core'),
            'add_new_item'       => __('Add New Portfolio', 'editech-core'),
            'edit_item'          => __('Edit Portfolio', 'editech-core'),
            'new_item'           => __('New Portfolio', 'editech-core'),
            'all_items'          => __('All Portfolios', 'editech-core'),
            'view_item'          => __('View Portfolio', 'editech-core'),
            'search_items'       => __('Search Portfolio', 'editech-core'),
            'not_found'          => __('No Portfolio found', 'editech-core'),
            'not_found_in_trash' => __('No Portfolio found in Trash', 'editech-core'),
            'menu_name'          => __('Portfolios', 'editech-core'),
        );

        $labels     = apply_filters('osf_postype_portfolio_labels', $labels);
        $slug_field = osf_get_option('portfolio_settings', 'slug_portfolio', 'portfolios');
        $slug       = isset($slug_field) ? $slug_field : "portfolios";

        register_post_type($this->post_type,
            array(
                'labels'        => $labels,
                'supports'      => array('title', 'editor', 'excerpt', 'thumbnail'),
                'public'        => true,
                'has_archive'   => true,
                'rewrite'       => array('slug' => apply_filters('osf_custom_post_type_portfolio_slug', $slug)),
                'menu_position' => 5,
                'categories'    => array(),
                'menu_icon'     => 'dashicons-portfolio',
            )
        );
    }


    /**
     * @return void
     */
    public function create_taxonomy() {
        $labels         = array(
            'name'              => __('Categories', "editech-core"),
            'singular_name'     => __('Category', "editech-core"),
            'search_items'      => __('Search Category', "editech-core"),
            'all_items'         => __('All Categories', "editech-core"),
            'parent_item'       => __('Parent Category', "editech-core"),
            'parent_item_colon' => __('Parent Category:', "editech-core"),
            'edit_item'         => __('Edit Category', "editech-core"),
            'update_item'       => __('Update Category', "editech-core"),
            'add_new_item'      => __('Add New Category', "editech-core"),
            'new_item_name'     => __('New Category Name', "editech-core"),
            'menu_name'         => __('Categories', "editech-core"),
        );
        $slug_cat_field = osf_get_option('portfolio_settings', 'slug_category_portfolio', 'category-portfolio');
        $slug_cat       = isset($slug_cat_field) ? $slug_cat_field : "category-portfolio";
        $args           = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'show_in_nav_menus' => false,
            'rewrite'           => array('slug' => $slug_cat)
        );
        // Now register the taxonomy
        register_taxonomy($this->taxonomy, array($this->post_type), $args);
    }

    /**
     *
     */
    public static function getQuery($args = array()) {
        $default = array(
            'post_type' => 'osf_portfolio',
        );

        $args = array_merge($default, $args);

        return new WP_Query($args);
    }

    public static function getId($post_id = 0) {
        $post_ids = array();
        $array    = array(
            'post__not_in' => array($post_id),
        );
        $sevices  = self::getQuery($array);
        while ($sevices->have_posts()) {
            $sevices->the_post();
            $post_ids[] = get_the_ID();
        }
        wp_reset_postdata();

        return $post_ids;
    }

    /**
     * @param $term_id is term_id in taxonomy
     * @param $post    is name post type
     * @param taxonomy  is name taxonomy
     */
    public static function get_by_term_id($term_id, $per_page = -1) {
        wp_reset_query();
        $args = array();
        if ($term_id == 0 || empty($term_id)) {
            $args = array(
                'posts_per_page' => $per_page,
                'post_type'      => "osf_portfolio",
            );
        } else {
            $args = array(
                'posts_per_page' => $per_page,
                'post_type'      => "osf_portfolio",
                'tax_query'      => array(
                    array(
                        'taxonomy' => "osf_portfolio_category",
                        'field'    => 'term_id',
                        'terms'    => $term_id,
                        'operator' => 'IN'
                    )
                )
            );
        }

        return new WP_Query($args);
    }

    /**
     * @param $term_id is term_id in taxonomy
     * @param $post    is name post type
     * @param taxonomy  is name taxonomy
     */
    public static function get_portfolio($per_page = -1) {
        wp_reset_query();
        $args = array(
            'posts_per_page' => $per_page,
            'post_type'      => "osf_portfolio",
        );

        return new WP_Query($args);
    }

    public function create_meta_box() {

        $cmb2 = new_cmb2_box(array(
            'id'           => $this->prefix . 'info',
            'title'        => __('Info', 'editech-core'),
            'object_types' => array($this->post_type), // Post type
            'context'      => 'normal',
            'priority'     => 'high',
            'show_names'   => true, // Show field names on the left
        ));
        $cmb2->add_field(array(
            'name'       => __('Description', 'editech-core'),
            'id'         => $this->prefix . 'description',
            'type'       => 'textarea',
            'attributes' => array(
                'placeholder' => 'Your Description ',
            ),
        ));
        $cmb2->add_field(array(
            'name'       => __('Client', 'editech-core'),
            'id'         => $this->prefix . 'client',
            'type'       => 'text',
            'attributes' => array(
                'placeholder' => 'Your Specialist ',
            ),
        ));
        $cmb2->add_field(array(
            'name'       => __('Date', 'editech-core'),
            'id'         => $this->prefix . 'date',
            'type'       => 'text_date',
            'attributes' => array(
                'placeholder' => '02/17/2021',
            ),
        ));
        $cmb2->add_field(array(
            'name'       => __('Website', 'editech-core'),
            'id'         => $this->prefix . 'website',
            'type'       => 'text_url',
            'attributes' => array(
                'placeholder' => 'https://your-website.com',
            ),
        ));
        $cmb2->add_field(array(
            'name'       => __('Location', 'editech-core'),
            'id'         => $this->prefix . 'location',
            'type'       => 'text',
            'attributes' => array(
                'placeholder' => 'Location',
            ),
        ));
    }

    /**
     *
     * @param $post is name post type
     * @param taxonomy  is name taxonomy
     */
    public static function get_the_term_filter_name($post, $taxonomy_name) {
        $terms = wp_get_post_terms($post->ID, $taxonomy_name, array("fields" => "names"));

        return $terms;
    }

    /**
     * Get Categories Post ID
     *
     * @param $args
     */
    public function get_term_portfolio($id) {
        $categories_list = get_the_term_list($id, $this->taxonomy, '', apply_filters('portfolio-separate-meta', '/'), '');

        if ($categories_list) {
            // Make sure there's more than one category before displaying.
            echo '<span class="portfolio-cat-links"><span class="screen-reader-text">' . esc_html__('Categories', 'editech-core') . '</span>' . $categories_list . '</span>';
        }
    }

    /**
     * Get All Categories
     *
     * @param $args
     */
    public static function getCategory($per_page = 0) {
        $args  = array(
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
            'number'     => $per_page,
        );
        $terms = get_terms('osf_portfolio_category', $args);

        return $terms;
    }

    /**
     * @param $term_id is term_id in taxonomy
     * @param $post_id is id post type
     */
    public static function check_active_category_by_post_id($term_id, $post_id) {
        $termid = array();
        $terms  = wp_get_post_terms($post_id, 'osf_portfolio_category');
        foreach ($terms as $term) {
            $termid[] = $term->term_id;
        }
        if (in_array($term_id, $termid)) {
            return true;
        }

        return false;
    }

    public function widgets_init() {
        register_sidebar(array(
            'name'          => esc_html__('Portfolio Sidebar', 'editech-core'),
            'id'            => 'sidebar-portfolio',
            'description'   => esc_html__('Add widgets here to appear in your Portfolio.', 'editech-core'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        ));
    }

    function call_taxonomy_template_from_directory() {
        global $post;
        $taxonomy_slug = get_query_var('taxonomy');
        load_template(get_template_directory() . "taxonomy-$taxonomy_slug.php");
    }

    public function set_sidebar($name) {
        if (is_singular('osf_portfolio') && is_active_sidebar('sidebar-portfolio')) {
            $name = 'sidebar-portfolio';
        }
        return $name;
    }

    public function body_class($classes) {
        if (is_post_type_archive($this->post_type) || is_tax($this->taxonomy)) {
            if (in_array('opal-content-layout-2cr', $classes)) {
                $key           = array_search('opal-content-layout-2cr', $classes);
                $classes[$key] = 'opal-content-layout-1c';
            }
        }
        if (is_singular($this->post_type) && is_active_sidebar('sidebar-portfolio')) {

            $classes[] = 'opal-portfolio-layout-2cr';
        }

        return $classes;
    }

    public function get_page_template_file($template) {
        if (is_singular($this->post_type)) {
            $template = locate_template('single-portfolio.php') ? locate_template('single-portfolio.php') : trailingslashit(EDITECH_CORE_PLUGIN_DIR) . 'templates/portfolio/single-portfolio.php';
        } elseif (is_post_type_archive($this->post_type) || is_tax($this->taxonomy)) {
            $template = locate_template('archive-portfolio.php') ? locate_template('archive-portfolio.php') : trailingslashit(EDITECH_CORE_PLUGIN_DIR) . 'templates/portfolio/archive-portfolio.php';
        }
        return $template;
    }

}// end class
OSF_Custom_Post_Type_Portfolio::getInstance();