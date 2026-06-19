(function (wp) {
  'use strict';

  if (!wp || !wp.plugins || !wp.editPost || !wp.element || !wp.components || !wp.data || !wp.blocks || !wp.apiFetch) {
    return;
  }

  var el = wp.element.createElement;
  var Fragment = wp.element.Fragment;
  var useState = wp.element.useState;
  var registerPlugin = wp.plugins.registerPlugin;
  var PluginMoreMenuItem = wp.editPost.PluginMoreMenuItem;
  var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
  var Modal = wp.components.Modal;
  var TextareaControl = wp.components.TextareaControl;
  var Button = wp.components.Button;
  var Notice = wp.components.Notice;
  var Spinner = wp.components.Spinner;

  var seoFieldMap = {
    TITULO: 'title',
    TITULO_SEO: 'seoTitle',
    PALAVRA_CHAVE: 'focusKeyword',
    SLUG: 'slug',
    META_DESCRICAO: 'metaDescription',
    RESUMO: 'excerpt',
    CATEGORIA: 'category',
    IMAGEM_ALT: 'imageAlt',
  };

  function parseSeoPackage(value) {
    var match = value.match(/---GUIA-SEO---\s*([\s\S]*?)\s*---FIM-GUIA-SEO---\s*([\s\S]*)$/i);
    if (!match) {
      return null;
    }

    var metadata = {};
    match[1].split('\n').forEach(function (line) {
      var field = line.match(/^\s*([A-Z_]+)\s*:\s*(.+?)\s*$/);
      if (field && seoFieldMap[field[1]]) {
        metadata[seoFieldMap[field[1]]] = field[2].trim();
      }
    });

    return {
      metadata: metadata,
      body: match[2].trim().replace(/^#\s+.+(?:\n+|$)/, ''),
    };
  }

  function plainText(value) {
    return value
      .replace(/```[\s\S]*?```/g, ' ')
      .replace(/!\[[^\]]*\]\([^)]+\)/g, ' ')
      .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
      .replace(/[#>*_~`|-]/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();
  }

  function normalize(value) {
    return (value || '')
      .toLocaleLowerCase('pt-BR')
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');
  }

  function analyzeSeoPackage(seoPackage) {
    if (!seoPackage) {
      return [];
    }

    var meta = seoPackage.metadata;
    var bodyText = plainText(seoPackage.body);
    var keyword = normalize(meta.focusKeyword);
    var normalizedBody = normalize(bodyText);
    var words = bodyText ? bodyText.split(/\s+/).length : 0;
    var occurrences = keyword ? normalizedBody.split(keyword).length - 1 : 0;
    var density = words && keyword ? (occurrences / words) * 100 : 0;
    var headingText = normalize(
      seoPackage.body
        .split('\n')
        .filter(function (line) {
          return /^#{2,3}\s+/.test(line);
        })
        .join(' ')
    );
    var warnings = [];

    ['title', 'seoTitle', 'focusKeyword', 'slug', 'metaDescription', 'excerpt', 'category', 'imageAlt'].forEach(function (field) {
      if (!meta[field]) {
        warnings.push('Campo obrigatório ausente: ' + field + '.');
      }
    });

    if (meta.title && keyword && normalize(meta.title).indexOf(keyword) === -1) {
      warnings.push('A palavra-chave não aparece no título.');
    }
    if (meta.title && !/\d/.test(meta.title)) {
      warnings.push('O título não contém um número.');
    }
    if (meta.seoTitle && keyword && normalize(meta.seoTitle).indexOf(keyword) === -1) {
      warnings.push('A palavra-chave não aparece no título SEO.');
    }
    if (meta.metaDescription && keyword && normalize(meta.metaDescription).indexOf(keyword) === -1) {
      warnings.push('A palavra-chave não aparece na meta descrição.');
    }
    if (meta.slug && keyword && normalize(meta.slug).replace(/-/g, ' ').indexOf(keyword) === -1) {
      warnings.push('A palavra-chave não aparece no slug.');
    }
    if (keyword && normalizedBody.slice(0, 250).indexOf(keyword) === -1) {
      warnings.push('A palavra-chave não aparece no início do conteúdo.');
    }
    if (keyword && headingText.indexOf(keyword) === -1) {
      warnings.push('A palavra-chave não aparece em um H2 ou H3.');
    }
    if (words < 600) {
      warnings.push('O artigo possui apenas ' + words + ' palavras.');
    }
    if (density > 0 && (density < 0.5 || density > 1.5)) {
      warnings.push('Densidade estimada da palavra-chave: ' + density.toFixed(1) + '%.');
    }
    if ((seoPackage.body.match(/https?:\/\/guiaantifraude\.com\//gi) || []).length < 2) {
      warnings.push('Inclua pelo menos dois links internos do Guia Antifraude.');
    }
    var allLinks = seoPackage.body.match(/https?:\/\/[^)\s]+/gi) || [];
    var externalLinks = allLinks.filter(function (url) {
      return url.indexOf('guiaantifraude.com') === -1;
    });
    if (externalLinks.length < 2) {
      warnings.push('Inclua pelo menos dois links para fontes externas.');
    }

    return warnings;
  }

  function escapeHtml(value) {
    return value
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }

  function inlineMarkdown(value) {
    var placeholders = [];
    var text = escapeHtml(value);

    text = text.replace(/`([^`\n]+)`/g, function (_, code) {
      var token = '@@FS_CODE_' + placeholders.length + '@@';
      placeholders.push('<code>' + code + '</code>');
      return token;
    });

    text = text.replace(/!\[([^\]]*)\]\(((?:https?:\/\/|\/)[^)\s]+)(?:\s+&quot;([^&]*)&quot;)?\)/g, function (_, alt, url, title) {
      return '<img src="' + url + '" alt="' + alt + '"' + (title ? ' title="' + title + '"' : '') + '>';
    });
    text = text.replace(/\[([^\]]+)\]\(((?:https?:\/\/|\/)[^)\s]+)(?:\s+&quot;([^&]*)&quot;)?\)/g, function (_, label, url, title) {
      return '<a href="' + url + '"' + (title ? ' title="' + title + '"' : '') + '>' + label + '</a>';
    });
    text = text.replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/__([^_\n]+)__/g, '<strong>$1</strong>');
    text = text.replace(/~~([^~\n]+)~~/g, '<s>$1</s>');
    text = text.replace(/(^|[\s(])\*([^*\n]+)\*(?=$|[\s).,!?:;])/g, '$1<em>$2</em>');
    text = text.replace(/(^|[\s(])_([^_\n]+)_(?=$|[\s).,!?:;])/g, '$1<em>$2</em>');

    placeholders.forEach(function (html, index) {
      text = text.replace('@@FS_CODE_' + index + '@@', html);
    });

    return text;
  }

  function markdownToHtml(markdown) {
    var lines = markdown.replace(/\r\n?/g, '\n').split('\n');
    var html = [];
    var paragraph = [];
    var listType = null;
    var quote = [];
    var code = [];
    var inCode = false;
    var codeLanguage = '';

    function flushParagraph() {
      if (paragraph.length) {
        html.push('<p>' + inlineMarkdown(paragraph.join(' ')) + '</p>');
        paragraph = [];
      }
    }

    function closeList() {
      if (listType) {
        html.push('</' + listType + '>');
        listType = null;
      }
    }

    function flushQuote() {
      if (quote.length) {
        html.push('<blockquote><p>' + inlineMarkdown(quote.join(' ')) + '</p></blockquote>');
        quote = [];
      }
    }

    function flushCode() {
      var className = codeLanguage ? ' class="language-' + codeLanguage.replace(/[^a-z0-9_-]/gi, '') + '"' : '';
      html.push('<pre><code' + className + '>' + escapeHtml(code.join('\n')) + '</code></pre>');
      code = [];
      codeLanguage = '';
    }

    lines.forEach(function (line) {
      var match;

      if (inCode) {
        if (/^```/.test(line)) {
          inCode = false;
          flushCode();
        } else {
          code.push(line);
        }
        return;
      }

      match = line.match(/^```\s*([a-z0-9_-]*)\s*$/i);
      if (match) {
        flushParagraph();
        closeList();
        flushQuote();
        inCode = true;
        codeLanguage = match[1] || '';
        return;
      }

      if (!line.trim()) {
        flushParagraph();
        closeList();
        flushQuote();
        return;
      }

      match = line.match(/^(#{1,6})\s+(.+)$/);
      if (match) {
        flushParagraph();
        closeList();
        flushQuote();
        var level = match[1].length;
        html.push('<h' + level + '>' + inlineMarkdown(match[2]) + '</h' + level + '>');
        return;
      }

      if (/^ {0,3}([-*_])(?:\s*\1){2,}\s*$/.test(line)) {
        flushParagraph();
        closeList();
        flushQuote();
        html.push('<hr>');
        return;
      }

      match = line.match(/^\s*>\s?(.*)$/);
      if (match) {
        flushParagraph();
        closeList();
        quote.push(match[1]);
        return;
      }

      match = line.match(/^\s*[-*+]\s+(.+)$/);
      if (match) {
        flushParagraph();
        flushQuote();
        if (listType !== 'ul') {
          closeList();
          listType = 'ul';
          html.push('<ul>');
        }
        html.push('<li>' + inlineMarkdown(match[1]) + '</li>');
        return;
      }

      match = line.match(/^\s*\d+[.)]\s+(.+)$/);
      if (match) {
        flushParagraph();
        flushQuote();
        if (listType !== 'ol') {
          closeList();
          listType = 'ol';
          html.push('<ol>');
        }
        html.push('<li>' + inlineMarkdown(match[1]) + '</li>');
        return;
      }

      closeList();
      flushQuote();
      paragraph.push(line.trim());
    });

    if (inCode) {
      flushCode();
    }
    flushParagraph();
    closeList();
    flushQuote();

    return html.join('\n');
  }

  function MarkdownImporter() {
    var state = useState(false);
    var isOpen = state[0];
    var setOpen = state[1];
    var markdownState = useState('');
    var markdown = markdownState[0];
    var setMarkdown = markdownState[1];
    var errorState = useState('');
    var error = errorState[0];
    var setError = errorState[1];
    var importingState = useState(false);
    var isImporting = importingState[0];
    var setImporting = importingState[1];
    var seoPackage = parseSeoPackage(markdown);
    var seoWarnings = analyzeSeoPackage(seoPackage);

    function importMarkdown(replace) {
      if (!markdown.trim()) {
        setError('Cole algum conteúdo em Markdown antes de importar.');
        return;
      }

      try {
        var source = seoPackage ? seoPackage.body : markdown;
        var blocks = wp.blocks.rawHandler({ HTML: markdownToHtml(source) });
        var dispatch = wp.data.dispatch('core/block-editor');

        if (!blocks.length) {
          setError('Não foi possível converter o conteúdo.');
          return;
        }

        if (!seoPackage) {
          if (replace) {
            dispatch.resetBlocks(blocks);
          } else {
            dispatch.insertBlocks(blocks);
          }

          setMarkdown('');
          setError('');
          setOpen(false);
          return;
        }

        var editorSelect = wp.data.select('core/editor');
        var blockSelect = wp.data.select('core/block-editor');
        var currentBlocks = blockSelect.getBlocks();
        var finalBlocks = replace ? blocks : currentBlocks.concat(blocks);
        var meta = seoPackage.metadata;
        var postId = editorSelect.getCurrentPostId();

        setImporting(true);
        setError('');

        wp.apiFetch({
          path: '/guia-antifraude/v1/importar-seo',
          method: 'POST',
          data: {
            post_id: postId,
            title: meta.title || '',
            seo_title: meta.seoTitle || '',
            focus_keyword: meta.focusKeyword || '',
            slug: meta.slug || '',
            meta_description: meta.metaDescription || '',
            excerpt: meta.excerpt || '',
            category: meta.category || '',
            image_alt: meta.imageAlt || '',
            content: wp.blocks.serialize(finalBlocks),
          },
        })
          .then(function () {
            window.sessionStorage.setItem(
              'fsMarkdownImportNotice',
              'Artigo, categoria e campos do Rank Math importados com sucesso.'
            );
            window.location.reload();
          })
          .catch(function (apiError) {
            setImporting(false);
            setError(apiError && apiError.message ? apiError.message : 'Não foi possível salvar os campos SEO.');
          });
      } catch (conversionError) {
        setImporting(false);
        setError('Erro ao converter o Markdown. Verifique a formatação e tente novamente.');
      }
    }

    if (window.sessionStorage.getItem('fsMarkdownImportNotice')) {
      var notice = window.sessionStorage.getItem('fsMarkdownImportNotice');
      window.sessionStorage.removeItem('fsMarkdownImportNotice');
      window.setTimeout(function () {
        wp.data.dispatch('core/notices').createSuccessNotice(notice, {
          type: 'snackbar',
        });
      }, 500);
    }

    return el(
      Fragment,
      null,
      el(
        PluginMoreMenuItem,
        {
          icon: 'editor-code',
          onClick: function () {
            setError('');
            setOpen(true);
          },
        },
        'Colar artigo SEO / Markdown'
      ),
      PluginDocumentSettingPanel &&
        el(
          PluginDocumentSettingPanel,
          {
            name: 'fs-importar-artigo-seo',
            title: 'Importar artigo SEO',
            className: 'fs-importar-artigo-seo-panel',
            initialOpen: true,
          },
          el(
            'p',
            null,
            'Cole a resposta estruturada do Gemini para preencher conteúdo, categoria e campos do Rank Math.'
          ),
          el(
            Button,
            {
              variant: 'primary',
              style: { width: '100%', justifyContent: 'center' },
              onClick: function () {
                setError('');
                setOpen(true);
              },
            },
            'Colar artigo SEO / Markdown'
          )
        ),
      isOpen &&
        el(
          Modal,
          {
            title: 'Colar artigo SEO / Markdown',
            className: 'fs-markdown-importer',
            onRequestClose: function () {
              setOpen(false);
            },
          },
          error &&
            el(
              Notice,
              {
                status: 'error',
                isDismissible: false,
              },
              error
            ),
          seoPackage &&
            el(
              Notice,
              {
                status: seoWarnings.length ? 'warning' : 'success',
                isDismissible: false,
              },
              seoWarnings.length
                ? el(
                    Fragment,
                    null,
                    el('strong', null, 'Pacote SEO identificado com ' + seoWarnings.length + ' aviso(s):'),
                    el(
                      'ul',
                      null,
                      seoWarnings.map(function (warning, index) {
                        return el('li', { key: index }, warning);
                      })
                    )
                  )
                : 'Pacote SEO completo identificado. Os campos do Rank Math serão preenchidos automaticamente.'
            ),
          el(TextareaControl, {
            label: 'Artigo SEO estruturado ou Markdown',
            help: seoPackage
              ? 'O bloco GUIA-SEO foi identificado. A importação salvará o artigo e recarregará o editor.'
              : 'Cole um pacote GUIA-SEO para preencher todos os campos, ou Markdown comum para converter apenas o conteúdo.',
            value: markdown,
            rows: 16,
            onChange: setMarkdown,
            disabled: isImporting,
          }),
          el(
            'div',
            {
              style: {
                display: 'flex',
                justifyContent: 'flex-end',
                gap: '8px',
                marginTop: '16px',
              },
            },
            el(
              Button,
              {
                variant: 'secondary',
                disabled: isImporting,
                onClick: function () {
                  importMarkdown(false);
                },
              },
              'Inserir no final'
            ),
            el(
              Button,
              {
                variant: 'primary',
                disabled: isImporting,
                onClick: function () {
                  if (window.confirm('Substituir todo o conteúdo atual pelos blocos convertidos?')) {
                    importMarkdown(true);
                  }
                },
              },
              isImporting ? el(Fragment, null, el(Spinner), ' Importando...') : 'Substituir conteúdo'
            )
          )
        )
    );
  }

  registerPlugin('fs-markdown-importer', {
    render: MarkdownImporter,
  });
})(window.wp);
