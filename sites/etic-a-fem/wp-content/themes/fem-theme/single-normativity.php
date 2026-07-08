<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>

<section id="primary" class="content-area">
  <main id="main" class="site-main" role="main">
    <header class="page-header text-center p-4 text-light bg-primary">
      <h2 class="text-info text-center h2">
        <?php
        $pasoSinH = get_page_by_path('no-des-paso-sin-huarache');
        echo get_the_title($pasoSinH);
        ?>
      </h2>
      <div class="row justify-content-center no-gutters">
        <div class="col col-12 col-sm-8 col-lg-6">
          <?php
          echo $pasoSinH->post_content;
          ?>
        </div>
      </div>
    </header><!-- .page-header -->
    <div class="row justify-content-center">
      <?php
      while (have_posts()) : the_post();

        get_template_part('template-parts/content', get_post_format());
        ?>
        <div class="col col-12 col-sm-10 col-lg-8 py-4">
          <hr>
          <?php
            //the_post_navigation();
            // If comments are open or we have at least one comment, load up the comment template.
            if (comments_open() || get_comments_number()) :
              comments_template();
            endif;
            ?>
        </div>
      <?php
      endwhile; // End of the loop.
      ?>
    </div>
  </main><!-- #main -->
</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
