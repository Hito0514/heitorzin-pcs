/* ==========================================================
   Heitorzin PCs - JavaScript do front-end
   1) Validação de formulários  2) Máscara de telefone
   3) Confirmação de exclusão   4) Filtro de tabelas
   5) Formulário de pedido (admin)  6) Mensagens flash
   ========================================================== */
(function () {
    'use strict';

    // ---------- 1) Validação de formulários (form[data-validate]) ----------
    function mostrarErro(campo, mensagem) {
        const wrap = campo.closest('.campo') || campo.parentElement;
        wrap.classList.add('invalido');
        let span = wrap.querySelector('.erro-campo');
        if (!span) {
            span = document.createElement('div');
            span.className = 'erro-campo';
            wrap.appendChild(span);
        }
        span.textContent = mensagem;
    }

    function limparErro(campo) {
        const wrap = campo.closest('.campo') || campo.parentElement;
        wrap.classList.remove('invalido');
        const span = wrap.querySelector('.erro-campo');
        if (span) span.remove();
    }

    function validarCampo(campo) {
        const valor = campo.value.trim();
        const rotulo = campo.dataset.rotulo || 'Este campo';

        if (campo.required && valor === '') {
            mostrarErro(campo, rotulo + ' é obrigatório.');
            return false;
        }
        if (valor !== '') {
            if (campo.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(valor)) {
                mostrarErro(campo, 'Informe um e-mail válido.');
                return false;
            }
            if (campo.type === 'tel') {
                const d = valor.replace(/\D/g, '');
                if (d.length < 10 || d.length > 11) {
                    mostrarErro(campo, 'Informe um telefone com DDD.');
                    return false;
                }
            }
            if (campo.type === 'password' && campo.dataset.min && valor.length < Number(campo.dataset.min)) {
                mostrarErro(campo, 'A senha deve ter pelo menos ' + campo.dataset.min + ' caracteres.');
                return false;
            }
            if (campo.dataset.igual) {
                const outro = document.getElementById(campo.dataset.igual);
                if (outro && outro.value !== campo.value) {
                    mostrarErro(campo, 'As senhas não conferem.');
                    return false;
                }
            }
            if (campo.type === 'number') {
                const n = Number(valor.replace(',', '.'));
                if (isNaN(n)) { mostrarErro(campo, 'Informe um número válido.'); return false; }
                if (campo.min !== '' && n < Number(campo.min)) {
                    mostrarErro(campo, 'O valor mínimo é ' + campo.min + '.');
                    return false;
                }
            }
        }
        limparErro(campo);
        return true;
    }

    document.querySelectorAll('form[data-validate]').forEach(function (form) {
        form.setAttribute('novalidate', 'novalidate');
        const campos = form.querySelectorAll('input:not([type=hidden]):not([type=checkbox]):not([type=file]), select, textarea');
        campos.forEach(function (c) {
            c.addEventListener('blur', function () { validarCampo(c); });
            c.addEventListener('input', function () {
                if (c.closest('.campo') && c.closest('.campo').classList.contains('invalido')) validarCampo(c);
            });
        });
        form.addEventListener('submit', function (ev) {
            let ok = true;
            let primeiro = null;
            campos.forEach(function (c) {
                if (!validarCampo(c)) {
                    ok = false;
                    if (!primeiro) primeiro = c;
                }
            });
            if (!ok) {
                ev.preventDefault();
                primeiro.focus();
            }
        });
    });

    // ---------- 2) Máscara de telefone (99) 99999-9999 ----------
    document.querySelectorAll('input[type=tel]').forEach(function (campo) {
        campo.addEventListener('input', function () {
            let d = campo.value.replace(/\D/g, '').slice(0, 11);
            let r = d;
            if (d.length > 10) r = '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
            else if (d.length > 6) r = '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
            else if (d.length > 2) r = '(' + d.slice(0, 2) + ') ' + d.slice(2);
            else if (d.length > 0) r = '(' + d;
            campo.value = r;
        });
    });

    // ---------- 3) Confirmação (form[data-confirm]) ----------
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            if (!window.confirm(form.dataset.confirm)) ev.preventDefault();
        });
    });

    // ---------- 4) Filtro instantâneo de tabelas (input[data-filtro-tabela]) ----------
    document.querySelectorAll('input[data-filtro-tabela]').forEach(function (input) {
        const tabela = document.querySelector(input.dataset.filtroTabela);
        if (!tabela) return;
        input.addEventListener('input', function () {
            const termo = input.value.toLowerCase();
            tabela.querySelectorAll('tbody tr').forEach(function (tr) {
                tr.style.display = tr.textContent.toLowerCase().includes(termo) ? '' : 'none';
            });
        });
    });

    // ---------- 5) Formulário de novo pedido (admin) ----------
    const lista = document.getElementById('itens-pedido');
    if (lista) {
        const modelo = document.getElementById('modelo-linha');
        const totalEl = document.getElementById('total-pedido');
        const moeda = function (v) {
            return 'R$ ' + v.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        function recalcular() {
            let total = 0;
            lista.querySelectorAll('.pedido-linha').forEach(function (linha) {
                const sel = linha.querySelector('select');
                const qtd = linha.querySelector('input[type=number]');
                const opt = sel.options[sel.selectedIndex];
                const preco = opt && opt.dataset.preco ? Number(opt.dataset.preco) : 0;
                const estoque = opt && opt.dataset.estoque ? Number(opt.dataset.estoque) : null;
                if (estoque !== null) qtd.max = estoque;
                const sub = preco * (Number(qtd.value) || 0);
                linha.querySelector('.sub').textContent = moeda(sub);
                total += sub;
            });
            totalEl.textContent = moeda(total);
        }

        function adicionarLinha(produtoId, quantidade) {
            const frag = modelo.content.cloneNode(true);
            const linha = frag.querySelector('.pedido-linha');
            const sel = linha.querySelector('select');
            const qtd = linha.querySelector('input[type=number]');
            if (produtoId) sel.value = produtoId;
            if (quantidade) qtd.value = quantidade;
            sel.addEventListener('change', recalcular);
            qtd.addEventListener('input', recalcular);
            linha.querySelector('.remover-linha').addEventListener('click', function () {
                if (lista.children.length > 1) { linha.remove(); recalcular(); }
            });
            lista.appendChild(frag);
            recalcular();
        }

        document.getElementById('add-linha').addEventListener('click', function () { adicionarLinha(); });

        let antigos = [];
        try { antigos = JSON.parse(lista.dataset.antigos || '[]'); } catch (e) { antigos = []; }
        if (antigos.length) antigos.forEach(function (a) { adicionarLinha(a.produto_id, a.quantidade); });
        else adicionarLinha();

        document.getElementById('form-pedido').addEventListener('submit', function (ev) {
            const algum = Array.from(lista.querySelectorAll('select')).some(function (s) { return s.value !== ''; });
            if (!algum) {
                ev.preventDefault();
                window.alert('Selecione pelo menos um produto para o pedido.');
            }
        });
    }

    // ---------- 6) Mensagens flash: fechar e sumir sozinhas ----------
    document.querySelectorAll('.flash').forEach(function (f) {
        const b = f.querySelector('button');
        if (b) b.addEventListener('click', function () { f.remove(); });
        if (f.classList.contains('flash-sucesso')) setTimeout(function () { f.remove(); }, 6000);
    });
})();
