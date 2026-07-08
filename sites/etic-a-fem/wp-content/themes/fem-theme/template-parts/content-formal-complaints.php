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
	array('col', 'col-6', 'col-sm-4', 'col-lg-3') : array('col', 'col-12', 'col-sm-10', 'col-lg-8');
?>
<article id="post-<?php the_ID(); ?>" <?php post_class($colClasses); ?>>
	<div class="w-100 mb-3 container-square bg-secondary rounded-circle border">
		<div class="post-thumbnail">
			<a href="<?php echo get_post_meta($post->ID, 'external-link', true) ?>" target="_blank" rel="noopener noreferrer">
				<?php if (has_post_thumbnail()) : ?>
					<?php the_post_thumbnail(null, array('class' => 'obj-fit-cover rounded-circle h-100')); ?>
				<?php else : ?>
					<img src="<?php echo (get_stylesheet_directory_uri() . '/assets/images/Fondo-Escaparate-Home.jpg') ?>" class="obj-fit-cover rounded-circle h-100" style="filter: grayscale(1);" alt="<?php the_title() ?>" />
				<?php endif; ?>
			</a>
		</div>
	</div><!-- .entry-->
	<footer class="entry-footer w-100">
		<div class="text-nowrap text-ellipsis">
			<?php wp_bootstrap_starter_entry_footer(); ?>
		</div>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
