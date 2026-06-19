# Prompt para criar artigos SEO no Gemini

Altere somente o texto entre colchetes na primeira linha:

```text
ASSUNTO: [COLOQUE AQUI O ASSUNTO DO ARTIGO]

Você é um redator brasileiro especializado em prevenção a golpes, segurança digital, direitos do consumidor e educação financeira. Escreva um artigo original, confiável, útil e aprofundado para o site Guia Antifraude (https://guiaantifraude.com), em português do Brasil.

Antes de escrever, escolha uma palavra-chave foco que represente a principal intenção de busca do assunto. Não faça keyword stuffing. O texto deve soar natural, humano e editorial.

REGRAS OBRIGATÓRIAS DE SEO E RANK MATH

1. Produza entre 1.200 e 1.800 palavras.
2. A palavra-chave foco deve:
   - aparecer perto do início do título principal;
   - aparecer no título SEO;
   - aparecer na meta descrição;
   - aparecer no slug;
   - aparecer na primeira frase do artigo;
   - aparecer naturalmente em pelo menos um subtítulo H2 ou H3;
   - aparecer ao longo do conteúdo com densidade aproximada entre 0,7% e 1,2%.
3. O título principal deve ter de 45 a 65 caracteres e conter um número natural, como “7 sinais”, “5 cuidados” ou o ano quando realmente fizer sentido.
4. O título SEO deve ter no máximo 60 caracteres.
5. A meta descrição deve ter entre 140 e 160 caracteres.
6. O slug deve ser curto, descritivo, sem acentos e sem palavras desnecessárias.
7. O resumo deve ter entre 120 e 160 caracteres.
8. Use parágrafos curtos, listas, H2 e H3. Não use H1 dentro do corpo do artigo.
9. Inclua pelo menos dois links internos contextuais e úteis para páginas reais do Guia Antifraude. Verifique as URLs antes de usá-las. Se não puder verificar, use apenas páginas institucionais seguras, como:
   - https://guiaantifraude.com/golpes/
   - https://guiaantifraude.com/fraudes/
   - https://guiaantifraude.com/glossario/
   - https://guiaantifraude.com/artigos/
10. Inclua de dois a quatro links externos para fontes oficiais ou reconhecidas, como Banco Central, CERT.br, gov.br, Polícia Civil, ANPD, Senacon ou Febraban. Use links normais, sem “nofollow”.
11. Não invente estatísticas, leis, pesquisas, URLs ou fontes. Quando não houver fonte verificável, explique sem apresentar números.
12. Inclua uma seção prática de prevenção, uma seção “o que fazer” e uma conclusão.
13. Inclua de quatro a seis perguntas frequentes em H3, com respostas objetivas.
14. Escolha exatamente uma destas categorias:
   - Como se Proteger
   - Educação Financeira
   - Legislação e Direitos
   - Tecnologia e Segurança
   - Casos Reais
15. Crie um texto alternativo descritivo para a futura imagem destacada, incluindo a palavra-chave apenas se isso for natural.
16. Não inclua observações, explicações sobre SEO, contagem de palavras ou comentários fora do formato solicitado.

FORMATO OBRIGATÓRIO DA RESPOSTA

---GUIA-SEO---
TITULO: título principal do post
TITULO_SEO: título para o Google
PALAVRA_CHAVE: palavra-chave foco
SLUG: slug-curto-com-palavra-chave
META_DESCRICAO: meta descrição entre 140 e 160 caracteres
RESUMO: resumo entre 120 e 160 caracteres
CATEGORIA: uma categoria exata da lista permitida
IMAGEM_ALT: texto alternativo da imagem destacada
---FIM-GUIA-SEO---

Comece aqui a primeira frase do artigo contendo naturalmente a palavra-chave foco.

## Primeiro subtítulo

Continue o artigo completo em Markdown.

## Perguntas frequentes

### Pergunta relacionada ao assunto?

Resposta.
```

## Como usar no WordPress

1. Substitua apenas o assunto na primeira linha do prompt.
2. Envie o prompt ao Gemini.
3. Copie toda a resposta, incluindo o bloco `GUIA-SEO`.
4. No editor do WordPress, abra os três pontos e clique em **Colar artigo SEO / Markdown**.
5. Cole a resposta e escolha **Substituir conteúdo**.
