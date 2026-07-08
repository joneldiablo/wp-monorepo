<?php

/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>

<section id="primary" class="content-area row justify-content-center no-gutters">
  <main id="main" class="site-main col col-12" role="main">

    <?php
    if (have_posts()) : ?>

      <header class="page-header text-center p-4">
        <h1 class="h3 text-red">
          <?php
            echo post_type_archive_title('', false);
            ?>
        </h1>
        <p>
          <?php
            echo get_the_post_type_description();
            ?>
        </p>
      </header><!-- .page-header -->
      <div class="container">
        <div class="row justify-content-center">
          <?php
            /* Start the Loop */
            while (have_posts()) : the_post();

              /*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
              get_template_part('template-parts/content', get_post_format());

            endwhile;
            ?>
        </div>
      </div>
    <?php
      the_posts_navigation();

    else :

      get_template_part('template-parts/content', 'none');

    endif; ?>

  </main><!-- #main -->
</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
