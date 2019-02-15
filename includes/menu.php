<?php
function super_wtyka_create_posttype()
{
    register_post_type( 'events',
        array(
            'labels' => array(
                'name' => __( 'Events' ),
                'singular_name' => __( ' Event' )
            ),
            'public' => true,
            'has_archive' => true,
            //'rewrite' => array('slug' => 'events'),
            'supports' => array( 'title', 'editor', 'excerpt'),
            'register_meta_box_cb' => 'super_wtyka_metaboxes'
        )
    );
}
 
add_action( 'init', 'super_wtyka_create_posttype' );
 

function super_wtyka_metaboxes()
{
    add_meta_box('super_wtyka_events_location', 'Event Location', 'super_wtyka_events_location', 'events', 'normal', 'default');
    add_meta_box('super_wtyka_events_date', 'Event Date', 'super_wtyka_events_date', 'events', 'side', 'default');
    add_meta_box('super_wtyka_events_time', 'Event Time', 'super_wtyka_events_time', 'events', 'side', 'default');
}

function super_wtyka_events_location()
{
    global $post;
    $value = get_post_meta($post->ID, '_location', true);
    echo '<input type="text" name="_location" value="'. $value . '" class="widefat" />';
}

function super_wtyka_events_date()
{
    global $post;
    $value = get_post_meta($post->ID, '_date', true);
    echo '<input type="date" name="_date" value="' . $value . '" class="widefat" />';
}

function super_wtyka_events_time()
{
    global $post;
    $value = get_post_meta($post->ID, '_time', true);
    echo '<input type="time" name="_time" value="' . $value . '" class="widegaat" />';
    // wp_nonce_field - Tworzy ukryty input, który pozwala nam upewnić się, że żadanie zapisu pochodzi z obecnej strony. 
    // Korzystanie z tego zabezpieczenia jest bardzo ważne.
    // Zabezpieczenie obejmuje WSZYSTKIE pola, nie tylko _time.
    wp_nonce_field( plugin_basename(__FILE__), 'eventmeta_field' );
}

function super_wtyka_save_meta($post_id, $post) 
{
    if(isset($_POST['eventmeta_field'])) {

        // sprawdzenie upoważnienia od zapisywania danych
        if(!wp_verify_nonce($_POST['eventmeta_field'], plugin_basename(__FILE__))) {
            return $post->ID;
        }

        // sprawdzenie czy użytkownik może edytować post
        if (!current_user_can('edit_post', $post->ID)) return $post->ID;

        // dodanie wartości do tablicy
        $events_meta['_location'] = $_POST['_location'];
        $events_meta['_date'] = $_POST['_date'];
        $events_meta['_time'] = $_POST['_time'];

        
        foreach($events_meta as $key => $value) {   //  pętla przechodząca przez tablicę
            if($post->post_type == 'revision') return;  //  uniknięcie podwójnego zapisania
            if(get_post_meta($post->ID, $key, FALSE)) {    //  aktualizacja pola, jeżeli posiada już jakąś wartość
                update_post_meta($post->ID, $key, $value);
            }
            else {  //  tworzenie wartości w polu, jeśli nic nie ma
                add_post_meta($post->ID, $key, $value);
            }

            if(!$value) delete_post_meta($post->ID, $key);  //  usuwanie pola, jeżeli wartość jest pusta
        }
    }
}

add_action('save_post', 'super_wtyka_save_meta', 1, 2);


function super_wtyka_add_settings_page() 
{
    add_submenu_page('edit.php?post_type=events', 'Super Wtyka - Settings', 'Settings', 'manage_options', 
                    'super_wtyka_settings', 'super_wtyka_settings_content');
}

add_action('admin_menu', 'super_wtyka_add_settings_page');