<?php

/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

?>
<?php
$colClasses = !is_single() ?
  array('col', 'col-12', 'col-sm-6', 'col-lg-4', 'd-flex align-items-start flex-column') : array('col', 'col-12', 'col-sm-10', 'col-lg-8', 'mb-5');
$evidence_file = get_post_meta($post->ID, 'evidence_file', true);
$file_path = wp_upload_dir()['basedir'] . $evidence_file;
$file = wp_upload_dir()['baseurl'] . $evidence_file;
if (file_exists($file_path)) {
  $mime = mime_content_type($file_path);
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($colClasses); ?>>
  <header class="entry-header w-100 mb-3">
    <?php if (is_single()) : ?>
      <p class="my-2"><a href="javascript:history.back()" class="text-reset"> &#60; Regresar</a></p>
    <?php endif; ?>
    <div class="post-thumbnail container-16-9 bg-secondary">
      <?php if (strpos($mime, 'video') !== false) : ?>
        <video controls preload="none" poster="<?php the_post_thumbnail_url() ?>">
          <source src="<?php echo $file ?>" type="<?php echo $mime ?>" />
        </video>
      <?php elseif (strpos($mime, 'image') !== false) : ?>
        <img src="<?php echo $file ?>" class="obj-fit-cover" />
      <?php elseif (has_post_thumbnail()) : ?>
        <?php the_post_thumbnail(null, array('class' => 'obj-fit-cover')); ?>
      <?php else : ?>
        <img src="<?php echo (get_stylesheet_directory_uri() . '/assets/images/Fondo-Escaparate-Home.jpg') ?>" class="obj-fit-cover" style="filter: grayscale(1);" />
      <?php endif; ?>
    </div>
  </header><!-- .entry-header -->
  <div class="entry-content text-secondary mb-auto w-100">
    <?php
    if (is_single()) :
      the_title('<h1 class="entry-title text-red h2">', '</h1>');
    else :
      the_title(
        '<h3 class="entry-title h4 font-weight-bold excerpt-title">
          <a href="' . get_permalink() . '" 
            title="' . the_title_attribute('echo=0') . '" 
            class="text-body"
            rel="bookmark">',
        '</a></h3>'
      );
    endif;

    if ('post' === get_post_type()) : ?>
      <div class="entry-meta">
        <?php wp_bootstrap_starter_posted_on(); ?>
      </div><!-- .entry-meta -->
    <?php
    endif; ?>
    <?php
    if (is_single()) :
      the_content();
    else :
      ?>
      <div class="excerpt text-justify">
        <?php
          the_excerpt();
          ?>
      </div>
    <?php
    endif;

    wp_link_pages(array(
      'before' => '<div class="page-links">' . esc_html__('Pages:', 'wp-bootstrap-starter'),
      'after'  => '</div>',
    ));
    ?>
  </div><!-- .entry-content -->
  <footer class="entry-footer w-100">
    <?php if (!is_single()) : ?>
      <hr>
      <div class="d-flex justify-content-between align-items-center">
        <div class="fb-share-button" data-href="<?php echo get_permalink(); ?>" data-layout="button_count" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Compartir</a></div>
        <p class="text-right px-1 mb-0">
          <span class="like-number"><?php echo (int) get_post_meta($id, 'likes', true); ?></span>
          <a href="#" class="btn-like"><i class="far fa-heart text-red"></i></a>
        </p>
      </div>
    <?php endif; ?>
    <div class="text-nowrap text-ellipsis">
      <?php wp_bootstrap_starter_entry_footer(); ?>
    </div>
    <?php if (!is_single()) : ?>
      <hr>
    <?php endif; ?>
  </footer><!-- .entry-footer -->
</article><!-- #post-## -->