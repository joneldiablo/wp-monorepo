<?php
$id = get_the_ID();
$count = get_post_meta($id, 'counter', true);
$count = $count ? $count : 0;
update_post_meta($id, 'counter', ++$count);
/**
 * Template Name: Atrévete a exigir
 */

get_header(); ?>

<section id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<header class="page-header text-center p-4 text-light bg-primary">
			<?php
			echo the_title('<h1 class="text-info text-center h2">', '</h1>');
			?>
			<div class="row justify-content-center no-gutters">
				<div class="col col-12 col-sm-8 col-lg-6">
					<?php
					while (have_posts()) : the_post();
						get_template_part('template-parts/content', 'notitle');
					endwhile; // End of the loop.
					?>
				</div>
			</div>
		</header><!-- .page-header -->
		<?php
		$postTypes = array('complaint-info', 'formal-complaints');
		foreach ($postTypes as $postType) : ?>
			<section class="post-type-<?php echo $postType ?> container owl-carousel-in">
				<header class="page-header text-center p-4">
					<?php
					$postTypeObj = get_post_type_object($postType);
					if ($postType === 'formal-complaints') : ?>
						<h1 class="h3 text-red">
							<?php echo $postTypeObj->labels->name; ?>
						</h1>
						<p>
							<?php echo $postTypeObj->description; ?>
						</p>
					<?php endif; ?>
				</header><!-- .page-header -->
				<div class="row owl-carousel">
					<?php $loop = new WP_Query(array('post_type' => $postType, 'posts_per_page' => 9)); ?>
					<?php while ($loop->have_posts()) : $loop->the_post();
						get_template_part('template-parts/content', $postType);
					?>
					<?php endwhile; ?>
				</div>
				<?php
				$post_type_data = get_post_type_object($postType);
				$post_type_slug = '/' . $post_type_data->rewrite['slug'];
				if ($postType !== 'formal-complaints') : ?>
					<p class="see-more text-right">
						<a href="<?php echo get_option('siteurl') . $post_type_slug ?>" class="text-dark ">Ver más...</a>
					</p>
				<?php endif; ?>
			</section>
		<?php endforeach; ?>
	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
