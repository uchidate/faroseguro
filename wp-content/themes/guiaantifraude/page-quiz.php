<?php
/*
 * Template Name: Quiz — Golpe ou Fraude?
 */
get_header(); ?>

<main class="fs-quiz-page">
  <div class="container">
    <div class="fs-quiz-wrap">

      <div class="fs-quiz-intro" id="fs-quiz-intro">
        <span class="fs-eyebrow">Diagnóstico rápido</span>
        <h1>O que aconteceu com você?</h1>
        <p>Responda 3 perguntas e saiba se você foi vítima de um <strong>golpe</strong> ou <strong>fraude</strong> — e o que fazer agora.</p>
        <button class="fs-btn fs-btn--primary fs-btn--lg" id="fs-quiz-start">Começar diagnóstico</button>
      </div>

      <div class="fs-quiz" id="fs-quiz" hidden>
        <div class="fs-quiz__steps" aria-label="Progresso">
          <div class="fs-quiz__step-bar"><div class="fs-quiz__step-fill" id="fs-quiz-fill"></div></div>
          <span class="fs-quiz__step-label" id="fs-quiz-step-label">Pergunta 1 de 3</span>
        </div>

        <!-- Q1 -->
        <div class="fs-quiz__question" data-q="1" data-active="true">
          <h2>Como você descobriu o problema?</h2>
          <div class="fs-quiz__options">
            <button class="fs-quiz__opt" data-value="mensagem">Recebi uma mensagem, ligação ou e-mail</button>
            <button class="fs-quiz__opt" data-value="extrato">Vi no extrato bancário sem ter feito nada</button>
            <button class="fs-quiz__opt" data-value="bloqueio">Meu acesso a uma conta foi bloqueado</button>
            <button class="fs-quiz__opt" data-value="cobranca">Chegou cobrança de dívida que não reconheço</button>
          </div>
        </div>

        <!-- Q2 -->
        <div class="fs-quiz__question" data-q="2">
          <h2>Você realizou alguma ação?</h2>
          <div class="fs-quiz__options">
            <button class="fs-quiz__opt" data-value="sim_transferencia">Sim — fiz uma transferência ou pagamento</button>
            <button class="fs-quiz__opt" data-value="sim_dados">Sim — forneci dados pessoais ou senhas</button>
            <button class="fs-quiz__opt" data-value="sim_instalou">Sim — instalei um aplicativo ou cliquei em link</button>
            <button class="fs-quiz__opt" data-value="nao">Não fiz nada — aconteceu sem minha participação</button>
          </div>
        </div>

        <!-- Q3 -->
        <div class="fs-quiz__question" data-q="3">
          <h2>Qual foi o impacto percebido?</h2>
          <div class="fs-quiz__options">
            <button class="fs-quiz__opt" data-value="dinheiro">Perdi dinheiro ou fiz pagamento indevido</button>
            <button class="fs-quiz__opt" data-value="dados">Meus dados foram usados sem autorização</button>
            <button class="fs-quiz__opt" data-value="acesso">Perdi acesso a contas ou dispositivos</button>
            <button class="fs-quiz__opt" data-value="credito">Apareceu dívida ou crédito em meu nome</button>
          </div>
        </div>

        <div class="fs-quiz__nav">
          <button class="fs-btn fs-btn--ghost" id="fs-quiz-back" hidden>Voltar</button>
        </div>
      </div>

      <!-- Resultado -->
      <div class="fs-quiz-result" id="fs-quiz-result" hidden>
        <div class="fs-quiz-result__icon" id="fs-quiz-result-icon"></div>
        <h2 id="fs-quiz-result-title"></h2>
        <p id="fs-quiz-result-desc"></p>
        <div class="fs-quiz-result__links" id="fs-quiz-result-links"></div>
        <button class="fs-btn fs-btn--ghost" id="fs-quiz-restart">Refazer diagnóstico</button>
      </div>

    </div>
  </div>
</main>

<?php get_footer(); ?>
