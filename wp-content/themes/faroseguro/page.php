<?php get_header(); ?>

<main class="container">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article id="page-<?php the_ID(); ?>" <?php post_class(); ?> >
        <header class="entry-header">
          <h1 class="entry-title"><?php the_title(); ?></h1>
        </header>
        <div class="entry-content">
          <?php the_content(); ?>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else : ?>
    <section class="no-results not-found">
      <h2>Nenhum conteúdo encontrado</h2>
      <p>Verifique se a página existe e tente novamente.</p>
    </section>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
