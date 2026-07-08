<?php

if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3')) { ?>
  <div id="footer-widget" class="row m-0 <?php if (!is_theme_preset_active()) {
                                              echo 'bg-dark text-white pt-4';
                                            } ?>">
    <div class="container">
      <div class="row justify-content-center">
        <?php wp_nav_menu(array(
            'theme_location' => 'footer-menu',
            'container_class' => 'container-footer-menu col col-12',
            'menu_class' => 'nav justify-content-center align-items-center nav-dark'
          )); ?>
        <?php if (is_active_sidebar('footer-1')) : ?>
          <div class="col col-12 col-sm-8 col-md-6 col-lg-4"><?php dynamic_sidebar('footer-1'); ?></div>
        <?php endif; ?>
        <?php if (is_active_sidebar('footer-2')) : ?>
          <div class="col col-12 col-sm-8 col-md-6 col-lg-4"><?php dynamic_sidebar('footer-2'); ?></div>
        <?php endif; ?>
        <?php if (is_active_sidebar('footer-3')) : ?>
          <div class="col col-12 col-sm-8 col-md-6 col-lg-4"><?php dynamic_sidebar('footer-3'); ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

<?php }
