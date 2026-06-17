<?php get_header(); ?>

<main class="fs-page">
  <?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
      <article id="page-<?php the_ID(); ?>" <?php post_class('fs-page__article'); ?> >
        <header class="fs-page__hero">
          <div class="container--narrow">
            <p class="fs-eyebrow">Faro Seguro</p>
            <h1 class="fs-page__title"><?php the_title(); ?></h1>
            <?php if (has_excerpt()): ?>
              <p class="fs-page__lead"><?php the_excerpt(); ?></p>
            <?php endif; ?>
          </div>
        </header>
        <div class="container--narrow fs-page__body">
          <div class="fs-prose">
            <?php the_content(); ?>
          </div>
        </div>
      </article>
    <?php endwhile; ?>
  <?php else : ?>
    <section class="container fs-empty">
      <h1>Nenhum conteúdo encontrado</h1>
      <p>Verifique se a página existe e tente novamente.</p>
    </section>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
