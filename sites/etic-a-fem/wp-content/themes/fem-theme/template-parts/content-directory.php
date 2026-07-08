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
	array('col', 'col-12', 'col-sm-6', 'col-lg-4', 'mb-4', 'pt-4') : array('col', 'col-12', 'col-sm-10', 'col-lg-8');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($colClasses); ?>>
	<div class="entry-content text-secondary mb-4 w-100">
		<?php
		if (is_single()) :
			the_title('<h1 class="entry-title text-dark h2">', '</h1>');
		else :
			the_title(
				'<h3 class="entry-title h4 font-weight-bold">
          <a href="' . get_permalink() . '"
            title="' . the_title_attribute('echo=0') . '"
            class="text-dark"
            rel="bookmark">',
				'</a></h3>'
			);
		endif;

		?>
		<?php
		the_content();
		$site = get_post_meta($post->ID, 'site', true);
		if ($site) : ?>
			<a href="<?php echo $site ?>" class="btn btn-outline-secondary" target="_blank">VER SITIO</a>
		<?php endif;
		wp_link_pages(array(
			'before' => '<div class="page-links">' . esc_html__('Pages:', 'wp-bootstrap-starter'),
			'after'  => '</div>',
		));
		?>
	</div><!-- .entry-content -->
	<footer class="entry-footer w-100">
		<div class="text-nowrap text-ellipsis">
			<?php wp_bootstrap_starter_entry_footer(); ?>
		</div>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
