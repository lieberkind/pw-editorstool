<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Patchwork Redaktørværktøj</title>
  <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/stylesheets/screen.css">

  <?php wp_head(); ?>
</head>
<body>
  <header class="page-header-container">
    <div class="page-header">
      <img src="<?php bloginfo('template_url'); ?>/img/patchwork_logo.png" class="patchwork-logo">
      <?php

        wp_nav_menu(array(
          'theme_location'  => 'primary_navigation',
          'container'       => 'nav',
          'container_class' => 'primary-navigation-container',
          'menu_class'      => 'primary-navigation' 
        ));
      ?>
    </div>
  </header>
  <div class="page-wrapper">
    <div class="container">