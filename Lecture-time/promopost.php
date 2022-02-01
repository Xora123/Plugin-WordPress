<?php
/*
    Plugin Name:  PromoPost
    Plugin URI:   https://www.tutowp.fr/tutowp
    Description:  Ajout d'un message promo à la fin de tous les posts WordPress
    Version:      1.0
    Author:       TutoWP
    Author URI:   https://www.tutowp.fr/tutowp
    */



defined('ABSPATH') or die('Oups !');

//--On applique le filtre pour l'afficher sur le content--//
add_filter('the_content', 'time_to_read_post');

//--On applique le filtre pour l'afficher sur le title--//
add_filter('the_title', 'time_to_read_post_title', 10, 2);

//--Function pour calculer le temps de lecture--//
function capitaine_reading_time($post_id, $post, $update)
{

    if (!$update) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    if (defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) {
        return;
    }
    if ($post->post_type != 'post') {
        return;
    }

    //--Calculer le temps de lecture--//
    $word_count = str_word_count(strip_tags($post->post_content));

    //--On prend comme base 250 mots par minute--//
    $minutes = ceil($word_count / 250);

    //--On sauvegarde la meta--//
    update_post_meta($post_id, 'reading_time', $minutes);
}

//--On ajoute dans la base de données "save_post" le temps de lecture--//
add_action('save_post', 'capitaine_reading_time', 10, 3);

//--Calcul et Affichage dans le titre de l'article--// 
function time_to_read_post($content)
{

    //--On appelle la variable global Wordpress id--//
    global $id;

    //--On applique ça sur chaque single post--//
    if (is_single()) {
        $time_to_read = get_post_meta($id, 'reading_time', true);
        if (empty($time_to_read)) {
            return $content;
        }
        //--Affichage--//
        $content = '<div class="estimated-time">Temps estimé pour la lecture de l article: ' . $time_to_read . ' min</div>' . $content;
    }

    return $content;
}

//--Calcul et Affichage dans le titre de la liste des blogs--//
function time_to_read_post_title($title, $post_id)
{
    //--On appelle la variable global Wordpress post--/
    global $post;

    //--On applique ça sur l'homepage et on calcul--/
    if (in_the_loop() && is_home() && $post->post_type == "post") {
        $time_to_read = get_post_meta($post_id, 'reading_time', true);
        if (empty($time_to_read)) {
            return $title;
        }
        //--Affichage--// 
        $title .= '<span>| ' . $time_to_read . ' min</span>';
    }

    return $title;
}


//--Function pour utiliser le css--//
function pp_style()
{
    wp_enqueue_style('promopost', plugin_dir_url(__FILE__) . '/promopost.css');
}
add_action('wp_enqueue_scripts', 'pp_style');


require_once('options/option.php');
MenuPage::register();