# Passo a passo: preparar e gravar a apresentação (vídeo de tela)

## A) Antes de gravar (preparação – 10 min)

1. Abra o **XAMPP Control Panel** → **Start** em **Apache** e **MySQL** (ficam verdes).
2. Copie a pasta `heitorzin-pcs` para `C:\xampp\htdocs\`.
3. Abra `http://localhost/phpmyadmin` → **Importar** → selecione `database/heitorzin_pcs.sql` → **Importar**.
4. Abra `http://localhost/heitorzin-pcs/public/` e confirme que a loja aparece.
5. Prepare uma imagem qualquer (JPG/PNG) na área de trabalho para mostrar o upload.
6. Deixe abertos: o navegador, o phpMyAdmin (aba do banco `heitorzin_pcs`) e o VS Code com o projeto.
7. Use uma janela anônima para o cliente e outra janela normal para o admin (assim você alterna sem deslogar).
8. Para gravar: **Windows + G** (Xbox Game Bar → Capturar) ou **OBS Studio**. Ative o microfone.

## B) Roteiro da gravação (~8 a 10 minutos)

| Tempo | O que mostrar | O que falar (resumo) |
|---|---|---|
| 0:00 | Slide/tela inicial ou a home | "Sou Heitor Almeida Souza, projeto Heitorzin PCs, loja de peças de computador." |
| 0:30 | VS Code: pastas `app/models`, `controllers`, `views`, `core` | "Arquitetura MVC, POO e PDO. Controllers chamam Models; Views só exibem." |
| 1:30 | `Database.php` (PDO) e `Auth.php` (`password_hash`) | "Conexão PDO com prepared statements e senhas com hash." |
| 2:15 | phpMyAdmin: tabelas e relacionamentos (aba **Designer**) | "7 tabelas, chaves primárias e estrangeiras." |
| 3:00 | **Home pública** (`/public/`) | Destaques, categorias, layout preto e branco listrado. |
| 3:30 | **Catálogo**: buscar "ryzen", filtrar categoria, ordenar por preço | Funcionalidades extras: busca e filtro. |
| 4:15 | **Detalhes do produto** → adicionar ao carrinho | Página de detalhes e carrinho. |
| 4:45 | Tentar finalizar sem login → **Criar conta** (cadastro novo, mostrar validação JS com campo vazio/senha curta) | Registro de cliente e validação JavaScript. |
| 5:30 | **Finalizar pedido** → "Meus pedidos" | Pedido gerado, estoque baixou. |
| 6:00 | Janela admin: login `admin@heitorzinpcs.com` / `admin123` → **Dashboard** | Dashboard, estoque baixo, receita por categoria. |
| 6:30 | **Produtos**: criar um produto com **upload de imagem**, editar, excluir | CRUD completo + upload. |
| 7:30 | Mostrar rapidamente **Categorias, Fornecedores, Clientes, Usuários** (criar/editar/excluir um) | 6 CRUDs ao todo. |
| 8:15 | **Pedidos**: ver o pedido do cliente, mudar status para "cancelado" e mostrar o estoque voltando | Controle de estoque automático. |
| 8:45 | **Relatórios**: filtro de datas e **Exportar CSV** | Relatório de vendas. |
| 9:15 | Tentar excluir uma categoria com produtos (mensagem de bloqueio) e fazer **logout**; tentar abrir `/admin` (volta ao login) | Segurança e integridade. |
| 9:45 | GitHub: repositório público + README | "Código no GitHub, README com instruções." |

## C) Publicar no GitHub (entrega)

```bash
cd heitorzin-pcs
git init
git add .
git commit -m "Projeto Heitorzin PCs - entrega final"
git branch -M main
git remote add origin https://github.com/SEU-USUARIO/heitorzin-pcs.git
git push -u origin main
```
1. Crie antes o repositório **público** vazio em github.com → **New repository** (nome `heitorzin-pcs`, sem README).
2. Depois do push, edite o `README.md` colocando o link do repositório na tabela do topo.
3. Envie o link ao professor.

## D) Problemas comuns
- **Erro de conexão com o banco:** MySQL desligado no XAMPP, ou o `.sql` não foi importado.
- **Página sem estilo:** acesse por `http://localhost/heitorzin-pcs/public/` (com `/public/`).
- **Porta 80 ocupada:** no XAMPP, mude a porta do Apache e acesse `localhost:PORTA/...`.
- **Upload não funciona:** confirme que a pasta `public/uploads` existe e o arquivo tem até 2 MB.
