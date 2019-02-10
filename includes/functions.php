<?php

function super_wtyka_widget_content() {
    $loop = new WP_Query( array( 'post_type' => 'events', 'posts_per_page' => -1) );
    while ( $loop->have_posts() ) : $loop->the_post();
        $id = get_the_ID();
        $date = strtotime(get_post_meta($id, "_date", true));
        $location = get_post_meta($id, "_location", true);
        $time = get_post_meta($id, "_time", true);
        $day = date('d', $date);
        $month = date('M', $date);

        ?>
        <a href="<?php the_permalink(); ?>">
            <div class="super-wtyka-widget" >
                <div class="wtykadate"><?php echo $day; ?><span><?php echo $month; ?> </span></div>
                <div class="super-wtyka-content">
                    <span class="super-wtyka-title"><?php the_title(); ?></span>
                    <span class="super-wtyka-hours"><?php echo $time; ?></span>
                    <span class="super-wtyka-location"><?php echo $location; ?></span>
                </div>
            </div>
        </a>

        <?php

    endwhile; wp_reset_query();

}


function super_wtyka_load_styles() 
{
    wp_enqueue_style('super_wtyka_widget_style', plugins_url('/super-wtyka/public/widget-style.css'));
}

add_action('wp_enqueue_scripts', 'super_wtyka_load_styles');