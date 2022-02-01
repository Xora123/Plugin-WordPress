<?php

class MenuPage {

    const GROUP = "plugin_options";
    public static function register(){
        add_action('admin_menu', [self::class, 'addMenu']);
        add_action('admin_init', [self::class, 'registerSettings']);
    }

    public static function registerSettings(){
        register_setting(self::GROUP, 'age', ['default' => 260]);
        add_settings_section('plugin_option_section', 'Paramètres', function(){

        }, self::GROUP);
        add_settings_field('plugion_options_mots', "Nombre de mot lu par minute", function(){
            ?>
            <input type="number" id="age" name="age" required
       minlength="4" maxlength="8" size="10">
            <?php

        }, self::GROUP, 'plugin_option_section');
    }
    public static function addMenu(){
        add_options_page("Gestion du plugin", "Modification", "manage_options", "plugin_options", [self::class, 'render']);
    }

     public static function render(){
        ?>
          <h1> Menu de Gestion </h1>

        <form action="options.php" methode="post">
            <?= get_option('age') ?>
            <?php 
            settings_fields(self::GROUP);
            do_settings_sections(self::GROUP);
            submit_button();
             ?> 

       </form>
       <?php
     }
 }