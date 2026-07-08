<?php
$the_slug = 'directorio';
$args = array(
	'name'        => $the_slug,
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1
);
$directory = get_posts($args);
$id = intval($directory->ID);
// debería de usar $id pero no se almacena correctamente o.O
$count = get_post_meta(121, 'counter', true);
$count = $count ? $count : 0;
update_post_meta(121, 'counter', ++$count);
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */

get_header(); ?>

<section id="primary" class="content-area">
	<header class="page-header text-center p-4 bg-primary mb-4">
		<?php
		$postTypeObj = get_post_type_object('directory');
		?>
		<h1 class="h3 text-info">
			<?php echo $postTypeObj->labels->name; ?>
		</h1>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col col-12 col-md-6 text-white">
					<p>
						Haz la búsqueda de los distintos hospitales IMSS, ISSSTE, Institutos y Asociaciones que pueden orientarte a tu atención médica.
					</p>
				</div>
			</div>
		</div>
	</header><!-- .page-header -->
	<main id="main" class="site-main container" role="main">
		<div class="row">
			<div class="col col-12 col-md-6 filters-container">
				<div id="filter-anchor" style="position:relative; top: -160px;"></div>
				<form action="#filter-anchor">
					<input class="form-control" id="search" name="search" type="text" placeholder="Escribe delegación o nombre de institución" title="Escribe la delegación o parte del nombre de la institución" value="<?php echo $_GET['search'] ?>">
				</form>
			</div>
			<div class="w-100"></div>
			<?php
			if (have_posts()) : ?>

			<?php
				/* Start the Loop */
				while (have_posts()) : the_post();

					/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
					get_template_part('template-parts/content', 'directory');

				endwhile;

				the_posts_navigation();

			else :

				get_template_part('template-parts/content', 'none');

			endif; ?>
		</div>
	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_sidebar();
get_footer();
//NOT uncomment!!!! generate directory fields
//include get_theme_file_path() . '/template/instituciones.php';
