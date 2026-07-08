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
    <div class="row justify-content-center">
      <?php
      while (have_posts()) : the_post();
        get_template_part('template-parts/content', get_post_format());
        //the_post_navigation();
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()) : ?>
          <div class="col col-12 col-sm-10 col-lg-8 py-4">
            <hr>
            <?php
                comments_template();
                ?>
          </div>
        <?php endif; ?>
    </div>
  <?php
  endwhile; // End of the loop.
  ?>
  </main><!-- #main -->
</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
