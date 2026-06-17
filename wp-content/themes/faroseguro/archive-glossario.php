<?php get_header(); ?>

<main class="fs-archive fs-archive--glossario">
  <div class="fs-archive__hero">
    <div class="container">
      <span class="fs-eyebrow">Referência</span>
      <h1 class="fs-archive__title">Glossário de Segurança Financeira</h1>
      <p class="fs-archive__desc">Definições claras dos termos técnicos usados no universo de golpes, fraudes bancárias e segurança digital.</p>
    </div>
  </div>

  <div class="container fs-archive__body">

    <!-- Índice alfabético -->
    <div class="fs-alpha-index">
      <?php
      $letters = range('A', 'Z');
      foreach ($letters as $l) echo "<a href=\"#letra-{$l}\" class=\"fs-alpha-index__letter\">{$l}</a>";
      ?>
    </div>

    <?php
    $all = get_posts(['post_type' => 'glossario', 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    $grouped = [];
    foreach ($all as $p) {
      $first = strtoupper(mb_substr($p->post_title, 0, 1));
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
                <a href="<?php echo get_permalink($t); ?>"><?php echo esc_html($t->post_title); ?></a>
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
</main>

<?php get_footer(); ?>
