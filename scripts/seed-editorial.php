<?php
/**
 * Seed editorial — Atualiza post_content dos golpes e fraudes
 *
 * Uso:
 *   wp eval-file scripts/seed-editorial.php
 *
 * Idempotente: só atualiza posts cujo post_content está vazio.
 * Para forçar reescrita passe: define('FS_FORCE_EDITORIAL', true);
 */

defined('ABSPATH') || define('ABSPATH', dirname(__FILE__) . '/');

$force = defined('FS_FORCE_EDITORIAL') && FS_FORCE_EDITORIAL;

function seed_editorial($slug, $post_type, $content, $force = false) {
    $post = get_page_by_path($slug, OBJECT, $post_type);
    if (!$post) {
        WP_CLI::warning("  [skip] post não encontrado: {$slug}");
        return;
    }
    if (!$force && !empty(trim($post->post_content))) {
        WP_CLI::log("  [skip] já tem conteúdo: {$slug}");
        return;
    }
    $result = wp_update_post(['ID' => $post->ID, 'post_content' => $content], true);
    if (is_wp_error($result)) {
        WP_CLI::warning("  [erro] {$slug}: " . $result->get_error_message());
    } else {
        WP_CLI::success("  [ok] atualizado: {$slug}");
    }
}

/* ─────────────────────────────────────────
   GOLPES
───────────────────────────────────────── */

WP_CLI::log('');
WP_CLI::log('=== Conteúdo editorial — Golpes ===');

seed_editorial('golpe-do-falso-motoboy', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Bancos nunca enviam motoboys para recolher cartões
- O golpista usa engenharia social para criar urgência
- Ao entregar o cartão, a vítima perde o acesso à conta em minutos
- Idosos são o principal alvo, mas qualquer correntista pode ser vítima
[/resumo]

O golpe do falso motoboy é uma das modalidades mais antigas e ainda eficientes do crime financeiro no Brasil. Apesar de amplamente divulgado, ele continua fazendo vítimas todos os anos — especialmente entre pessoas acima de 60 anos, que tendem a confiar mais em contatos que se identificam como representantes de instituições.

O roteiro é quase sempre o mesmo: o telefone toca, uma voz profissional e educada se identifica como atendente de um banco conhecido e comunica que foi detectada uma tentativa de fraude no cartão da vítima. Para "resolver a situação", o banco estaria enviando um motoboy para recolher o cartão comprometido e entregar um novo.

[destaque tipo="perigo"]O banco NUNCA envia funcionários ou representantes para buscar cartões em domicílio. Se alguém oferecer esse serviço, é golpe — sem exceção.[/destaque]

A eficiência do golpe está na engenharia social. O criminoso mantém a vítima na linha, cria um senso de urgência ("sua conta pode ser bloqueada") e instrui que ela não deve comentar com ninguém enquanto aguarda o motoboy — uma técnica clássica de isolamento que impede que familiares alertem a vítima.

Quando o cartão é entregue, os golpistas já sabem a senha: ela foi capturada durante a ligação, quando o criminoso pediu que a vítima "confirmasse" os dados para cancelar o cartão anterior. Em questão de minutos, saques e transferências esvaziam a conta.

[estatistica numero="R$ 2,5 bi" descricao="em fraudes bancárias por telefone registradas no Brasil em 2023" fonte="Febraban"]

<h2>Por que as pessoas ainda caem?</h2>

A resposta está na psicologia da urgência. Quando alguém nos diz que nossa conta está em risco, o instinto natural é agir rápido. O criminoso explora exatamente esse momento de stress para cortar o raciocínio crítico. Além disso, os golpistas frequentemente têm acesso a dados básicos da vítima — nome completo, CPF, número do banco — obtidos em vazamentos de dados, o que torna a abordagem altamente convincente.

[faq titulo="Perguntas frequentes"]
[faq-item q="O banco pode ligar pedindo meu cartão?"]Não. Nenhum banco, em nenhuma circunstância, solicita a devolução física de um cartão por meio de motoboy ou qualquer outro mensageiro. A única forma legítima de cancelar um cartão é pelo app, pelo internet banking ou ligando você mesmo para o número oficial no verso do cartão.[/faq-item]
[faq-item q="E se eu já entreguei o cartão?"]Ligue imediatamente para o banco pelo número oficial (no verso de outro cartão seu ou no site oficial). Solicite o bloqueio emergencial e registre um Boletim de Ocorrência. Guarde o número de protocolo de tudo.[/faq-item]
[faq-item q="Como saber se a ligação é verdadeira?"]Desligue e ligue de volta pelo número oficial do banco. Nunca confie no número que aparece no seu celular — ele pode ser falsificado com técnicas de spoofing.[/faq-item]
[/faq]

<h2>O papel do spoofing de chamadas</h2>

Muitos casos do golpe do falso motoboy envolvem spoofing — a falsificação do número de origem da chamada. A vítima vê no display do celular o número oficial do banco, o que aumenta enormemente a credibilidade do contato. Essa tecnologia é acessível a criminosos e não exige conhecimento técnico avançado.

A Anatel e os bancos trabalham em conjunto para combater essa prática, mas a solução definitiva ainda não existe. Por isso, a melhor defesa continua sendo comportamental: desconfie de qualquer ligação que peça ação imediata e nunca entregue nada antes de verificar por conta própria.
CONTENT
, $force);

seed_editorial('golpe-do-falso-suporte-tecnico', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Nenhuma empresa de tecnologia ou banco solicita acesso remoto ao seu celular
- Aplicativos como AnyDesk e TeamViewer são legítimos, mas podem ser usados para o mal
- O criminoso age em tempo real enquanto você observa a tela
- O golpe pode drenar conta, cartão e empréstimos em menos de 30 minutos
[/resumo]

Imagine receber uma mensagem avisando que sua conta bancária foi comprometida. Em seguida, o telefone toca — é alguém do "suporte do banco" com um plano para resolver tudo. A solução? Instalar um pequeno aplicativo que permite ao atendente "verificar o problema à distância". Parece razoável. É um golpe.

O golpe do falso suporte técnico cresceu exponencialmente com a popularização dos aplicativos bancários. Os criminosos se aproveitam da complexidade percebida da tecnologia e do medo genuíno de ter a conta invadida para convencer as vítimas a instalar ferramentas de acesso remoto — principalmente AnyDesk, TeamViewer e similares.

[destaque tipo="perigo"]Com acesso remoto ao seu celular, o criminoso enxerga tudo que você vê — inclusive senhas que você digita, tokens de autenticação e saldos em tempo real.[/destaque]

<h2>Como o ataque se desenrola</h2>

[passo numero="1" titulo="O primeiro contato"]A vítima recebe uma ligação, e-mail ou mensagem no WhatsApp com um alerta sobre "atividade suspeita" na conta. O contato parece oficial: usa o nome do banco, números semelhantes aos reais e linguagem profissional.[/passo]

[passo numero="2" titulo="A instalação do aplicativo"]O "atendente" orienta a vítima a baixar um aplicativo de acesso remoto, geralmente disponível gratuitamente nas lojas oficiais. Isso dá aparência de legitimidade ao processo.[/passo]

[passo numero="3" titulo="A ação silenciosa"]Com acesso ao dispositivo, o criminoso navega pelo app bancário da vítima enquanto a mantém ocupada na ligação. Realiza transferências Pix, solicita empréstimos e altera limites — tudo visível na tela da vítima, mas explicado como "procedimentos de segurança".[/passo]

[passo numero="4" titulo="O desaparecimento"]Quando a sessão termina, o criminoso desinstala o app remotamente ou instrui a vítima a fazê-lo para "confirmar que o problema foi resolvido". O rastro desaparece, mas o dinheiro também.[/passo]

[estatistica numero="47%" descricao="dos golpes digitais em 2023 envolveram alguma forma de acesso remoto ao dispositivo da vítima" fonte="DFNDR Lab"]

<h2>Alvos preferenciais</h2>

Embora qualquer pessoa possa ser vítima, o perfil mais comum envolve pessoas que recentemente realizaram alguma transação digital — compraram algo online, abriram conta em um banco digital ou fizeram um Pix pela primeira vez. Criminosos compram listas de leads de vazamentos de dados e contatam pessoas logo após eventos que tornam a abordagem mais crível.

[faq titulo="Perguntas frequentes"]
[faq-item q="AnyDesk e TeamViewer são ilegais?"]Não. São ferramentas legítimas usadas por empresas de TI para suporte remoto. O problema é quando são usadas por criminosos para acesso não autorizado. Nenhuma instituição financeira, porém, usa essas ferramentas para atendimento ao cliente.[/faq-item]
[faq-item q="O banco me liga para instalar aplicativos?"]Jamais. Bancos possuem seus próprios canais de atendimento e nunca solicitam instalação de aplicativos de terceiros, nem durante atendimento telefônico nem por mensagem.[/faq-item]
[faq-item q="Como remover o acesso remoto se já instalei?"]Desinstale o aplicativo imediatamente e reinicie o dispositivo. Troque todas as senhas bancárias de outro dispositivo seguro e ligue para o banco para bloquear a conta preventivamente.[/faq-item]
[/faq]

[destaque tipo="dica"]Se receber uma ligação de "suporte técnico" não solicitada, desligue imediatamente. Ligue de volta para o número oficial que você mesmo buscou — não o número que te ligou.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-do-pix-comprovante-falso', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Um comprovante Pix pode ser editado em segundos com qualquer editor de imagem
- O dinheiro só é seu quando aparece no extrato — não no print
- Vendedores online são os principais alvos
- Pequenas inconsistências no comprovante entregam o golpe
[/resumo]

Com a popularização do Pix, surgiu uma nova modalidade de golpe que não exige habilidade técnica sofisticada: o golpista simplesmente edita uma imagem de comprovante real e envia à vítima como prova de pagamento. Em segundos, um negócio que parecia concluído se torna um prejuízo.

O golpe do comprovante falso afeta principalmente vendedores de itens usados em plataformas como OLX, Marketplace do Facebook e grupos de WhatsApp. A dinâmica é simples: o comprador demonstra interesse, negocia o valor, envia um comprovante editado e pressiona para retirar o produto imediatamente — antes que a vítima tenha tempo de conferir o extrato.

[destaque tipo="importante"]O único comprovante válido de um Pix é o lançamento no seu extrato bancário. Print de tela não tem valor jurídico e pode ser facilmente falsificado.[/destaque]

<h2>Como identificar um comprovante falso</h2>

Existem alguns sinais que denunciam comprovantes adulterados, mas o mais confiável continua sendo verificar o extrato:

[checklist titulo="Sinais de alerta no comprovante" tipo="warn"]
- Nome do pagador diferente do acordado na negociação
- Horário da transação em momento atípico (madrugada, feriado)
- Valor com centavos estranhos que não combinam com o negociado
- Layout diferente do padrão do banco declarado pelo comprador
- Código de autenticação (E2E ID) ausente ou com formato incorreto
- Fonte ou espaçamento inconsistente com o restante do documento
[/checklist]

<h2>O que diferencia o Pix de outros meios de pagamento</h2>

O Pix é instantâneo e irreversível para o vendedor. Uma vez que o comprador realiza a transferência de verdade, o dinheiro cai na conta em segundos e não pode ser estornado pelo banco sem autorização judicial. Essa característica, que é uma vantagem para transações legítimas, também é explorada pelos golpistas para criar pressão: "já transferi, pode me dar o produto".

[estatistica numero="R$ 1,2 bi" descricao="em fraudes envolvendo Pix registradas em 2023 no Brasil" fonte="Banco Central do Brasil"]

[faq titulo="Perguntas frequentes"]
[faq-item q="Posso reverter um Pix que fiz por engano?"]Se você foi vítima de golpe, registre um Boletim de Ocorrência e acione o banco imediatamente. O Banco Central tem o Mecanismo Especial de Devolução (MED) que pode reverter Pix fraudulentos dentro de 7 dias, mas o dinheiro precisa estar disponível na conta do destinatário.[/faq-item]
[faq-item q="O banco é responsável se aceitei um comprovante falso?"]Juridicamente, a responsabilidade é do vendedor que liberou o produto sem confirmar o recebimento. Porém, se o banco não aplicou medidas de segurança adequadas, pode haver responsabilidade compartilhada. Consulte o Procon ou um advogado.[/faq-item]
[faq-item q="Como vender com segurança usando Pix?"]Sempre acesse o app do banco e confirme o lançamento no extrato antes de entregar qualquer produto. Para valores altos, aguarde alguns minutos adicionais — transações suspeitas podem ser bloqueadas temporariamente pelo sistema do banco.[/faq-item]
[/faq]

[destaque tipo="dica"]Para vendas presenciais, peça ao comprador que faça o Pix na sua frente e mostre a confirmação no app dele. Em seguida, abra o seu próprio app e confirme o lançamento antes de entregar o produto.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-do-emprego-falso', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Nunca pague para conseguir emprego — nenhuma vaga legítima cobra isso
- Documentos enviados em processos falsos alimentam outros golpes de identidade
- O golpe atinge mais intensamente jovens e pessoas em situação de desemprego prolongado
- Verifique sempre o CNPJ da empresa antes de qualquer envio de dados
[/resumo]

O desemprego cria vulnerabilidade. Quando uma pessoa está há semanas ou meses buscando uma oportunidade, a chegada de uma proposta atraente ativa um mecanismo poderoso: a esperança. É exatamente essa emoção que os golpistas do emprego falso exploram com precisão.

A operação funciona em duas variantes principais. Na primeira, o objetivo é financeiro imediato: cobrar taxas para "processar" a candidatura, pagar por exames médicos, comprar uniformes ou custear treinamentos antes de começar. Na segunda — e mais perigosa a longo prazo — o objetivo é coletar documentos para cometer fraudes de identidade: abrir contas, solicitar crédito, realizar compras em nome da vítima.

[destaque tipo="perigo"]Qualquer processo seletivo que peça pagamento ou documentos completos sem entrevista presencial ou por vídeo é um sinal vermelho imediato.[/destaque]

<h2>Os canais mais usados pelos golpistas</h2>

Os anúncios aparecem em plataformas legítimas como Indeed, LinkedIn e Catho — além de grupos de WhatsApp e páginas no Instagram. Os criminosos pagam para anunciar ou criam perfis falsos com logos e descrições copiadas de empresas reais. Muitas vezes, a empresa que está sendo falsificada nem sabe que seu nome está sendo usado.

[estatistica numero="2,3 milhões" descricao="de vagas falsas identificadas no Brasil em 2023, segundo levantamento de plataformas de emprego" fonte="Infojobs"]

<h2>Como verificar se uma vaga é legítima</h2>

[checklist titulo="Checklist de verificação de vagas" tipo="ok"]
- Pesquise o CNPJ da empresa no site da Receita Federal (receita.fazenda.gov.br)
- Busque o nome da empresa + "reclamação" ou "golpe" no Google
- Verifique se a empresa tem site oficial com domínio próprio
- Confirme se a vaga está também no site oficial da empresa
- Ligue para o RH da empresa pelo número do site oficial para confirmar o processo
- Nunca transfira dinheiro antes de assinar contrato e ter primeiro dia confirmado
[/checklist]

[faq titulo="Perguntas frequentes"]
[faq-item q="Posso enviar foto de documento para uma vaga?"]Só após confirmação da legitimidade da empresa e, mesmo assim, com cuidado. Prefira enviar em entrevistas presenciais ou por plataformas com autenticação. Nunca envie por WhatsApp para um contato desconhecido.[/faq-item]
[faq-item q="Já paguei a taxa. O que fazer?"]Registre Boletim de Ocorrência imediatamente. Se o pagamento foi por Pix, acione o banco para tentativa de estorno via MED. Denuncie o perfil ou anúncio na plataforma onde foi divulgado.[/faq-item]
[faq-item q="Como denunciar uma vaga falsa?"]Pela própria plataforma onde foi anunciada (botão "Denunciar"), pelo Procon do seu estado e pela Polícia Civil via delegacia digital ou presencial.[/faq-item]
[/faq]

<h2>O rastro dos documentos roubados</h2>

Quando um golpista coleta RG, CPF e foto com documento de centenas de vítimas em um único processo seletivo falso, esses dados são negociados em fóruns criminosos. Uma identidade completa pode ser vendida por menos de R$ 50 e usada para abrir contas digitais, solicitar empréstimos e cometer outros crimes em nome da vítima — que só descobrirá o problema meses depois, quando for negativada por dívidas que jamais contraiu.

[destaque tipo="importante"]Se você enviou documentos para uma vaga que suspeita ser falsa, monitore seu CPF no Serasa e no Registrato do Banco Central. Ative alertas de movimentação gratuitamente.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-do-amor-romance-scam', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- O golpe pode durar semanas ou meses antes do pedido de dinheiro
- Criminosos criam laços emocionais reais antes de agir
- Fotos são sempre roubadas de terceiros — a busca reversa identifica isso
- Vítimas frequentemente sentem vergonha, mas o golpe é sofisticado e afeta pessoas de todos os perfis
[/resumo]

O golpe do amor — conhecido internacionalmente como romance scam — é provavelmente a modalidade de fraude financeira mais cruel. Não porque o valor perdido seja sempre o maior, mas porque rouba algo além do dinheiro: a confiança emocional da vítima. Pessoas que passaram por essa experiência relatam um impacto psicológico comparável ao luto.

O criminoso — muitas vezes parte de uma organização criminosa operando de outros países — cria um perfil cuidadosamente construído em aplicativos de relacionamento, redes sociais ou grupos do WhatsApp. As fotos são reais, mas pertencem a outra pessoa: militares americanos, médicos europeus, empresários bem-sucedidos são os perfis mais comuns. A identidade é elaborada com detalhes convincentes.

[destaque tipo="importante"]O romance scam não é um golpe de ingenuidade. É uma operação de manipulação psicológica conduzida por profissionais. Qualquer pessoa pode ser vítima.[/destaque]

<h2>A construção do vínculo</h2>

O que diferencia o romance scam de outros golpes é o tempo investido. Semanas — às vezes meses — de conversas diárias, mensagens de bom dia, interesse genuíno nos problemas da vítima, declarações de amor progressivamente mais intensas. O criminoso estuda a vítima e molda a relação para criar dependência emocional.

Pedidos de videochamada são sistematicamente recusados com desculpas plausíveis: problemas com a câmera, conexão ruim, horário incompatível por conta do fuso. Para chamadas de voz, o criminoso pode usar transformadores de voz ou recrutar cúmplices locais.

[estatistica numero="R$ 320 mil" descricao="é o prejuízo médio por vítima em casos de romance scam com transferência internacional" fonte="FBI IC3 2023"]

<h2>O momento do pedido</h2>

Quando o vínculo emocional está estabelecido, surge a crise: um acidente, uma cirurgia urgente, um negócio que precisa de capital imediato, uma remessa presa na alfândega. O pedido de dinheiro parece um detalhe menor diante de tudo que foi construído. E a vítima, agora emocionalmente comprometida, frequentemente ajuda — às vezes várias vezes.

[checklist titulo="Sinais que denunciam o romance scam" tipo="warn"]
- Perfil com poucas fotos, todas profissionais demais
- História de vida impressionante mas difícil de verificar (militar no exterior, médico humanitário)
- Declaração de amor acelerada — dias ou semanas após o primeiro contato
- Recusa sistemática de videochamada
- Pedido de dinheiro com justificativa emocional urgente
- Solicita transferência internacional ou em criptomoedas
[/checklist]

[faq titulo="Perguntas frequentes"]
[faq-item q="Como fazer a busca reversa de uma foto?"]No Google Imagens (images.google.com), clique no ícone de câmera e faça o upload da foto do perfil. Se a imagem aparecer associada a outro nome ou conta, é um sinal claro de identidade falsa.[/faq-item]
[faq-item q="O que fazer se percebo que fui vítima?"]Corte o contato imediatamente, sem explicações. Registre Boletim de Ocorrência. Se transferiu dinheiro, acione o banco. Não sinta vergonha — relate o ocorrido a alguém de confiança e busque apoio psicológico se necessário.[/faq-item]
[faq-item q="Posso recuperar o dinheiro?"]Raramente, especialmente em transferências internacionais ou em criptomoedas. Registre o B.O. de qualquer forma — os dados ajudam investigações que podem identificar redes criminosas maiores.[/faq-item]
[/faq]

[destaque tipo="dica"]Antes de aprofundar qualquer relacionamento online, peça uma videochamada. Não aceite desculpas repetidas. Um relacionamento legítimo não tem razão para evitar contato visual.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-da-maquininha-troca-de-cartao', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Você nunca deve entregar seu cartão — nem mesmo para "passar na maquininha"
- A troca acontece em segundos durante uma distração
- A senha é capturada visualmente antes ou durante a troca
- Cobrir o teclado ao digitar é a proteção mais eficaz
[/resumo]

Dentro de uma loja, em um momento de desatenção, um cartão sai do bolso da vítima e outro idêntico entra. O processo dura menos de três segundos. O golpe da maquininha — também chamado de golpe da troca de cartão — é um dos crimes mais praticados em ambientes de varejo no Brasil e ainda vitima milhares de pessoas por ano.

A mecânica é simples: um vendedor ou atendente cúmplice utiliza um erro na maquininha (real ou simulado) como pretexto para pedir o cartão ao cliente. Enquanto "tenta resolver o problema" ou entrega o cartão para um colega, realiza a troca por outro cartão visualmente semelhante — mesma bandeira, às vezes mesmo banco. A senha já foi memorizada durante a digitação ou capturada por uma câmera posicionada estrategicamente.

[destaque tipo="perigo"]A única pessoa que deve tocar no seu cartão durante um pagamento é você mesmo. Nunca entregue o cartão a ninguém, independentemente da justificativa.[/destaque]

<h2>Onde mais acontece</h2>

O golpe é mais frequente em estabelecimentos com alto fluxo de clientes e pouca fiscalização: restaurantes populares, pequenas lojas de roupas, feiras livres e postos de gasolina. Mas registros mostram que também ocorre em grandes varejistas, especialmente quando um funcionário age de forma isolada.

[estatistica numero="R$ 4.200" descricao="é o valor médio retirado por golpistas nas primeiras 2 horas após a troca do cartão" fonte="Serasa Experian 2023"]

<h2>Como se proteger na prática</h2>

[checklist titulo="Regras para pagamentos com cartão" tipo="ok"]
- Nunca entregue o cartão — insira, aproxime ou passe você mesmo
- Cubra o teclado com a outra mão ao digitar a senha
- Ao terminar a transação, verifique imediatamente se está com o seu cartão
- Confira que o nome no cartão, o vencimento e os últimos 4 dígitos são os corretos
- Prefira pagamento por aproximação (NFC) — dispensa o uso do chip e da senha
- Ative notificações por push para cada transação no app do banco
[/checklist]

<h2>Se o cartão for trocado</h2>

O problema mais grave não é a perda do plástico — é a janela de tempo entre a troca e o bloqueio. Criminosos agem imediatamente após a separação: realizam saques em caixas eletrônicos próximos e compras em lojas parceiras. Em casos documentados, o saldo completo foi retirado em menos de 40 minutos.

[faq titulo="Perguntas frequentes"]
[faq-item q="Meu banco vai me ressarcir?"]Depende da política de cada banco e das circunstâncias. Se você se deparar com transações não reconhecidas, conteste imediatamente — o banco tem obrigação de investigar. A contestação é mais fácil quando feita nas primeiras horas.[/faq-item]
[faq-item q="Posso rastrear onde as compras foram feitas?"]Sim. O extrato bancário mostra o estabelecimento e horário de cada transação. Essas informações são essenciais para o Boletim de Ocorrência e para a investigação policial.[/faq-item]
[faq-item q="Cartão sem senha (crédito) é mais seguro?"]Não necessariamente. Transações de crédito por aproximação até certo valor dispensam senha, o que significa que um cartão trocado pode ser usado imediatamente para compras mesmo sem a senha.[/faq-item]
[/faq]

[destaque tipo="dica"]Configure um limite diário baixo para transações presenciais no app do banco. Se precisar de um valor maior, você mesmo autoriza pontualmente. Isso limita o prejuízo em caso de clonagem ou troca.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-do-qr-code-falso', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- QR Codes em locais físicos podem ser substituídos por versões falsas com facilidade
- O nome do destinatário é a única verificação que você consegue fazer antes de confirmar
- O golpe é mais comum em restaurantes, estacionamentos e pontos de cobrança pública
- QR Codes enviados por mensagem devem ser tratados com desconfiança extra
[/resumo]

Um quadrado de pixels. Parece inofensivo. Mas desde que o Pix tornou o QR Code o método de pagamento preferido de milhões de brasileiros, esse pequeno ícone se tornou alvo de uma modalidade de golpe sofisticada e difícil de detectar a olho nu.

O golpe do QR Code falso funciona de duas formas principais. Na primeira, o criminoso imprime um QR Code com a chave Pix de sua conta e cola sobre o QR Code legítimo do estabelecimento — em uma sobrecamada tão discreta que só a inspeção cuidadosa revela a adulteração. Na segunda, QR Codes fraudulentos são enviados por e-mail, WhatsApp ou SMS, disfarçados de cobranças de contas de luz, água, telefone ou boletos.

[destaque tipo="perigo"]Antes de confirmar qualquer Pix por QR Code, leia com atenção o nome do destinatário que aparece na tela. Se não reconhecer, cancele imediatamente.[/destaque]

<h2>Por que é difícil detectar</h2>

O QR Code em si não traz informação legível para o olho humano. Você só descobre para onde o pagamento irá depois de escanear — e nesse momento, a atenção da maioria das pessoas está na confirmação do valor, não no nome do destinatário. Os golpistas contam exatamente com esse descuido.

[estatistica numero="37%" descricao="dos usuários de Pix nunca verificam o nome do destinatário antes de confirmar a transação" fonte="Pesquisa OpinionBox 2023"]

<h2>Modalidades do golpe</h2>

[comparativo titulo="Física vs. Digital" col1="QR Code físico adulterado" col2="QR Code enviado por mensagem"]
Colado sobre o original em estabelecimentos | Enviado por e-mail, WhatsApp ou SMS
Vítima está no local e sente urgência de pagar | Vítima recebe cobrança fora do contexto esperado
Difícil detectar sem inspeção física | Fácil de identificar pela URL ou remetente suspeito
Acontece em restaurantes, estacionamentos, lojas | Simula contas de concessionárias e bancos
[/comparativo]

[faq titulo="Perguntas frequentes"]
[faq-item q="Como inspecionar um QR Code físico?"]Toque levemente na superfície do QR Code. Se sentir uma borda ou sobreposição, pode ser um adesivo. Em caso de dúvida, prefira pagar por outro método ou peça ao estabelecimento uma chave Pix para digitar manualmente.[/faq-item]
[faq-item q="E se o nome do destinatário for de uma pessoa física em vez do estabelecimento?"]Isso não é necessariamente um golpe — muitos pequenos negócios usam a chave Pix pessoal do dono. Mas confirme com o atendente antes de pagar, especialmente para valores altos.[/faq-item]
[faq-item q="Paguei para o QR Code errado. E agora?"]Acione o banco imediatamente. O Banco Central tem o Mecanismo Especial de Devolução (MED) para casos de fraude. Registre B.O. e guarde prints de tudo.[/faq-item]
[/faq]

[destaque tipo="dica"]Para pagamentos em estabelecimentos, prefira digitar a chave Pix manualmente após confirmar com o atendente. É mais seguro do que depender de um QR Code que você não pode verificar completamente.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-do-falso-inss', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- O INSS não liga pedindo senhas ou dados bancários
- A Central do INSS é o 135 — ligue de volta se tiver qualquer dúvida
- Aposentados são o principal alvo; o golpe também envolve crédito consignado fraudulento
- Verifique regularmente seus contratos no Meu INSS
[/resumo]

Para muitos aposentados e pensionistas, o benefício do INSS representa a principal — às vezes a única — fonte de renda. É por isso que golpes direcionados a esse público causam dano tão devastador: não roubam apenas dinheiro, mas a segurança de quem depende daquela quantia para viver.

O golpe do falso INSS funciona com uma premissa simples e eficaz: criar urgência em torno do benefício. O criminoso liga se identificando como funcionário do INSS, do Ministério da Previdência Social ou de um "correspondente bancário credenciado". A mensagem varia, mas o objetivo é sempre o mesmo: fazer a vítima agir rápido, sem pensar.

[destaque tipo="perigo"]O INSS não entra em contato por telefone para solicitar senhas, dados bancários ou orientar saques. Qualquer ligação com esse teor é golpe.[/destaque]

<h2>Os roteiros mais comuns</h2>

Os criminosos utilizam diferentes abordagens dependendo do perfil da vítima:

**Revisão de benefício**: O golpista informa que a vítima tem direito a valores retroativos ou a um aumento no benefício, mas precisa "confirmar dados" ou "assinar um documento" para receber.

**Bloqueio pendente**: A vítima é informada de que seu CPF tem uma irregularidade e o benefício será suspenso caso não tome uma ação imediata — geralmente fornecer dados ou comparecer a um banco específico.

**Crédito consignado**: O criminoso oferece um empréstimo consignado com condições "exclusivas" e pressiona para a assinatura do contrato, muitas vezes sem que a vítima entenda o que está contratando.

[estatistica numero="R$ 2,4 bi" descricao="em fraudes previdenciárias e consignadas detectadas pelo INSS em 2023" fonte="Ministério da Previdência Social"]

<h2>O consignado fraudulento</h2>

Uma variante especialmente prejudicial envolve contratos de crédito consignado assinados sem o pleno entendimento da vítima — ou com a assinatura do idoso obtida sob pressão ou mediante documentos confusos. O desconto começa automaticamente no benefício no mês seguinte, e a vítima só percebe o problema quando o valor recebido diminui significativamente.

[faq titulo="Perguntas frequentes"]
[faq-item q="Como verificar se há contratos de consignado em meu nome?"]Acesse o aplicativo Meu INSS (Gov.br) e vá em "Extrato de Empréstimos". Todos os contratos ativos aparecem lá com nome da instituição, valor e prazo.[/faq-item]
[faq-item q="O que fazer se encontrar um contrato que não fiz?"]Ligue para a Central 135 e relate. Em seguida, registre Boletim de Ocorrência e procure a ouvidoria do banco envolvido. O INSS pode suspender o desconto durante a investigação.[/faq-item]
[faq-item q="Como evitar que familiares idosos sejam vítimas?"]Oriente-os a nunca fornecer dados por telefone e a ligar sempre para o 135 antes de qualquer ação. Se possível, cadastre um representante legal de confiança no sistema do INSS.[/faq-item]
[/faq]

[destaque tipo="dica"]Acesse o Meu INSS pelo menos uma vez por mês para verificar seu extrato de benefícios e a lista de contratos ativos. Qualquer movimentação não reconhecida deve ser reportada imediatamente.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-do-falso-delivery', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Plataformas legítimas de delivery não pedem pagamento antecipado fora do app
- Perfis falsos copiam fotos e cardápios de restaurantes reais
- O prejuízo médio é menor, mas o volume de vítimas é alto
- Denunciar o perfil protege outros consumidores
[/resumo]

A fome é um gatilho poderoso. E os golpistas do falso delivery sabem exatamente como usá-la. Com a popularização dos aplicativos de entrega de comida, surgiu uma modalidade de golpe que mistura o desejo imediato do consumidor com a pressa natural de uma refeição: o restaurante falso.

A operação começa com a criação de um perfil em redes sociais — geralmente no Instagram ou no Facebook — com fotos de pratos apetitosos, cardápio completo, horários de funcionamento e endereço. Tudo copiado de estabelecimentos reais. O contato é feito exclusivamente por WhatsApp, onde o "atendente" confirma a disponibilidade dos itens e solicita pagamento antecipado via Pix.

[destaque tipo="perigo"]Nenhum restaurante ou serviço de delivery legítimo solicita pagamento antecipado via Pix pelo WhatsApp antes de confirmar a entrega.[/destaque]

<h2>Por que esse golpe funciona</h2>

Diferente de outros golpes que exploram medo, o falso delivery explora o desejo e a conveniência. A vítima está com fome, quer resolver a situação rapidamente e não está em modo de vigilância. O valor normalmente é pequeno — entre R$ 50 e R$ 200 — o que reduz a barreira psicológica para o pagamento e diminui a probabilidade de denúncia.

Além disso, os perfis são bem elaborados: têm seguidores (comprados), algumas avaliações positivas (falsas) e um atendimento rápido e educado. A desconfiança só surge quando a entrega não aparece e o número para de responder.

[estatistica numero="R$ 180" descricao="é o valor médio perdido por vítima no golpe do falso delivery" fonte="Procon-SP 2023"]

<h2>Como identificar um perfil falso</h2>

[checklist titulo="Sinais de restaurante falso" tipo="warn"]
- Perfil com poucas publicações, todas recentes
- Comentários genéricos demais ("Ótimo!", "Amei!")
- Apenas contato por WhatsApp — sem presença nos apps oficiais de delivery
- Preços significativamente abaixo do mercado
- Fotos com marca d'água ou estilo diferente entre si (copiadas de fontes diferentes)
- Sem CNPJ visível ou endereço verificável no Google Maps
[/checklist]

[faq titulo="Perguntas frequentes"]
[faq-item q="Como verificar se o restaurante é real?"]Pesquise o nome no Google Maps. Estabelecimentos reais têm endereço físico, avaliações ao longo do tempo e muitas vezes aparecem nos aplicativos oficiais de delivery. Se só existe no Instagram, desconfie.[/faq-item]
[faq-item q="Paguei e não recebi. O que fazer?"]Registre Boletim de Ocorrência (pode ser online pela delegacia digital do seu estado). Acione o banco para tentativa de estorno e denuncie o perfil no Instagram/Facebook como "Fraude ou golpe".[/faq-item]
[faq-item q="Aplicativos de delivery são mais seguros?"]Sim. Nos apps oficiais (iFood, Rappi, 99Food), o pagamento é processado pela plataforma, que funciona como intermediária. Em caso de problema com o pedido, há mecanismo de reembolso. Prefira sempre pedir pelos apps.[/faq-item]
[/faq]

[destaque tipo="dica"]Antes de fazer o primeiro pedido em qualquer perfil de restaurante nas redes sociais, pesquise o nome no Google + "reclamação" e verifique se o estabelecimento existe no Google Maps com avaliações ao longo do tempo.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-do-investimento-falso-piramide', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Retorno garantido não existe em investimentos legítimos
- Esquemas de pirâmide eventualmente colapsam — os últimos a entrar perdem tudo
- A CVM e o Banco Central têm listas de empresas não autorizadas
- Criptomoedas são frequentemente usadas para dificultar o rastreamento
[/resumo]

"Invista R$ 5.000 e receba R$ 1.000 por mês, garantido." A frase parece absurda quando escrita assim, mas embutida em uma apresentação profissional, cercada de depoimentos entusiasmados e apresentada por alguém de confiança, ela se torna convincente o suficiente para ter destruído fortunas de milhares de brasileiros.

Os golpes de investimento falso — conhecidos como esquemas Ponzi ou pirâmides financeiras — existem há mais de um século, mas nunca foram tão sofisticados. As versões modernas usam plataformas digitais com aparência de corretoras internacionais, tokens de criptomoedas proprietários e um vocabulário técnico elaborado para criar credibilidade.

[destaque tipo="perigo"]Qualquer investimento que prometa rendimento fixo garantido, independentemente das condições de mercado, é ilegal e fraudulento. Não existe isso em investimentos legítimos.[/destaque]

<h2>Como o esquema funciona</h2>

A matemática de uma pirâmide é implacável. Os rendimentos dos primeiros investidores são pagos com o dinheiro dos mais recentes. Enquanto há novos entrantes, o sistema se sustenta. Quando o fluxo de captação diminui — inevitavelmente — os saques são bloqueados, o sistema colapsa e a esmagadora maioria dos participantes perde tudo que investiu.

O elemento mais devastador é a estrutura de indicação: cada participante é incentivado a recrutar amigos e familiares, tornando-se involuntariamente um vetor de prejuízo para as pessoas mais próximas.

[estatistica numero="R$ 4,3 bi" descricao="em prejuízos para investidores brasileiros em esquemas fraudulentos entre 2019 e 2023" fonte="CVM"]

<h2>Verificação antes de investir</h2>

[checklist titulo="O que verificar antes de qualquer investimento" tipo="ok"]
- Confirme se a empresa tem autorização do Banco Central (para bancos e financeiras)
- Confirme se tem autorização da CVM (para gestoras e corretoras)
- Pesquise o nome da empresa no sistema "Alerta" da CVM
- Verifique se o CNPJ existe e está ativo na Receita Federal
- Busque reclamações no Reclame Aqui e no Procon
- Desconfie de qualquer retorno prometido acima de 1% ao mês (CDI atual como referência)
[/checklist]

[faq titulo="Perguntas frequentes"]
[faq-item q="Criptomoedas são golpes?"]Não necessariamente. Criptomoedas são ativos reais negociados em exchanges regulamentadas. O golpe é quando alguém oferece rendimento fixo garantido em cripto ou cria uma "moeda" própria sem valor real.[/faq-item]
[faq-item q="Posso recuperar o dinheiro perdido em uma pirâmide?"]Em muitos casos não, especialmente quando os organizadores desaparecem com o capital. Registre B.O. e notifique a CVM e o Ministério Público — isso aumenta as chances de investigação e eventual reparação coletiva.[/faq-item]
[faq-item q="Como consultar se uma empresa está na lista negra da CVM?"]Acesse cvm.gov.br e busque por "Alertas ao Mercado". A CVM publica regularmente avisos sobre entidades não autorizadas.[/faq-item]
[/faq]

[destaque tipo="importante"]Se alguém na sua rede social ou círculo de amigos está promovendo um investimento com retorno "garantido", provavelmente já foi captado pelo esquema. Ao invés de entrar, ajude-o a verificar a legitimidade da empresa antes que o prejuízo se amplie.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-da-conta-salario-bloqueada', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Bancos nunca pedem que você saque dinheiro para "desbloquear" uma conta
- A pressão por urgência é a principal ferramenta do golpista
- O golpe é mais eficaz próximo a datas de pagamento de salário
- A única verificação válida é pelo app do próprio banco
[/resumo]

O telefone toca em uma tarde comum. A voz do outro lado é séria, profissional: "Senhor(a), identificamos uma irregularidade em sua conta salário. Por medida de segurança, o banco precisará bloquear o saldo caso o problema não seja resolvido nas próximas horas." O coração acelera. Afinal, aquele dinheiro é o salário do mês.

O golpe da conta salário bloqueada opera com uma combinação precisa de urgência, autoridade e medo financeiro. O criminoso se identifica como funcionário do banco, do departamento de segurança ou até do RH da empresa empregadora. A solução apresentada é sempre a mesma: sacar o dinheiro imediatamente e depositá-lo em uma "conta segura" enquanto o problema é resolvido.

[destaque tipo="perigo"]Nenhum banco, em nenhuma circunstância, pede que o cliente saque dinheiro e transfira para outra conta como procedimento de segurança. Isso é sempre um golpe.[/destaque]

<h2>O timing do golpe</h2>

Não é coincidência que o golpe aconteça frequentemente nos dias seguintes ao pagamento de salários — os criminosos têm acesso a informações sobre ciclos de pagamento de diversas categorias profissionais. Funcionários públicos, por exemplo, são alvos frequentes nos dias seguintes ao dia 5 de cada mês, quando seus salários costumam cair.

A urgência criada pelo golpista tem prazo: "você tem até o final do expediente bancário". Isso impede que a vítima consulte familiares, pesquise sobre o procedimento ou ligue para o banco pelo número oficial.

[estatistica numero="68%" descricao="das vítimas do golpe da conta salário bloqueada transferiram o dinheiro no mesmo dia da ligação" fonte="Febraban 2023"]

<h2>Por que é eficaz</h2>

O medo de perder o acesso ao salário ativa um estado emocional que prejudica o raciocínio crítico. Além disso, os criminosos frequentemente têm dados básicos da vítima — nome, banco, últimos dígitos do cartão — obtidos em vazamentos, o que torna a abordagem muito convincente.

[faq titulo="Perguntas frequentes"]
[faq-item q="Como verificar se minha conta realmente está bloqueada?"]Abra o app do banco. Se conseguir acessar e ver o saldo, a conta não está bloqueada. Se não conseguir acessar, ligue para o número do banco que está no verso do seu cartão — não o número de quem te ligou.[/faq-item]
[faq-item q="E se o número que apareceu no celular era realmente do banco?"]Números podem ser falsificados com técnicas de spoofing. Desligue e ligue de volta pelo número oficial impresso no verso do cartão ou no site oficial do banco.[/faq-item]
[faq-item q="Meu empregador pode me ligar pedindo para movimentar minha conta salário?"]Não. Empregadores não têm acesso às suas contas bancárias e nunca têm motivo legítimo para pedir movimentações financeiras.[/faq-item]
[/faq]

[destaque tipo="dica"]Salve o número oficial do seu banco na sua agenda telefônica agora, antes de precisar. Em um momento de stress, ter o número acessível impede que você ligue para o número errado — ou para o golpista de volta.[/destaque]
CONTENT
, $force);

seed_editorial('golpe-do-emprestimo-nome-da-vitima', 'golpe', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- O golpe é silencioso — a vítima não percebe no momento em que acontece
- Dados obtidos em vazamentos são suficientes para contratar crédito em seu nome
- A descoberta costuma acontecer meses depois, pela cobrança ou negativação
- Monitoramento preventivo do CPF é a melhor defesa
[/resumo]

Você nunca pediu empréstimo algum. Mas um dia chega uma carta de cobrança, ou uma mensagem do Serasa, ou simplesmente um cartão de crédito que você não solicitou. Alguém usou seu nome, seu CPF e seus dados pessoais para contratar crédito — e desapareceu com o dinheiro.

O golpe do empréstimo em nome da vítima — tecnicamente chamado de fraude de identidade em crédito — cresceu exponencialmente com a digitalização do sistema financeiro. A abertura de contas digitais sem presença física, combinada com o volume de dados pessoais disponíveis em vazamentos, criou um ambiente favorável para criminosos que operam remotamente.

[destaque tipo="perigo"]Com nome completo, CPF, data de nascimento e foto de documento, criminosos conseguem abrir contas e contratar crédito em fintechs e bancos digitais sem que a vítima perceba.[/destaque]

<h2>De onde vêm os dados</h2>

Os dados pessoais usados nesse tipo de golpe têm diferentes origens. Vazamentos de grandes bases de dados expuseram informações de centenas de milhões de brasileiros. Em janeiro de 2021, um único vazamento expôs dados de 223 milhões de CPFs — praticamente toda a população do país. Esses dados circulam em grupos criminosos e são vendidos por valores irrisórios.

[estatistica numero="223 milhões" descricao="de CPFs brasileiros expostos no maior vazamento de dados da história do país, em 2021" fonte="Dfndr Lab / PSafe"]

<h2>Como identificar que foi vítima</h2>

[checklist titulo="Sinais de que seus dados foram usados" tipo="warn"]
- Cobrança de dívida que você não reconhece
- Negativação no Serasa, SPC ou Boa Vista sem motivo aparente
- Chegada de cartões de crédito que não solicitou
- E-mail de boas-vindas de banco que nunca acessou
- SMS de confirmação de cadastro em serviço desconhecido
- Contrato no Registrato do Banco Central que não reconhece
[/checklist]

<h2>Monitoramento preventivo</h2>

A principal defesa contra esse golpe não é reativa — é preventiva. Existem ferramentas gratuitas que permitem monitorar o uso do seu CPF:

[passo numero="1" titulo="Registrato — Banco Central"]Acesse registrato.bcb.gov.br com login Gov.br. Veja todos os relacionamentos bancários em seu nome, empréstimos ativos e chaves Pix cadastradas.[/passo]

[passo numero="2" titulo="Serasa e Boa Vista"]Crie contas gratuitas no Serasa.com.br e BoaVistaConsumidor.com.br. Ative alertas por e-mail para qualquer consulta ao seu CPF.[/passo]

[passo numero="3" titulo="Gov.br — Conta verificada"]Acesse gov.br e verifique se há serviços públicos ou contratos em seu nome que você não reconhece.[/passo]

[faq titulo="Perguntas frequentes"]
[faq-item q="O banco pode contratar crédito sem minha assinatura?"]Não de forma legítima. Se um contrato foi aberto com dados falsificados, a responsabilidade é da instituição financeira que não validou adequadamente a identidade. Você pode exigir o cancelamento e reparação de danos.[/faq-item]
[faq-item q="Posso bloquear preventivamente o uso do meu CPF para crédito?"]Sim. Alguns bureaus de crédito oferecem o serviço de "Congelamento de Crédito" (credit freeze), que impede novas consultas sem sua autorização. O Serasa oferece essa funcionalidade no app.[/faq-item]
[faq-item q="Preciso de advogado?"]Para contestar empréstimos fraudulentos diretamente com o banco, geralmente não. Se o banco se recusar a cancelar a dívida, o Procon e o Banco Central são os próximos passos. Advogado só é necessário para ação judicial posterior.[/faq-item]
[/faq]
CONTENT
, $force);

/* ─────────────────────────────────────────
   FRAUDES
───────────────────────────────────────── */

WP_CLI::log('');
WP_CLI::log('=== Conteúdo editorial — Fraudes ===');

seed_editorial('fraude-de-identidade-account-takeover', 'fraude', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Account takeover é a tomada de controle de uma conta existente, diferente de abrir conta nova em seu nome
- SIM swap — a transferência fraudulenta do seu número de celular — é uma porta de entrada comum
- Autenticação por SMS é menos segura do que por aplicativo autenticador
- A detecção precoce limita o prejuízo significativamente
[/resumo]

Você está no trabalho quando percebe que o celular perdeu o sinal. Tenta reiniciar, troca de local, sem sucesso. Depois de alguns minutos, uma notificação no e-mail avisa que a senha do app bancário foi alterada. Em outro dispositivo. Isso é um SIM swap em andamento — e é uma das formas mais eficientes de Account Takeover.

A fraude de identidade por tomada de conta (Account Takeover ou ATO) difere do golpe de empréstimo em nome da vítima em um aspecto crucial: aqui, o criminoso não cria uma nova identidade financeira — ele assume o controle da sua conta real. Com acesso total ao app bancário, pode transferir saldo, solicitar empréstimos, alterar dados cadastrais e bloquear o acesso do titular legítimo.

[destaque tipo="perigo"]SIM swap é quando um criminoso convence a operadora de celular a transferir seu número para um chip dele. Com seu número, recebe todos os seus SMS — inclusive códigos de autenticação bancária.[/destaque]

<h2>As principais portas de entrada</h2>

[comparativo titulo="Como o criminoso obtém acesso" col1="Método" col2="Como se proteger"]
SIM Swap (troca fraudulenta de chip) | Cadastre senha junto à operadora para portabilidade
Phishing de credenciais | Nunca insira dados em links recebidos por e-mail ou SMS
Vazamento de senha reutilizada | Use senhas únicas para cada serviço (gerenciador de senhas)
Malware no dispositivo | Mantenha antivírus atualizado e evite apps fora das lojas oficiais
Engenharia social com suporte | Banco nunca pede token ou senha por telefone
[/comparativo]

[estatistica numero="340%" descricao="de aumento nos casos de Account Takeover no Brasil entre 2020 e 2023" fonte="ClearSale"]

<h2>Como descobrir que sua conta foi comprometida</h2>

[checklist titulo="Sinais de Account Takeover" tipo="warn"]
- Perda repentina de sinal de celular (possível SIM swap)
- E-mails de alteração de senha ou dados que você não solicitou
- Notificação de login em dispositivo desconhecido
- Transações não reconhecidas no extrato
- Não conseguir mais acessar o app bancário com suas credenciais
- Recebimento de novos cartões ou documentos que não solicitou
[/checklist]

[faq titulo="Perguntas frequentes"]
[faq-item q="Autenticação em dois fatores por SMS é segura?"]É melhor do que só senha, mas é vulnerável ao SIM swap. O ideal é usar um aplicativo autenticador (Google Authenticator, Authy, Microsoft Authenticator) que gera códigos localmente, sem depender do número de celular.[/faq-item]
[faq-item q="O banco ressarce em casos de ATO?"]Depende das circunstâncias. Se o acesso foi obtido por falha de segurança da própria instituição (como validação insuficiente de identidade), o banco tem responsabilidade. Se a vítima instalou malware ou cedeu credenciais, a análise é mais complexa.[/faq-item]
[faq-item q="Como proteger minha conta junto à operadora?"]Ligue para sua operadora e solicite o cadastro de uma senha para autorizar qualquer portabilidade ou troca de chip. Esse serviço é gratuito e dificulta significativamente o SIM swap.[/faq-item]
[/faq]

[destaque tipo="dica"]Ative alertas por push para cada transação no app do banco. Quanto mais rápido você identificar uma movimentação suspeita, maior a chance de reversão.[/destaque]
CONTENT
, $force);

seed_editorial('fraude-skimming-clonagem-cartao-atm', 'fraude', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Skimming é a instalação de dispositivos ilegais em caixas eletrônicos para copiar dados do cartão
- A câmera ou teclado falso captura a senha — sem ela, os dados do chip não são suficientes
- Cobrir o teclado ao digitar a senha é a defesa mais simples e eficaz
- Pagamento por aproximação (NFC) é imune ao skimming físico
[/resumo]

Você faz um saque no caixa eletrônico, a transação aparece normal, e segue seu dia. Três horas depois, seu celular vibra: uma notificação de compra em outra cidade que você não fez. Em algum momento, em algum ATM, um dispositivo invisível copiou os dados do seu cartão.

Skimming é o nome dado à instalação de equipamentos ilegais em caixas eletrônicos ou maquininhas de cobrança com o objetivo de capturar os dados da tarja magnética dos cartões. Diferente de outros crimes financeiros, o skimming não exige nenhuma interação com a vítima — acontece de forma completamente silenciosa durante uma transação aparentemente normal.

[destaque tipo="importante"]O skimming captura os dados da tarja magnética — não do chip. Por isso cartões com chip são mais seguros, e transações por aproximação (NFC) são imunes a essa modalidade de fraude.[/destaque]

<h2>Como os dispositivos são instalados</h2>

Os criminosos instalam um leitor falso (skimmer) sobre a entrada original do cartão no ATM. Esse dispositivo é projetado para ser quase indistinguível do original — mesma cor, textura e encaixe. Em paralelo, uma câmera minúscula é posicionada para capturar a digitação da senha, ou um teclado sobreposto mais sensível registra os pressionamentos.

Os dados coletados são armazenados no skimmer ou transmitidos via Bluetooth para o criminoso nas proximidades. Com tarja e senha em mãos, fabricam cartões clonados usados para saques em outros estados ou países.

[estatistica numero="R$ 2,1 bi" descricao="em perdas com fraudes em cartões no Brasil em 2023, parte significativa por clonagem" fonte="Abecs"]

<h2>Como inspecionar um ATM antes de usar</h2>

[checklist titulo="Inspeção rápida antes de usar o caixa" tipo="ok"]
- Puxe levemente o leitor de cartão — skimmers são sobrepositivos e podem ceder
- Verifique se o teclado está bem fixado e tem a mesma textura em todos os botões
- Observe se há câmeras anômalas acima ou ao lado do teclado (pequenos orifícios)
- Compare com outros ATMs do mesmo banco próximos — diferenças são suspeitas
- Prefira ATMs dentro de agências bancárias com monitoramento e movimento
[/checklist]

[faq titulo="Perguntas frequentes"]
[faq-item q="Cartão com chip pode ser clonado por skimming?"]Os dados do chip não podem ser lidos pelo skimmer. Porém, muitos sistemas ainda mantêm a tarja magnética como fallback — e é ela que é clonada. Por isso, prefira sempre transações por chip ou aproximação.[/faq-item]
[faq-item q="O banco é responsável pelo skimming em seu ATM?"]Sim. O banco tem responsabilidade objetiva por fraudes em seus terminais. Conteste as transações imediatamente e registre B.O. — o banco é obrigado a investigar e, se confirmada a fraude, ressarcir.[/faq-item]
[faq-item q="Como usar ATM com mais segurança?"]Prefira ATMs dentro de agências, cubra sempre o teclado com a outra mão, e ative notificações de transação no app. Configure um limite de saque diário baixo pelo app.[/faq-item]
[/faq]

[destaque tipo="dica"]Se notar algo estranho em um ATM — peça solta, teclado diferente, câmera suspeita — não use e avise o banco. Muitas investigações de skimming começam com relatos de clientes.[/destaque]
CONTENT
, $force);

seed_editorial('fraude-no-financiamento-de-veiculo', 'fraude', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Criminosos usam identidades roubadas para financiar veículos sem pagar as parcelas
- A vítima descobre meses depois pela cobrança ou negativação
- Verifique regularmente o Detran e o Registrato para checar propriedades em seu nome
- Documentos perdidos ou roubados exigem ação imediata de registro de ocorrência
[/resumo]

Nenhum carro comprado, nenhuma concessionária visitada, nenhum contrato assinado. Mas um dia chega uma cobrança de parcelas atrasadas de um financiamento de R$ 60.000 — e o veículo está registrado no Detran no seu nome. A fraude de financiamento de veículo é um dos crimes de identidade com maior impacto financeiro individual no Brasil.

A operação funciona com dados pessoais obtidos em vazamentos ou roubos de documentos. Com nome completo, CPF, RG e comprovante de renda falsificado, criminosos se apresentam a concessionárias ou financeiras como compradores legítimos. O veículo é liberado, as parcelas jamais são pagas, e a dívida — registrada em nome da vítima — cresce com juros e multas até a negativação.

[destaque tipo="perigo"]Se você perdeu ou teve documentos roubados, registre Boletim de Ocorrência imediatamente. Esse registro é a sua principal proteção legal contra fraudes de identidade subsequentes.[/destaque]

<h2>O circuito da fraude</h2>

[passo numero="1" titulo="Obtenção dos dados"]Criminosos compram dados em fóruns clandestinos ou os obtêm por meio de documentos perdidos, phishing ou vazamentos de bases de dados.[/passo]

[passo numero="2" titulo="Falsificação de documentos"]Com os dados reais da vítima, constroem um conjunto de documentos com foto diferente — a do criminoso. Em alguns casos, envolvem despachantes desonestos ou funcionários corruptos de financeiras.[/passo]

[passo numero="3" titulo="Aprovação do crédito"]O financiamento é aprovado por uma instituição que não validou adequadamente a identidade do solicitante. O veículo é retirado.[/passo]

[passo numero="4" titulo="Descoberta pela vítima"]Meses depois, a vítima recebe cobranças, tem o nome negativado ou descobre a irregularidade ao tentar financiar algo em seu próprio nome.[/passo]

[estatistica numero="R$ 3,8 bi" descricao="em fraudes no crédito para veículos registradas no Brasil em 2022" fonte="Serasa Experian"]

[faq titulo="Perguntas frequentes"]
[faq-item q="Como verificar se há veículo financiado em meu nome?"]Acesse o Registrato do Banco Central (registrato.bcb.gov.br) para ver contratos de crédito. Para verificar registro de veículo, consulte o site do Detran do seu estado com seu CPF.[/faq-item]
[faq-item q="A concessionária ou financeira tem responsabilidade?"]Sim. A instituição que aprovou o crédito sem validação adequada de identidade tem responsabilidade objetiva. Você pode exigir o cancelamento do contrato e a exclusão do seu nome de qualquer lista de inadimplentes.[/faq-item]
[faq-item q="Preciso pagar as parcelas enquanto disputo a fraude?"]Não — desde que notifique o banco e registre B.O. Documente tudo e leve ao Procon se o banco se recusar a suspender a cobrança durante a investigação.[/faq-item]
[/faq]

[destaque tipo="dica"]Consulte o Registrato pelo menos uma vez a cada seis meses. O serviço é gratuito, exige apenas login no Gov.br, e mostra todos os contratos de crédito em seu nome — incluindo aqueles que você não fez.[/destaque]
CONTENT
, $force);

seed_editorial('fraude-phishing-bancario', 'fraude', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Phishing bancário replica com precisão o visual dos bancos para enganar clientes
- A URL do site é sempre diferente do domínio oficial — verifique sempre
- Nunca acesse o banco por links recebidos em e-mail, SMS ou WhatsApp
- A urgência na mensagem é uma característica quase universal do phishing
[/resumo]

A mensagem parece legítima. O logo do banco está correto, as cores são as certas, o texto é formal e profissional. Há apenas um detalhe diferente — e ele é quase imperceptível: o endereço do site termina em ".net" em vez de ".com.br", ou tem uma letra extra, ou usa um subdomínio como "seguro.banco-original.site". Esse detalhe é o único rastro visível de uma armadilha cuidadosamente construída.

Phishing bancário é a fraude que usa páginas falsas, e-mails e mensagens fraudulentas para coletar credenciais de acesso a contas bancárias. O nome vem do inglês "fishing" (pescar) — uma analogia ao anzol digital lançado na esperança de que alguém morda.

[destaque tipo="perigo"]Seu banco nunca enviará um link pedindo que você insira login, senha ou token. Se uma mensagem faz isso, é phishing — independentemente de como parece.[/destaque]

<h2>Anatomia de um ataque de phishing</h2>

O ciclo de um phishing bancário moderno é rápido e automatizado. A mensagem chega, a vítima clica, insere os dados na página falsa — e os criminosos já estão usando essas credenciais em tempo real no site verdadeiro do banco. Alguns sistemas de phishing chegam a redirecionar o usuário para o site real após capturar os dados, para que a vítima não perceba o que aconteceu.

[estatistica numero="R$ 1,8 bi" descricao="em perdas causadas por phishing bancário no Brasil em 2023" fonte="Kaspersky"]

<h2>Como identificar uma mensagem de phishing</h2>

[checklist titulo="Sinais de phishing bancário" tipo="warn"]
- URL com domínio diferente do oficial do banco (verifique letra por letra)
- Mensagem com urgência extrema: "sua conta será bloqueada em 24h"
- Erros de português, espaçamento ou formatação estranha
- Remetente de e-mail diferente do domínio oficial (ex: banco@gmail.com)
- Solicitação de token, código de confirmação ou senha completa
- Botão "Clique aqui" que leva a um link diferente do texto exibido
[/checklist]

<h2>Phishing por SMS e WhatsApp</h2>

O smishing (phishing por SMS) e o whishing (por WhatsApp) cresceram com a adoção do celular como principal dispositivo bancário. Mensagens com links encurtados (bit.ly, t.co) escondem o destino real e impedem a verificação da URL antes de clicar.

[faq titulo="Perguntas frequentes"]
[faq-item q="Abri o link mas não inseri dados. Fui hackeado?"]Depende. Apenas acessar uma página de phishing geralmente não instala malware. Mas se o site pediu para instalar algo ou o seu dispositivo está com comportamento estranho, faça uma verificação com antivírus.[/faq-item]
[faq-item q="Como verificar se um site é o oficial do banco?"]Digite o endereço manualmente no navegador — nunca copie de mensagens. O site oficial do banco estará nos documentos físicos (extrato, cartão), no aplicativo e nas redes sociais verificadas da instituição.[/faq-item]
[faq-item q="O banco pode me ressarcir se cai em phishing?"]Se o phishing aproveitou uma vulnerabilidade técnica do banco ou se o banco falhou em detectar a transação suspeita, há responsabilidade compartilhada. Cada caso é analisado individualmente.[/faq-item]
[/faq]

[destaque tipo="dica"]Instale um gerenciador de senhas. Além de criar senhas fortes e únicas para cada serviço, ele não preencherá automaticamente suas credenciais em sites falsos — porque reconhece que o domínio é diferente do original.[/destaque]
CONTENT
, $force);

seed_editorial('fraude-no-fgts', 'fraude', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Criminosos usam dados de trabalhadores para sacar FGTS sem autorização
- O saque emergencial e a modalidade aniversário são os mais visados
- Monitore seu saldo pelo app FGTS regularmente
- A Caixa tem processo específico para contestação de saques fraudulentos
[/resumo]

O FGTS — Fundo de Garantia do Tempo de Serviço — representa anos de trabalho acumulados. Para muitos trabalhadores brasileiros, é a única reserva financeira disponível. É exatamente por isso que criminosos o visam: um saldo que existe, que tem valor e que pode ser acessado digitalmente por quem tiver os dados certos.

A fraude no FGTS funciona a partir de dados pessoais roubados ou obtidos em vazamentos. Com CPF, nome completo e data de nascimento — informações expostas em múltiplos vazamentos nos últimos anos — criminosos acessam o aplicativo FGTS da Caixa Econômica Federal ou criam uma conta Gov.br em nome da vítima para solicitar saques.

[destaque tipo="perigo"]Se você recebeu alguma comunicação da Caixa sobre movimentação no FGTS que não reconhece, ligue imediatamente para o 0800 726 0101 e bloqueie o acesso.[/destaque]

<h2>As modalidades mais visadas</h2>

**Saque emergencial**: Durante períodos em que o governo libera saques emergenciais do FGTS, criminosos atuam em massa, aproveitando o volume de transações para diluir a detecção.

**Modalidade aniversário**: A adesão à modalidade aniversário — que permite saques anuais de parte do saldo — é feita digitalmente e pode ser ativada fraudulentamente. Uma vez ativa, permite saques que reduzem significativamente o saldo total.

**Simulação de demissão**: Em casos mais elaborados, criminosos conseguem simular uma rescisão de contrato de trabalho para acionar o saque total do fundo.

[estatistica numero="R$ 1,4 bi" descricao="em saques fraudulentos do FGTS identificados pela Caixa Econômica Federal entre 2021 e 2023" fonte="CGU"]

<h2>Como monitorar seu FGTS</h2>

[checklist titulo="Monitoramento preventivo" tipo="ok"]
- Instale o app FGTS (Caixa) e verifique o saldo mensalmente
- Ative notificações no app para movimentações
- Acesse seu extrato de saques — qualquer saque não reconhecido deve ser contestado
- Verifique se está ou não na modalidade aniversário — se não optou, não deveria estar
- Mantenha seu e-mail e telefone atualizados no cadastro para receber alertas
[/checklist]

[faq titulo="Perguntas frequentes"]
[faq-item q="Como contestar um saque fraudulento no FGTS?"]Ligue para o 0800 726 0101 da Caixa, registre Boletim de Ocorrência e vá pessoalmente a uma agência com o B.O. A Caixa tem processo específico para fraudes — o saldo pode ser restituído após investigação.[/faq-item]
[faq-item q="O que é a modalidade aniversário e como sei se fui inscrito sem querer?"]Na modalidade aniversário, você saca parte do FGTS todo ano no mês do seu aniversário, mas perde o direito ao saque total em caso de demissão. Verifique no app FGTS em qual modalidade você está cadastrado.[/faq-item]
[faq-item q="A Caixa ressarce saques fraudulentos?"]Sim, após investigação. O processo pode levar meses. O B.O. registrado e a comunicação imediata são fundamentais para o sucesso da contestação.[/faq-item]
[/faq]

[destaque tipo="importante"]Verifique periodicamente se o e-mail e o telefone cadastrados no seu Gov.br são realmente seus. Se um criminoso alterou esses dados, receberá as notificações de movimentação — e você não.[/destaque]
CONTENT
, $force);

seed_editorial('fraude-boleto-bancario-adulterado', 'fraude', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Boletos podem ser adulterados no computador da vítima (malware) ou enviados falsificados por e-mail
- O nome do beneficiário na tela de confirmação é a verificação essencial
- Mesmo que o valor seja idêntico, um boleto adulterado paga para a conta errada
- Antivírus atualizado é proteção fundamental contra malware de boleto
[/resumo]

O boleto bancário é um dos métodos de pagamento mais usados no Brasil — especialmente por pessoas jurídicas, para pagamento de fornecedores, aluguel e serviços. Exatamente por isso, a fraude de boleto adulterado causa prejuízos que frequentemente superam dezenas de milhares de reais em uma única transação.

A fraude funciona de duas formas principais. Na primeira — e mais difícil de detectar — um malware instalado no computador da vítima intercepta boletos abertos no navegador e altera silenciosamente os dados do beneficiário antes de exibir a tela de pagamento. A vítima vê o nome correto, o valor correto — mas o código de barras já foi substituído. Na segunda forma, boletos falsificados são enviados por e-mail, disfarçados como cobranças legítimas.

[destaque tipo="perigo"]Malware de boleto altera o código de barras em tempo real, sem modificar o restante da aparência do documento. A única forma de detectar é comparar o CPF/CNPJ do beneficiário na tela de confirmação do banco.[/destaque]

<h2>Como o malware de boleto funciona</h2>

O malware é instalado geralmente por meio de e-mails com anexos maliciosos, downloads de software pirata ou cliques em links infectados. Uma vez instalado, monitora silenciosamente a atividade do navegador. Quando detecta um boleto sendo processado, intercepta a requisição e substitui os dados bancários — mantendo o visual idêntico.

O usuário paga normalmente, recebe o comprovante — e o dinheiro vai para a conta do criminoso. O beneficiário legítimo cobra a dívida semanas depois, quando as parcelas não aparecem.

[estatistica numero="R$ 2,7 bi" descricao="em fraudes com boletos adulterados no Brasil entre 2020 e 2023" fonte="Febraban"]

<h2>Verificação obrigatória antes de pagar</h2>

[checklist titulo="Checklist antes de pagar qualquer boleto" tipo="ok"]
- Confirme o nome do beneficiário na tela de confirmação do banco — deve corresponder ao emissor
- Verifique os primeiros dígitos do código de barras (correspondentes ao banco emissor)
- Para boletos de alto valor, ligue para o emissor e confirme o código
- Nunca pague boletos de e-mails não esperados sem verificar a origem
- Mantenha o antivírus atualizado no computador que usa para pagar boletos
- Prefira pagar pelo app bancário do celular — menos vulnerável a malware de desktop
[/checklist]

[faq titulo="Perguntas frequentes"]
[faq-item q="Posso pedir estorno de um boleto pago para conta errada?"]Se foi fraude comprovada, sim. Acione o banco imediatamente, registre B.O. e apresente a prova de que o boleto original era de outro beneficiário. O prazo de investigação varia por banco.[/faq-item]
[faq-item q="Quem é responsável — o banco ou eu?"]O banco tem responsabilidade pela segurança do sistema de pagamentos. Se a adulteração ocorreu em seu computador infectado, a análise é mais complexa — mas o B.O. e a documentação são essenciais para qualquer contestação.[/faq-item]
[faq-item q="Como saber se meu computador tem malware de boleto?"]Instale um antivírus de reputação conhecida (Kaspersky, Avast, Bitdefender) e faça uma varredura completa. Para pagamentos de alto valor, use preferencialmente o celular com app bancário oficial.[/faq-item]
[/faq]

[destaque tipo="dica"]Algumas empresas implementam validação de boleto — você pode checar se o boleto é legítimo pelo portal do banco emitente. O Banco Central também mantém um validador de boletos no site bcb.gov.br.[/destaque]
CONTENT
, $force);

seed_editorial('fraude-em-leilao-online', 'fraude', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Leilões online falsos oferecem produtos impossíveis de verificar antes do pagamento
- Preço muito abaixo do mercado é o principal sinal de alerta
- Leilões legítimos têm CNPJ, site próprio e são registrados em juntas comerciais
- Nunca pague sinal ou valor integral antes de inspecionar o bem pessoalmente
[/resumo]

Um carro seminovo com 30.000 km, primeiro dono, completo, por R$ 35.000. O preço está R$ 20.000 abaixo da tabela FIPE. A foto é impecável. O vendedor diz que é leilão de veículo apreendido por banco, processo rápido, pagamento adiantado para garantir o lance. Parece uma oportunidade única. É uma armadilha clássica.

A fraude em leilão online cresceu com a popularização dos leilões digitais de veículos apreendidos, bens recuperados de devedores e mercadorias retidas pela Receita Federal — categorias que o público sabe existirem, o que confere credibilidade à narrativa do golpista. Sites falsos são construídos com o visual de leiloeiros legítimos, e os anúncios aparecem em buscas orgânicas e até como anúncios pagos.

[destaque tipo="perigo"]Leilões legítimos nunca exigem pagamento do lance antes da verificação do bem. Se há pressão para pagar antecipado, sem visita ao item, é golpe.[/destaque]

<h2>Como identificar um leilão fraudulento</h2>

[checklist titulo="Verificação antes de participar de qualquer leilão" tipo="ok"]
- Pesquise o CNPJ da leiloeira no site da Junta Comercial do estado
- Verifique se o leiloeiro é habilitado — a lista está no site do Conselho Federal de Leiloeiros
- Busque o nome da empresa no Reclame Aqui e em fóruns de consumidores
- Nunca aceite como prova de legitimidade apenas prints de documentos enviados por mensagem
- Exija visita presencial ao bem antes de qualquer pagamento
- Desconfie de sites criados há menos de 6 meses (verifique em who.is)
[/checklist]

[estatistica numero="R$ 28.000" descricao="é o valor médio perdido por vítima em fraudes de leilão online de veículos" fonte="Procon-SP 2023"]

<h2>O processo de um leilão legítimo</h2>

Leilões reais seguem um processo regulamentado: o leiloeiro é habilitado pela Junta Comercial, os bens ficam expostos para visitação prévia, há edital publicado com regras claras e o pagamento ocorre após a arrematação, com documentação completa da transferência. Não há urgência, não há "lance exclusivo disponível por 24 horas", não há pagamento via Pix para pessoa física desconhecida.

[faq titulo="Perguntas frequentes"]
[faq-item q="Como verificar se um leiloeiro é habilitado?"]Acesse o site do Conselho Federal de Leiloeiros (cofl.org.br) ou da Junta Comercial do seu estado. Leiloeiros habilitados têm matrícula registrada e podem ser consultados publicamente.[/faq-item]
[faq-item q="Já paguei o sinal. Consigo recuperar?"]Se o pagamento foi por Pix, acione o banco imediatamente para tentativa de estorno via MED. Registre B.O. e reporte à plataforma onde o anúncio foi veiculado. A recuperação depende de o dinheiro ainda estar na conta do destinatário.[/faq-item]
[faq-item q="Anúncios de leilão no Google são confiáveis?"]Não necessariamente. Criminosos usam Google Ads para promover sites fraudulentos. A presença de anúncio pago não é garantia de legitimidade.[/faq-item]
[/faq]

[destaque tipo="importante"]Para qualquer compra de alto valor encontrada online — carro, imóvel, maquinário — a regra é simples: se não pode ver pessoalmente antes de pagar, não compre. Nenhuma oportunidade real exige que você abra mão dessa verificação.[/destaque]
CONTENT
, $force);

seed_editorial('fraude-portabilidade-credito-consignado', 'fraude', <<<'CONTENT'
[resumo titulo="O que você precisa saber"]
- Portabilidade fraudulenta transfere seu consignado para banco com taxa mais alta
- A diferença entre os valores é embolsada pelos criminosos
- Servidores públicos e aposentados são os principais alvos
- Verifique seus contratos ativos mensalmente no holerite ou no Meu INSS
[/resumo]

Uma operação financeira acontece sem que você perceba. Seu contrato de crédito consignado — aquele empréstimo com desconto direto no salário que você fez anos atrás — foi "portado" para outro banco. As parcelas continuam iguais no holerite, mas o saldo devedor aumentou, o prazo se estendeu e a taxa de juros subiu. O dinheiro extra ficou com os criminosos.

A fraude de portabilidade de crédito consignado é uma das mais lucrativas do crime financeiro brasileiro exatamente porque é silenciosa. A vítima continua pagando, o banco continua debitando — a diferença é que parte desse dinheiro vai para bolsos errados, e o término do contrato recuou meses ou anos no futuro.

[destaque tipo="perigo"]Se você não solicitou portabilidade de consignado, mas há um contrato em andamento em outro banco, você foi vítima de fraude. Conteste imediatamente na Central 135 (INSS) ou no RH da sua empresa.[/destaque]

<h2>Como a fraude é executada</h2>

O processo pode ocorrer de duas formas. Na primeira, um correspondente bancário desonesto acessa seus dados — muitas vezes via sistemas internos — e realiza a portabilidade sem sua autorização, recebendo comissão do banco receptor e retendo parte do troco da operação. Na segunda, um criminoso externo usa seus dados para solicitar a portabilidade digitalmente, aproveitando sistemas com validação insuficiente de identidade.

[estatistica numero="R$ 4,2 bi" descricao="em crédito consignado irregular detectado pelo Ministério da Previdência em 2023" fonte="Ministério da Previdência Social"]

<h2>Como a portabilidade legítima funciona</h2>

A portabilidade de crédito é um direito do consumidor — você pode transferir uma dívida para um banco com taxa menor. O processo legítimo, porém, exige que você mesmo solicite, compare taxas, assine o contrato e receba o detalhamento completo da operação. Nenhuma portabilidade pode ser feita sem sua autorização expressa.

[comparativo titulo="Portabilidade legítima vs. fraudulenta" col1="Legítima" col2="Fraudulenta"]
Você solicita ao banco ou correspondente | Terceiro solicita sem sua participação
Você recebe e assina o contrato | Assinatura falsificada ou inexistente
Taxa menor que o contrato anterior | Taxa mais alta — diferença é lucro do criminoso
Você recebe informativo de CET e prazo | Você não recebe nada — só nota no holerite depois
[/comparativo]

[faq titulo="Perguntas frequentes"]
[faq-item q="Como verificar se há portabilidade não autorizada?"]Para servidores públicos: verifique o holerite e procure o RH. Para aposentados e pensionistas: acesse o Meu INSS ou ligue para a Central 135 e solicite o extrato de empréstimos.[/faq-item]
[faq-item q="Posso cancelar uma portabilidade fraudulenta?"]Sim. Registre B.O., notifique o banco envolvido e a Central 135. O Banco Central tem poder para determinar o cancelamento e a restituição dos valores pagos indevidamente.[/faq-item]
[faq-item q="Correspondente bancário pode fazer portabilidade sem minha presença?"]Não. Toda operação de portabilidade exige documentação e autorização do titular. Se um correspondente realizou a operação sem sua participação, cometeu crime.[/faq-item]
[/faq]

[destaque tipo="dica"]Toda vez que receber o holerite ou extrato de benefício, verifique o desconto de consignado. Se o valor, o banco ou o prazo mudou sem que você tenha solicitado, investigue imediatamente — cada mês de atraso aprofunda o prejuízo.[/destaque]
CONTENT
, $force);

WP_CLI::log('');
WP_CLI::success('Conteúdo editorial inserido com sucesso!');
