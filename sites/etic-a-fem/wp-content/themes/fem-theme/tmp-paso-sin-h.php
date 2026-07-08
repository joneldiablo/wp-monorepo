<?php
$id = get_the_ID();
$count = get_post_meta($id, 'counter', true);
$count = $count ? $count : 0;
update_post_meta($id, 'counter', ++$count);
/**
 * Template Name: No des paso sin huarache
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
		$postTypes = array('normativity', 'consultation', 'health-system');
		foreach ($postTypes as $postType) : ?>
			<section class="post-type-<?php echo $postType ?> container owl-carousel-in">
				<header class="page-header text-center p-4">
					<h1 class="h3 text-red">
						<?php
						$postTypeObj = get_post_type_object($postType);
						echo $postTypeObj->labels->name;
						?>
					</h1>
				</header><!-- .page-header -->
				<div class="row owl-carousel">
					<?php $loop = new WP_Query(array('post_type' => $postType, 'posts_per_page' => 9)); ?>
					<?php while ($loop->have_posts()) : $loop->the_post();
						get_template_part('template-parts/content', $postType);
					?>
					<?php endwhile; ?>
				</div>
				<p class="see-more text-right">
					<a href="<?php echo get_option('siteurl') ?>/normatividad" class="text-dark ">Ver más...</a>
				</p>
			</section>
		<?php endforeach; ?>
	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
