<?php
include get_theme_file_path() . '/vendor/autoload.php';
$id = get_the_ID();
$count = get_post_meta($id, 'counter', true);
$count = $count ? $count : 0;
update_post_meta($id, 'counter', ++$count);
/**
 * Template Name: Home
 */

get_header(); ?>
<div class="row no-gutters">
	<section id="primary" class="content-area col-sm-12">
		<main id="main" class="site-main" role="main">
			<section id="form-container" class="bg-primary mb-4">
				<div class="container">
					<div class="row justify-content-center">
						<div class="col col-12 col-xl-10 text-white pt-4">
							<?php
							while (have_posts()) : the_post();

								get_template_part('template-parts/content', 'home');

							endwhile; // End of the loop.
							?>
							<?php
							$json_content = file_get_contents(get_theme_file_path() . '/denuncia.json');
							$json_data = json_decode($json_content, true);
							$pug = new Pug();
							$pug->displayFile(get_theme_file_path() . '/template/pug/denuncia-form-public.pug', [
								'site' => [
									'data' => ['schema' => $json_data],
									'home' => get_home_url(),
									'assets' => get_stylesheet_directory_uri() . '/assets/'
								]
							]);
							?>
						</div>
					</div>
				</div>
			</section>
			<section>
				<div id='filters-anchor' style="position: relative; top: -160px;"></div>
				<h2 class="text-red text-center">Al que no habla, Dios no lo oye</h2>
				<div class="row justify-content-center">
					<div class="col col-12 col-md-6 offset-md-6">
						<p class="text-center text-red" style="font-size: 20px">
							<?php echo wp_count_posts('complaint')->publish; ?> casos de reportados
						</p>
					</div>
				</div>
				<p class="text-center">Conoce las historias de los demás usuarios</p>
				<div class="filters-container container">
					<form method="GET" action="#filters-anchor">
						<div class="row">
							<?php
							$filters_content = file_get_contents(get_theme_file_path() . '/filters.json');
							$filters_data = json_decode($filters_content, true);
							$pug->displayFile(
								get_theme_file_path() . '/template/pug/filters.pug',
								['site' => [
									'data' => ['schema' => $filters_data, 'data' => $_GET],
									'home' => get_home_url(),
									'assets' => get_stylesheet_directory_uri() . '/assets/'
								]]
							);
							$meta = array('relation' => 'AND');
							if (($_GET['problem'])) {
								array_push($meta, array(
									'key' => 'problem',
									'value' => $_GET['problem']
								));
							}
							if (($_GET['instance'])) {
								array_push($meta, array(
									'key' => 'instance',
									'value' => $_GET['instance']
								));
							}
							if (($_GET['service'])) {
								array_push($meta, array(
									'key' => 'service',
									'value' => $_GET['service']
								));
							}
							$loop = new WP_Query(array(
								'post_type' => 'complaint',
								'post_status' => 'publish',
								'posts_per_page' => 6,
								's' => $_GET['search'],
								'meta_query' => $meta
							));
							?>
						</div>
					</form>
				</div>
				<div class="post-type-complaint container">
					<div class="row">
						<?php
						while ($loop->have_posts()) : $loop->the_post();
							get_template_part('template-parts/content', get_post_format());
						endwhile; ?>
					</div>
					<p class="see-more text-right">
						<a href="./denuncias" class="text-dark ">Ver más...</a>
					</p>
				</div>
			</section>
		</main><!-- #main -->
	</section><!-- #primary -->
</div><!-- #row -->
<?php

get_footer();
