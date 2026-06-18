<?php get_header(); ?>

<?php
fs_archive_hero(
    'Referência',
    'Glossário de Segurança Financeira',
    'Definições claras dos termos técnicos usados no universo de golpes, fraudes bancárias e segurança digital.',
    'dark'
);
?>

<div class="fs-archive__body">
  <div class="container">

    <div class="fs-alpha-index">
      <?php foreach (range('A', 'Z') as $l): ?>
        <a href="#letra-<?php echo $l; ?>" class="fs-alpha-index__letter"><?php echo $l; ?></a>
      <?php endforeach; ?>
    </div>

    <?php
    $all = get_posts(['post_type' => 'glossario', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    $grouped = [];
    foreach ($all as $p) {
        $first = strtoupper(mb_substr(fs_editorial_text($p->post_title), 0, 1));
        $grouped[$first][] = $p;
    }
    ?>

    <?php foreach ($grouped as $letter => $terms): ?>
      <section class="fs-glossario-group" id="letra-<?php echo $letter; ?>">
        <h2 class="fs-glossario-group__letter"><?php echo $letter; ?></h2>
        <div class="fs-glossario-terms">
          <?php foreach ($terms as $t): ?>
            <div class="fs-glossario-term">
              <h3 class="fs-glossario-term__title">
                <a href="<?php echo get_permalink($t); ?>"><?php echo esc_html(fs_editorial_text($t->post_title)); ?></a>
              </h3>
              <p class="fs-glossario-term__def"><?php echo wp_trim_words(get_the_excerpt($t), 25); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; wp_reset_postdata(); ?>

    <?php if (!$all): ?>
      <div class="fs-empty"><p>Nenhum termo cadastrado ainda.</p></div>
    <?php endif; ?>

  </div>
</div>

<?php get_footer(); ?>
