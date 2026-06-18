<?php
/**
 * Seed de conteúdo — Golpes e Fraudes
 *
 * Uso:
 *   wp eval-file scripts/seed-content.php
 *
 * O script é idempotente: verifica pelo post_name (slug) antes de inserir.
 * Rode quantas vezes quiser sem duplicar conteúdo.
 */

defined('ABSPATH') || define('ABSPATH', dirname(__FILE__) . '/');

/* ─────────────────────────────────────────
   Helpers
───────────────────────────────────────── */

/**
 * Insere um golpe se o slug ainda não existir.
 *
 * @param array $data {
 *   title, slug, excerpt, content,
 *   nivel_risco, prejuizo_estimado, novo_modus, fonte_referencia,
 *   como_age, sinais_alerta, como_se_proteger, o_que_fazer,
 *   tipo_golpe[], canal_golpe[], publico_alvo[]
 * }
 */
function seed_golpe(array $data): void {
    $exists = get_page_by_path($data['slug'], OBJECT, 'golpe');
    if ($exists) {
        WP_CLI::log("  [skip] golpe já existe: {$data['slug']}");
        return;
    }

    $post_id = wp_insert_post([
        'post_type'    => 'golpe',
        'post_status'  => 'publish',
        'post_title'   => $data['title'],
        'post_name'    => $data['slug'],
        'post_excerpt' => $data['excerpt'] ?? '',
        'post_content' => $data['content']  ?? '',
    ], true);

    if (is_wp_error($post_id)) {
        WP_CLI::warning("  [erro] {$data['slug']}: " . $post_id->get_error_message());
        return;
    }

    $meta_map = [
        'nivel_risco'       => $data['nivel_risco']       ?? 'alto',
        'prejuizo_estimado' => $data['prejuizo_estimado'] ?? '',
        'novo_modus'        => $data['novo_modus']        ?? '0',
        'fonte_referencia'  => $data['fonte_referencia']  ?? '',
        'como_age'          => $data['como_age']          ?? '',
        'sinais_alerta'     => $data['sinais_alerta']     ?? '',
        'como_se_proteger'  => $data['como_se_proteger']  ?? '',
        'o_que_fazer'       => $data['o_que_fazer']       ?? '',
    ];
    foreach ($meta_map as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    seed_terms($post_id, 'tipo_golpe',  $data['tipo_golpe']  ?? []);
    seed_terms($post_id, 'canal_golpe', $data['canal_golpe'] ?? []);
    seed_terms($post_id, 'publico_alvo',$data['publico_alvo'] ?? []);

    WP_CLI::success("  [ok] golpe inserido: {$data['slug']} (ID $post_id)");
}

/**
 * Insere uma fraude se o slug ainda não existir.
 *
 * @param array $data {
 *   title, slug, excerpt, content,
 *   nivel_risco, prejuizo_estimado, nova_tecnica, fonte_referencia,
 *   como_funciona, sinais_alerta, como_se_proteger, o_que_fazer,
 *   tipo_fraude[], canal_golpe[], publico_alvo[]
 * }
 */
function seed_fraude(array $data): void {
    $exists = get_page_by_path($data['slug'], OBJECT, 'fraude');
    if ($exists) {
        WP_CLI::log("  [skip] fraude já existe: {$data['slug']}");
        return;
    }

    $post_id = wp_insert_post([
        'post_type'    => 'fraude',
        'post_status'  => 'publish',
        'post_title'   => $data['title'],
        'post_name'    => $data['slug'],
        'post_excerpt' => $data['excerpt'] ?? '',
        'post_content' => $data['content']  ?? '',
    ], true);

    if (is_wp_error($post_id)) {
        WP_CLI::warning("  [erro] {$data['slug']}: " . $post_id->get_error_message());
        return;
    }

    $meta_map = [
        'nivel_risco'       => $data['nivel_risco']       ?? 'alto',
        'prejuizo_estimado' => $data['prejuizo_estimado'] ?? '',
        'nova_tecnica'      => $data['nova_tecnica']      ?? '0',
        'fonte_referencia'  => $data['fonte_referencia']  ?? '',
        'como_funciona'     => $data['como_funciona']     ?? '',
        'sinais_alerta'     => $data['sinais_alerta']     ?? '',
        'como_se_proteger'  => $data['como_se_proteger']  ?? '',
        'o_que_fazer'       => $data['o_que_fazer']       ?? '',
    ];
    foreach ($meta_map as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }

    seed_terms($post_id, 'tipo_fraude', $data['tipo_fraude'] ?? []);
    seed_terms($post_id, 'canal_golpe', $data['canal_golpe'] ?? []);
    seed_terms($post_id, 'publico_alvo',$data['publico_alvo'] ?? []);

    WP_CLI::success("  [ok] fraude inserida: {$data['slug']} (ID $post_id)");
}

/** Cria os termos se não existirem e vincula ao post. */
function seed_terms(int $post_id, string $taxonomy, array $term_names): void {
    if (empty($term_names)) return;
    $term_ids = [];
    foreach ($term_names as $name) {
        $term = term_exists($name, $taxonomy);
        if (!$term) {
            $term = wp_insert_term($name, $taxonomy);
        }
        if (!is_wp_error($term)) {
            $term_ids[] = (int) (is_array($term) ? $term['term_id'] : $term);
        }
    }
    if ($term_ids) {
        wp_set_post_terms($post_id, $term_ids, $taxonomy, true);
    }
}

/* ─────────────────────────────────────────
   GOLPES
───────────────────────────────────────── */

WP_CLI::log('');
WP_CLI::log('=== Inserindo Golpes ===');

seed_golpe([
    'title'             => 'Golpe do Falso Motoboy',
    'slug'              => 'golpe-do-falso-motoboy',
    'excerpt'           => 'Criminoso liga se passando por banco, alega fraude no cartão e manda um motoboy buscar o cartão físico na casa da vítima.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 2.000 – R$ 20.000',
    'novo_modus'        => '0',
    'como_age'          => 'O golpista liga se passando por atendente do banco alegando que detectou uma compra suspeita no cartão da vítima. Orienta que o cartão deve ser cancelado e substituído com urgência. Para "facilitar", oferece enviar um motoboy para recolher o cartão em casa. A vítima entrega o cartão e, em seguida, o criminoso usa o cartão físico para saques e compras.',
    'sinais_alerta'     => "Ligação não solicitada pedindo seu cartão físico\nBanco nunca manda buscar cartão em domicílio\nUrgência excessiva e pressão psicológica\nPede que você não desligue o telefone enquanto espera o motoboy",
    'como_se_proteger'  => "Nunca entregue seu cartão a ninguém — nem a um motoboy\nDesligue e ligue para o número oficial do seu banco\nSe desconfiar, bloqueie o cartão pelo app imediatamente\nO banco NUNCA solicita recolhimento de cartão",
    'o_que_fazer'       => "Ligue para o banco e bloqueie todos os cartões\nRegistre Boletim de Ocorrência (Delegacia Digital ou presencial)\nSolicite estorno de transações não reconhecidas\nGuarde todos os comprovantes de contato",
    'tipo_golpe'        => ['Engenharia Social', 'Golpe Bancário'],
    'canal_golpe'       => ['Telefone'],
    'publico_alvo'      => ['Idosos', 'Correntistas'],
]);

seed_golpe([
    'title'             => 'Golpe do Falso Suporte Técnico',
    'slug'              => 'golpe-do-falso-suporte-tecnico',
    'excerpt'           => 'Criminoso finge ser do suporte do banco ou de empresa de tecnologia e convence a vítima a instalar um aplicativo de acesso remoto.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 3.000 – R$ 50.000',
    'novo_modus'        => '0',
    'como_age'          => 'A vítima recebe uma ligação ou mensagem informando que sua conta foi hackeada ou que há vírus no celular. O "suporte" orienta a instalar um aplicativo de acesso remoto (AnyDesk, TeamViewer ou similar). Com acesso ao dispositivo, o criminoso realiza transferências Pix, pede empréstimos e drena a conta sem que a vítima perceba em tempo real.',
    'sinais_alerta'     => "Pedido para instalar aplicativo de acesso remoto\nMensagem ou ligação não solicitada sobre segurança da conta\nSolicitação de senhas ou códigos de autenticação\nPressão para agir com rapidez sem consultar ninguém",
    'como_se_proteger'  => "Nunca instale aplicativos por pedido de ligações não solicitadas\nNenhum banco pede acesso remoto ao seu celular\nDesconfie de urgência extrema — desligue e ligue de volta pelo número oficial\nNão compartilhe senhas ou tokens de autenticação",
    'o_que_fazer'       => "Desinstale imediatamente o aplicativo de acesso remoto\nContate o banco e bloqueie a conta\nTroque todas as senhas do dispositivo\nRegistre Boletim de Ocorrência e comunique ao banco",
    'tipo_golpe'        => ['Engenharia Social', 'Acesso Remoto'],
    'canal_golpe'       => ['Telefone', 'WhatsApp'],
    'publico_alvo'      => ['Correntistas', 'Idosos'],
]);

seed_golpe([
    'title'             => 'Golpe do Pix com Comprovante Falso',
    'slug'              => 'golpe-do-pix-comprovante-falso',
    'excerpt'           => 'Golpista envia um comprovante Pix falso ou adulterado para simular que o pagamento foi feito, sem que o dinheiro de fato caia na conta.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 500 – R$ 15.000',
    'novo_modus'        => '0',
    'como_age'          => 'O golpista negocia a compra de um produto ou serviço e envia um comprovante Pix falsificado ou editado digitalmente. A vítima, sem conferir o saldo, libera o produto ou serviço. O dinheiro nunca foi depositado.',
    'sinais_alerta'     => "Comprovante com layout diferente do seu banco\nNome do pagador diferente do acordado\nPressão para liberar produto antes de confirmar recebimento\nHorário de transação fora do comum",
    'como_se_proteger'  => "Sempre confira o saldo ou extrato antes de liberar qualquer produto\nNunca confie apenas no print — acesse o extrato pelo app\nSe vender online, aguarde a confirmação real no extrato\nDesconfie de comprovantes enviados por print de tela",
    'o_que_fazer'       => "Não libere o produto ou serviço enquanto não confirmar no extrato\nRegistre Boletim de Ocorrência\nSe já liberou, entre em contato com o banco e registre a fraude\nGuarde todas as conversas e o comprovante falso como prova",
    'tipo_golpe'        => ['Golpe de Pagamento', 'Fraude em Compra e Venda'],
    'canal_golpe'       => ['WhatsApp', 'Redes Sociais'],
    'publico_alvo'      => ['Vendedores Online', 'Autônomos'],
]);

seed_golpe([
    'title'             => 'Golpe do Emprego Falso',
    'slug'              => 'golpe-do-emprego-falso',
    'excerpt'           => 'Vaga de emprego inexistente é usada para coletar documentos pessoais da vítima ou cobrar taxas indevidas de cadastro e exames.',
    'nivel_risco'       => 'medio',
    'prejuizo_estimado' => 'R$ 200 – R$ 2.000 + roubo de identidade',
    'novo_modus'        => '0',
    'como_age'          => 'Anúncios falsos de emprego são publicados em plataformas legítimas ou redes sociais com salários atrativos e condições excelentes. Durante o processo seletivo, a vítima é solicitada a enviar documentos (RG, CPF, foto com documento) e pagar taxas para exame médico, cadastro ou uniforme. Os dados são usados para fraudes de identidade.',
    'sinais_alerta'     => "Processo seletivo sem entrevista presencial ou por vídeo\nPedido de pagamento para exame, uniforme ou cadastro\nSalário acima da média sem exigência de experiência\nVaga com contato apenas por WhatsApp",
    'como_se_proteger'  => "Nunca pague para conseguir emprego\nVerifique o CNPJ da empresa no site da Receita Federal\nNão envie documentos antes de uma entrevista confirmada\nPesquise o nome da empresa + 'golpe' ou 'reclamação'",
    'o_que_fazer'       => "Não envie mais documentos ou dinheiro\nRegistre Boletim de Ocorrência\nDenuncie a vaga na plataforma onde foi anunciada\nMonitore seu CPF no Serasa e Boa Vista",
    'tipo_golpe'        => ['Golpe de Identidade', 'Fraude em Recrutamento'],
    'canal_golpe'       => ['Redes Sociais', 'Sites de Emprego', 'WhatsApp'],
    'publico_alvo'      => ['Desempregados', 'Jovens'],
]);

seed_golpe([
    'title'             => 'Golpe do Amor (Romance Scam)',
    'slug'              => 'golpe-do-amor-romance-scam',
    'excerpt'           => 'Criminoso cria um perfil falso em aplicativos de relacionamento ou redes sociais, conquista a confiança emocional da vítima ao longo de semanas e depois pede dinheiro.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 5.000 – R$ 200.000',
    'novo_modus'        => '0',
    'como_age'          => 'O golpista cria um perfil atraente com fotos roubadas de terceiros. Inicia contato em aplicativos de relacionamento, redes sociais ou até grupos de WhatsApp. Constrói um relacionamento emocional intenso ao longo de semanas ou meses, evitando videochamadas com desculpas. Depois apresenta uma crise financeira urgente — acidente, problema médico, alfândega de presente — e pede transferências.',
    'sinais_alerta'     => "Perfil com fotos de pessoa muito atraente, sem amigos em comum\nRecusa sistemática de videochamada ou encontro presencial\nDeclaração de amor rápida demais\nHistória de vida elaborada mas inconsistente\nPedido de dinheiro com justificativa emocional urgente",
    'como_se_proteger'  => "Nunca transfira dinheiro para quem conheceu apenas online\nExija videochamada antes de aprofundar o relacionamento\nPesquise a foto do perfil no Google Imagens (busca reversa)\nConverse com amigos ou familiares sobre a pessoa antes de envolver dinheiro",
    'o_que_fazer'       => "Corte o contato imediatamente\nNão transfira mais dinheiro\nRegistre Boletim de Ocorrência\nDenuncie o perfil na plataforma\nBusque apoio emocional — a manipulação pode ser intensa",
    'tipo_golpe'        => ['Engenharia Social', 'Golpe Sentimental'],
    'canal_golpe'       => ['Redes Sociais', 'Aplicativos de Relacionamento'],
    'publico_alvo'      => ['Adultos', 'Idosos', 'Divorciados e Viúvos'],
]);

seed_golpe([
    'title'             => 'Golpe da Maquininha (Troca de Cartão)',
    'slug'              => 'golpe-da-maquininha-troca-de-cartao',
    'excerpt'           => 'Vendedor mal-intencionado distrai a vítima durante o pagamento, troca o cartão por um idêntico sem chip funcional e retém o original.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 1.000 – R$ 10.000',
    'novo_modus'        => '0',
    'como_age'          => 'O golpista, geralmente um atendente ou vendedor cúmplice, simula um erro na maquininha e pede o cartão para "ajudar a passar". Enquanto distrai a vítima, troca o cartão por outro visualmente semelhante. Já viu a senha durante a digitação ou usa um equipamento leitor (skimmer). Com o cartão e a senha, realiza saques e compras.',
    'sinais_alerta'     => "Maquininha com erro repetido que exige que você entregue o cartão\nAtendente que cobre o teclado ou gira a maquininha de forma estranha\nSentir que ficou com cartão diferente após a transação\nSaldo diminuindo sem compras reconhecidas",
    'como_se_proteger'  => "Nunca entregue o cartão — você deve inserir ou aproximar você mesmo\nSempre cubra o teclado ao digitar a senha\nConclua a transação e imediatamente confira se está com o seu cartão\nEm caso de erro na maquininha, prefira outro método de pagamento",
    'o_que_fazer'       => "Bloqueie imediatamente o cartão pelo app do banco\nLigue para o banco e comunique a troca\nRegistre Boletim de Ocorrência descrevendo o estabelecimento\nSolicite estorno de transações não reconhecidas",
    'tipo_golpe'        => ['Golpe Presencial', 'Golpe Bancário'],
    'canal_golpe'       => ['Presencial'],
    'publico_alvo'      => ['Correntistas'],
]);

seed_golpe([
    'title'             => 'Golpe do QR Code Falso',
    'slug'              => 'golpe-do-qr-code-falso',
    'excerpt'           => 'Criminosos colam adesivos com QR Codes falsos sobre QR Codes legítimos em restaurantes, estacionamentos ou pontos de cobrança para redirecionar pagamentos.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 50 – R$ 5.000',
    'novo_modus'        => '1',
    'como_age'          => 'QR Codes adulterados são colados sobre os originais em locais de pagamento. A vítima escaneia o código e realiza um Pix para a conta do criminoso em vez do estabelecimento legítimo. Em golpes digitais, QR Codes falsos são enviados por e-mail ou mensagem com a aparência de cobranças de concessionárias, bancos ou lojas.',
    'sinais_alerta'     => "QR Code com adesivo sobre outro adesivo ou aparentando ser sobreposto\nApós escanear, o nome do recebedor não corresponde à loja ou empresa\nChave Pix do destinatário não bate com o nome do estabelecimento\nQR Code enviado por mensagem pedindo pagamento urgente",
    'como_se_proteger'  => "Sempre verifique o nome do destinatário antes de confirmar o Pix\nToque no QR Code físico para ver se está colado sobre outro\nDesconfie de QR Codes enviados por mensagem fora do contexto esperado\nPrefira digitar a chave Pix manualmente em cobranças de estabelecimentos",
    'o_que_fazer'       => "Não confirme o pagamento se o destinatário for desconhecido\nAvise o estabelecimento sobre o QR Code falso\nRegistre Boletim de Ocorrência se o pagamento foi feito\nContate o banco para tentar reverter a transação",
    'tipo_golpe'        => ['Golpe de Pagamento', 'Golpe Digital'],
    'canal_golpe'       => ['Presencial', 'WhatsApp', 'E-mail'],
    'publico_alvo'      => ['Consumidores'],
]);

seed_golpe([
    'title'             => 'Golpe do Falso INSS',
    'slug'              => 'golpe-do-falso-inss',
    'excerpt'           => 'Criminosos se passam por servidores do INSS para aplicar golpes em aposentados e pensionistas, prometendo revisões de benefício ou ameaçando cancelamento.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 500 – R$ 30.000',
    'novo_modus'        => '0',
    'como_age'          => 'O golpista liga para aposentados alegando ser do INSS, Ministério da Previdência ou correspondente bancário. Informa sobre um benefício novo, revisão de pagamento atrasado ou alerta de cancelamento do benefício. Solicita dados pessoais, senhas do Meu INSS ou convence a vítima a assinar contratos de crédito consignado sem perceber.',
    'sinais_alerta'     => "Ligação do INSS não solicitada com promessa de revisão ou devolução de valores\nPedido de senha ou código de acesso ao Meu INSS\nOrientação para ir ao banco assinar um contrato urgente\nAmeaça de cancelamento de benefício por pendência cadastral",
    'como_se_proteger'  => "O INSS não liga pedindo senhas ou dados bancários\nAcesse o Meu INSS apenas pelo site ou app oficial\nNunca assine contratos por pressão de terceiros\nLigue para a Central 135 para verificar qualquer informação",
    'o_que_fazer'       => "Ligue para o 135 (Central do INSS) e relate o contato\nVerifique extratos de consignado no Meu INSS\nRegistre Boletim de Ocorrência\nSe assinou contrato indevido, procure o banco e o INSS para contestação",
    'tipo_golpe'        => ['Engenharia Social', 'Golpe Previdenciário'],
    'canal_golpe'       => ['Telefone'],
    'publico_alvo'      => ['Idosos', 'Aposentados e Pensionistas'],
]);

seed_golpe([
    'title'             => 'Golpe do Falso Delivery',
    'slug'              => 'golpe-do-falso-delivery',
    'excerpt'           => 'Golpista cria conta falsa em plataformas de delivery ou contata por WhatsApp se passando por restaurante para cobrar pedido antecipado que nunca é entregue.',
    'nivel_risco'       => 'medio',
    'prejuizo_estimado' => 'R$ 50 – R$ 500',
    'novo_modus'        => '0',
    'como_age'          => 'Perfis falsos de restaurantes são criados em aplicativos de delivery ou nas redes sociais com fotos e cardápios copiados. O golpista solicita pagamento antecipado via Pix ou transferência, prometendo entrega. O pedido nunca chega e o contato some.',
    'sinais_alerta'     => "Pedido de pagamento antecipado fora do app de delivery\nContato exclusivamente por WhatsApp sem presença no app oficial\nPreços muito abaixo do mercado\nSem avaliações ou com avaliações muito recentes e genéricas",
    'como_se_proteger'  => "Pague apenas pelo app oficial do serviço de delivery\nNunca transfira dinheiro diretamente para restaurantes por WhatsApp\nVerifique avaliações e tempo de operação do estabelecimento\nPrefira pagar na entrega quando possível",
    'o_que_fazer'       => "Reporte o perfil falso ao app de delivery\nRegistre Boletim de Ocorrência\nEntre em contato com o banco para tentar estornar o Pix\nDenuncie nas redes sociais e grupos da região",
    'tipo_golpe'        => ['Fraude em Compra e Venda', 'Golpe Digital'],
    'canal_golpe'       => ['WhatsApp', 'Aplicativos', 'Redes Sociais'],
    'publico_alvo'      => ['Consumidores'],
]);

seed_golpe([
    'title'             => 'Golpe do Investimento Falso (Pirâmide Financeira)',
    'slug'              => 'golpe-do-investimento-falso-piramide',
    'excerpt'           => 'Esquemas que prometem retornos financeiros garantidos e acima do mercado, muitas vezes usando criptomoedas ou plataformas internacionais como fachada.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 1.000 – R$ 500.000',
    'novo_modus'        => '0',
    'como_age'          => 'O golpista apresenta uma "oportunidade exclusiva" de investimento com rendimentos fixos muito acima do mercado (1-5% ao dia ou ao mês). Usa jargão técnico, plataformas com aparência profissional e depoimentos de "investidores lucrativos". A remuneração inicial é paga com o dinheiro de novos participantes. Quando os saques são bloqueados, o esquema colapsa.',
    'sinais_alerta'     => "Promessa de retorno garantido e fixo acima do mercado\nNecessidade de indicar amigos para receber bônus\nPlataforma sem regulação pelo Banco Central ou CVM\nDificuldade para sacar o dinheiro investido\nPressão para investir rapidamente antes de 'perder a vaga'",
    'como_se_proteger'  => "Verifique se a empresa tem autorização do Banco Central ou CVM\nDesconfie de qualquer investimento que prometa ganho fixo garantido\nNunca invista dinheiro que não pode perder em esquemas desconhecidos\nConsulte o Registrato do Banco Central antes de qualquer investimento",
    'o_que_fazer'       => "Não invista mais dinheiro\nRegistre Boletim de Ocorrência\nDenuncie ao Banco Central e à CVM\nReúna provas: prints, contratos, comprovantes de transferência\nBusque orientação jurídica",
    'tipo_golpe'        => ['Golpe Financeiro', 'Pirâmide'],
    'canal_golpe'       => ['Redes Sociais', 'WhatsApp', 'Internet'],
    'publico_alvo'      => ['Investidores', 'Adultos'],
]);

seed_golpe([
    'title'             => 'Golpe da Conta Salário Bloqueada',
    'slug'              => 'golpe-da-conta-salario-bloqueada',
    'excerpt'           => 'Golpista liga alegando que a conta salário está bloqueada por irregularidade e que é necessário sacar o dinheiro e depositar em outra conta para "regularização".',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 1.000 – R$ 15.000',
    'novo_modus'        => '0',
    'como_age'          => 'A vítima recebe uma ligação de suposto funcionário do banco ou do empregador informando que a conta salário foi bloqueada por suspeita de fraude ou por pendência cadastral. Para "desbloquear", orienta que a vítima saque todo o dinheiro e deposite em uma conta diferente indicada pelo golpista.',
    'sinais_alerta'     => "Ligação pedindo que você saque dinheiro e transfira para outra conta\nAlegação de bloqueio de conta que exige ação imediata\nOrientação para não comentar com ninguém sobre o bloqueio\nPressão para agir antes do horário bancário encerrar",
    'como_se_proteger'  => "Bancos nunca pedem que você transfira dinheiro para desbloquear conta\nDesligue e ligue para o banco pelo número do cartão ou do site oficial\nNunca saque dinheiro por orientação de ligação não solicitada\nConfirme qualquer bloqueio diretamente no app do banco",
    'o_que_fazer'       => "Não transfira o dinheiro — desligue imediatamente\nAcesse o app do banco para verificar o real status da conta\nLigue para o banco pelo número oficial e reporte a tentativa\nRegistre Boletim de Ocorrência",
    'tipo_golpe'        => ['Engenharia Social', 'Golpe Bancário'],
    'canal_golpe'       => ['Telefone'],
    'publico_alvo'      => ['Trabalhadores', 'Correntistas'],
]);

seed_golpe([
    'title'             => 'Golpe do Empréstimo em Nome da Vítima',
    'slug'              => 'golpe-do-emprestimo-nome-da-vitima',
    'excerpt'           => 'Criminosos usam dados pessoais roubados ou comprados para contratar empréstimos e financiamentos em nome da vítima sem que ela saiba.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 5.000 – R$ 100.000',
    'novo_modus'        => '0',
    'como_age'          => 'Com CPF, nome completo, data de nascimento e foto do documento, criminosos abrem contas digitais ou usam contas já abertas para contratar crédito pessoal, consignado fraudulento ou financiamentos. A vítima só descobre meses depois por cobranças de dívidas ou negativação no CPF.',
    'sinais_alerta'     => "Cobranças de dívidas que você não reconhece\nNegativação no CPF sem motivo aparente\nRecebimento de cartões ou boletos que não solicitou\nNotificação de abertura de conta em banco que nunca acessou",
    'como_se_proteger'  => "Monitore seu CPF regularmente no Serasa, Boa Vista e SPC\nAtive alertas de movimentação no seu CPF (Registrato do Banco Central)\nNunca envie foto de documentos para desconhecidos\nUse o Cadastro Positivo e revise contratos regularmente",
    'o_que_fazer'       => "Registre Boletim de Ocorrência imediatamente\nConteste o empréstimo diretamente na instituição financeira\nInicialize processo de cancelamento no Banco Central (Registrato)\nContate o Procon e, se necessário, o Banco Central",
    'tipo_golpe'        => ['Roubo de Identidade', 'Golpe de Crédito'],
    'canal_golpe'       => ['Internet', 'Vazamento de Dados'],
    'publico_alvo'      => ['Correntistas', 'Adultos'],
]);

/* ─────────────────────────────────────────
   FRAUDES
───────────────────────────────────────── */

WP_CLI::log('');
WP_CLI::log('=== Inserindo Fraudes ===');

seed_fraude([
    'title'             => 'Fraude de Identidade (Account Takeover)',
    'slug'              => 'fraude-de-identidade-account-takeover',
    'excerpt'           => 'Criminosos assumem o controle de contas bancárias ou digitais da vítima usando dados vazados, phishing ou engenharia social para realizar transações não autorizadas.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 2.000 – R$ 100.000',
    'nova_tecnica'      => '0',
    'como_funciona'     => 'Com dados pessoais obtidos por vazamentos, phishing ou compra em fóruns criminosos, o fraudador solicita redefinição de senha, troca de número de telefone vinculado ou usa técnicas de SIM swap para assumir o número da vítima. Em seguida, acessa o app do banco e realiza transferências, solicita empréstimos ou altera dados de contato para impedir a recuperação da conta.',
    'sinais_alerta'     => "Perda repentina de sinal de celular (pode indicar SIM swap)\nE-mails de alteração de senha que você não solicitou\nNotificações de login em dispositivo desconhecido\nTransações não reconhecidas no extrato",
    'como_se_proteger'  => "Ative autenticação em dois fatores (2FA) por aplicativo, não por SMS\nUse senhas únicas e fortes para cada serviço\nCadastre uma chave de segurança adicional no banco\nAtive alertas de transações por push no app bancário",
    'o_que_fazer'       => "Ligue para o banco imediatamente e bloqueie a conta\nRegistre Boletim de Ocorrência\nContate a operadora de celular se suspeitar de SIM swap\nMonitore o CPF e solicite bloqueio de crédito preventivo",
    'tipo_fraude'       => ['Fraude Digital', 'Fraude Bancária'],
    'canal_golpe'       => ['Internet', 'Telefone'],
    'publico_alvo'      => ['Correntistas'],
]);

seed_fraude([
    'title'             => 'Fraude de Skimming (Clonagem de Cartão em ATM)',
    'slug'              => 'fraude-skimming-clonagem-cartao-atm',
    'excerpt'           => 'Dispositivos ilegais instalados em caixas eletrônicos ou maquininhas capturam os dados magnéticos do cartão e registram a senha digitada pela câmera oculta.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 500 – R$ 10.000',
    'nova_tecnica'      => '0',
    'como_funciona'     => 'Criminosos instalam um leitor falso (skimmer) sobre a entrada do cartão no caixa eletrônico e uma câmera oculta ou teclado sobreposto para capturar a senha. Os dados coletados são usados para fabricar cartões clonados ou realizar compras online. A vítima não percebe durante a transação.',
    'sinais_alerta'     => "Leitor de cartão com peça solta ou aparência diferente do habitual\nTeclado numérico que parece sobreposto ou com textura diferente\nCâmera suspeita acima do teclado ou ao lado do leitor\nTransações não reconhecidas após uso em caixas eletrônicos",
    'como_se_proteger'  => "Sempre cubra o teclado com a outra mão ao digitar a senha\nVerifique o leitor de cartão puxando levemente antes de inserir\nPreira caixas eletrônicos em agências bancárias com vigilância\nUse cartões com chip e prefira pagamento por aproximação",
    'o_que_fazer'       => "Bloqueie o cartão imediatamente pelo app\nLigue para o banco e reporte a clonagem\nRegistre Boletim de Ocorrência\nSolicite estorno das transações não reconhecidas\nComunique o banco sobre o ATM suspeito",
    'tipo_fraude'       => ['Fraude em Cartão', 'Fraude Presencial'],
    'canal_golpe'       => ['Presencial', 'ATM'],
    'publico_alvo'      => ['Correntistas'],
]);

seed_fraude([
    'title'             => 'Fraude no Financiamento de Veículo',
    'slug'              => 'fraude-no-financiamento-de-veiculo',
    'excerpt'           => 'Uso de documentos falsos ou dados de terceiros para contratar financiamento de veículo sem pagamento, deixando a dívida em nome da vítima ou do banco.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 20.000 – R$ 150.000',
    'nova_tecnica'      => '0',
    'como_funciona'     => 'Criminosos usam documentos falsificados ou furtados para se passar por compradores idôneos e financiar veículos. O carro é retirado e as parcelas nunca são pagas. A pessoa cujo nome foi usado descobre ao ser cobrada ou ter o nome negativado. Também ocorre com concessionárias coniventes ou funcionários corruptos que aprovam crédito sabendo da fraude.',
    'sinais_alerta'     => "Cobrança de parcelas de veículo que nunca comprou\nCPF negativado por financiamento desconhecido\nNotificação de transferência de propriedade de veículo sem sua autorização",
    'como_se_proteger'  => "Monitore seu CPF regularmente no Serasa e no Detran\nCadastre-se no Registrato do Banco Central para ver contratos em seu nome\nGuarde seus documentos com segurança e não os empréstimos",
    'o_que_fazer'       => "Registre Boletim de Ocorrência imediatamente\nConteste o financiamento no banco ou financeira\nComunique o Detran sobre transferência irregular\nBusque orientação jurídica para ressarcimento",
    'tipo_fraude'       => ['Fraude de Identidade', 'Fraude em Crédito'],
    'canal_golpe'       => ['Presencial', 'Internet'],
    'publico_alvo'      => ['Adultos'],
]);

seed_fraude([
    'title'             => 'Fraude de Phishing Bancário',
    'slug'              => 'fraude-phishing-bancario',
    'excerpt'           => 'Páginas falsas de bancos enviadas por e-mail, SMS ou redes sociais capturam login, senha e token de autenticação para acesso não autorizado à conta.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 500 – R$ 50.000',
    'nova_tecnica'      => '0',
    'como_funciona'     => 'A vítima recebe um e-mail, SMS ou mensagem com layout idêntico ao do banco alegando problema na conta, fatura em atraso ou atualização obrigatória. O link leva a uma página clonada onde a vítima insere login, senha e até o token de autenticação. O criminoso usa essas credenciais em tempo real para acessar a conta verdadeira e realizar transferências.',
    'sinais_alerta'     => "URL diferente do domínio oficial do banco (ex: banco-seguro.com em vez de banco.com.br)\nE-mail com erros de português ou formatação estranha\nMensagem pedindo token ou código de autenticação\nSolicita atualização urgente com prazo curto",
    'como_se_proteger'  => "Nunca clique em links de e-mails bancários — acesse direto pelo app ou digitando o endereço\nVerifique sempre a URL antes de digitar qualquer dado\nEm caso de dúvida, ligue para o banco pelo número no verso do cartão\nAtive autenticação em dois fatores por app autenticador",
    'o_que_fazer'       => "Troque imediatamente a senha do banco\nLigue para o banco e relate o acesso suspeito\nVerifique o extrato por transações não reconhecidas\nRegistre Boletim de Ocorrência",
    'tipo_fraude'       => ['Fraude Digital', 'Phishing'],
    'canal_golpe'       => ['E-mail', 'SMS', 'Redes Sociais'],
    'publico_alvo'      => ['Correntistas'],
]);

seed_fraude([
    'title'             => 'Fraude no FGTS',
    'slug'              => 'fraude-no-fgts',
    'excerpt'           => 'Criminosos obtêm dados de trabalhadores para sacar benefícios do FGTS de forma fraudulenta, incluindo saques emergenciais e antecipação de aniversário.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 500 – R$ 15.000',
    'nova_tecnica'      => '0',
    'como_funciona'     => 'Com dados pessoais obtidos em vazamentos, os criminosos acessam o aplicativo FGTS ou criam contas na Caixa Econômica com os dados da vítima. Realizam saques da modalidade aniversário, saque emergencial ou simulam demissão para sacar o saldo completo. A vítima descobre o saque fraudulento ao tentar usar o próprio FGTS.',
    'sinais_alerta'     => "Saldo do FGTS menor que o esperado sem ter solicitado saque\nNotificação de adesão à modalidade aniversário que você não fez\nE-mail ou SMS da Caixa sobre movimentação não reconhecida",
    'como_se_proteger'  => "Acesse regularmente o app FGTS para monitorar saldo e transações\nNunca compartilhe CPF e dados com links não oficiais\nCadastre uma senha forte no app e ative biometria",
    'o_que_fazer'       => "Registre Boletim de Ocorrência imediatamente\nLigue para a Caixa Econômica (0800 726 0101) e reporte\nConteste os saques fraudulentos na agência\nComunique a Ouvidoria da Caixa e, se necessário, o Banco Central",
    'tipo_fraude'       => ['Fraude Trabalhista', 'Fraude de Identidade'],
    'canal_golpe'       => ['Internet', 'Aplicativos'],
    'publico_alvo'      => ['Trabalhadores', 'Adultos'],
]);

seed_fraude([
    'title'             => 'Fraude de Boleto Bancário Adulterado',
    'slug'              => 'fraude-boleto-bancario-adulterado',
    'excerpt'           => 'Boletos legítimos são interceptados e adulterados digitalmente para redirecionar o pagamento para conta do fraudador, com o código de barras e dados bancários alterados.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 200 – R$ 50.000',
    'nova_tecnica'      => '0',
    'como_funciona'     => 'O boleto original é interceptado por malware no computador da vítima ou recebido de forma adulterada por e-mail. O código de barras ou o código do Pix é trocado, redirecionando o pagamento para a conta do fraudador. O beneficiário original não recebe o pagamento e o débito é feito normalmente na conta da vítima.',
    'sinais_alerta'     => "Nome do beneficiário diferente do esperado ao confirmar o pagamento\nBoleto enviado por e-mail diferente do habitual do fornecedor\nValor ligeiramente diferente do original\nCódigo de barras com caracteres corrompidos ou ilegíveis",
    'como_se_proteger'  => "Sempre confirme o nome do beneficiário antes de pagar\nPague boletos apenas pelo app do banco, não por links de e-mail\nMantenha antivírus atualizado no computador\nLigue para o fornecedor para confirmar boletos de alto valor",
    'o_que_fazer'       => "Não pague boletos suspeitos sem confirmar com o emissor\nSe já pagou, contate o banco imediatamente para tentativa de reversão\nRegistre Boletim de Ocorrência\nComunique o emissor do boleto sobre a fraude",
    'tipo_fraude'       => ['Fraude em Pagamento', 'Fraude Digital'],
    'canal_golpe'       => ['E-mail', 'Internet'],
    'publico_alvo'      => ['Empresas', 'Consumidores'],
]);

seed_fraude([
    'title'             => 'Fraude em Leilão Online',
    'slug'              => 'fraude-em-leilao-online',
    'excerpt'           => 'Sites falsos de leilão ou anúncios fraudulentos em plataformas legítimas oferecem produtos com preços muito abaixo do mercado e somem após o pagamento.',
    'nivel_risco'       => 'medio',
    'prejuizo_estimado' => 'R$ 500 – R$ 30.000',
    'nova_tecnica'      => '0',
    'como_funciona'     => 'Plataformas de leilão falsas ou perfis falsos em marketplaces anunciam veículos, eletrônicos e imóveis por valores muito abaixo do mercado. Exigem pagamento de sinal ou valor integral via Pix ou transferência antes de qualquer visita ou entrega. O contato some após o recebimento.',
    'sinais_alerta'     => "Preço muito abaixo do mercado sem explicação plausível\nSolicitação de pagamento antes de ver o produto\nSite sem CNPJ, endereço físico ou com dados inconsistentes\nPressão para pagar antes que 'outro comprador arremate'",
    'como_se_proteger'  => "Pesquise o CNPJ da empresa e cheque reclamações no Reclame Aqui\nNunca pague antes de inspecionar fisicamente o bem\nVerifique se o domínio do site é legítimo e seguro (https)\nDesconfie de preços muito abaixo do mercado",
    'o_que_fazer'       => "Registre Boletim de Ocorrência\nDenuncie o site ao Procon e ao Banco Central\nContate o banco para tentativa de reversão do Pix\nReúna provas: prints, e-mails, comprovantes",
    'tipo_fraude'       => ['Fraude em Comércio Eletrônico'],
    'canal_golpe'       => ['Internet', 'Redes Sociais'],
    'publico_alvo'      => ['Consumidores'],
]);

seed_fraude([
    'title'             => 'Fraude de Portabilidade de Crédito Consignado',
    'slug'              => 'fraude-portabilidade-credito-consignado',
    'excerpt'           => 'Criminosos contratam portabilidade de empréstimo consignado em nome da vítima ou convencem aposentados a assinar contratos sem entender o que estão assinando.',
    'nivel_risco'       => 'alto',
    'prejuizo_estimado' => 'R$ 2.000 – R$ 50.000',
    'nova_tecnica'      => '0',
    'como_funciona'     => 'Com dados pessoais ou contato direto com a vítima se passando por correspondente bancário, os fraudadores realizam portabilidade de contratos de crédito consignado para bancos parceiros com taxas mais altas. A diferença entre os valores é embolsada pelos criminosos. Em casos mais graves, contratam novos créditos sem a autorização da vítima.',
    'sinais_alerta'     => "Desconto novo e desconhecido no holerite ou benefício do INSS\nMensagem ou ligação de banco desconhecido sobre portabilidade\nDocumento de portabilidade que você não assinou\nRedução no valor líquido recebido sem explicação",
    'como_se_proteger'  => "Monitore regularmente seu extrato de consignações no Meu INSS ou RH\nNunca assine documentos sem ler ou por pressão\nDesconfie de promessas de liberação de crédito sem solicitação sua\nLigue para a Central 135 para verificar contratos em seu nome",
    'o_que_fazer'       => "Registre Boletim de Ocorrência\nConteste os contratos no banco e no INSS (Central 135)\nProcure o Procon ou a Defensoria Pública\nSolicite cópia de todos os contratos e documentos usados",
    'tipo_fraude'       => ['Fraude Previdenciária', 'Fraude em Crédito'],
    'canal_golpe'       => ['Telefone', 'Presencial'],
    'publico_alvo'      => ['Aposentados e Pensionistas', 'Servidores Públicos'],
]);

WP_CLI::log('');
WP_CLI::success('Seed concluído!');
