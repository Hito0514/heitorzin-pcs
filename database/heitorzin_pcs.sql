-- =====================================================================
--  HEITORZIN PCs - Script completo do banco de dados (MySQL / MariaDB)
--  Autor: Heitor Almeida Souza
--  Como usar: importe este arquivo inteiro no phpMyAdmin (aba "Importar")
--  ou execute:  mysql -u root -p < heitorzin_pcs.sql
-- =====================================================================

DROP DATABASE IF EXISTS heitorzin_pcs;
CREATE DATABASE heitorzin_pcs
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE heitorzin_pcs;

-- ---------------------------------------------------------------------
-- 1) USUARIOS  (quem acessa a área administrativa)
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(120) NOT NULL,
    senha       VARCHAR(255) NOT NULL,              -- password_hash()
    perfil      ENUM('admin','gerente') NOT NULL DEFAULT 'gerente',
    ativo       TINYINT(1) NOT NULL DEFAULT 1,
    criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2) CLIENTES  (quem compra na loja pública)
-- ---------------------------------------------------------------------
CREATE TABLE clientes (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(100) NOT NULL,
    email       VARCHAR(120) NOT NULL,
    telefone    VARCHAR(20)  NOT NULL,
    senha       VARCHAR(255) NOT NULL,              -- password_hash()
    endereco    VARCHAR(200) NULL,
    cidade      VARCHAR(80)  NULL,
    criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_clientes_email (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3) CATEGORIAS
-- ---------------------------------------------------------------------
CREATE TABLE categorias (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(60)  NOT NULL,
    descricao   VARCHAR(200) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categorias_nome (nome)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4) FORNECEDORES
-- ---------------------------------------------------------------------
CREATE TABLE fornecedores (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(100) NOT NULL,
    cnpj        VARCHAR(18)  NULL,
    email       VARCHAR(120) NULL,
    telefone    VARCHAR(20)  NULL,
    cidade      VARCHAR(80)  NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5) PRODUTOS
-- ---------------------------------------------------------------------
CREATE TABLE produtos (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome            VARCHAR(150) NOT NULL,
    marca           VARCHAR(60)  NULL,
    descricao       TEXT NULL,
    preco           DECIMAL(10,2) NOT NULL,
    estoque         INT NOT NULL DEFAULT 0,
    imagem          VARCHAR(255) NULL,              -- nome do arquivo em public/uploads
    destaque        TINYINT(1) NOT NULL DEFAULT 0,
    ativo           TINYINT(1) NOT NULL DEFAULT 1,
    categoria_id    INT UNSIGNED NOT NULL,
    fornecedor_id   INT UNSIGNED NULL,
    criado_em       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_produtos_categoria
        FOREIGN KEY (categoria_id)  REFERENCES categorias(id)   ON DELETE RESTRICT,
    CONSTRAINT fk_produtos_fornecedor
        FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE SET NULL,
    CONSTRAINT ck_produtos_preco   CHECK (preco >= 0),
    CONSTRAINT ck_produtos_estoque CHECK (estoque >= 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6) PEDIDOS
-- ---------------------------------------------------------------------
CREATE TABLE pedidos (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    cliente_id  INT UNSIGNED NOT NULL,
    status      ENUM('pendente','pago','enviado','entregue','cancelado') NOT NULL DEFAULT 'pendente',
    total       DECIMAL(10,2) NOT NULL DEFAULT 0,
    observacao  VARCHAR(255) NULL,
    criado_em   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_pedidos_cliente
        FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 7) ITENS_PEDIDO  (tabela associativa Pedido x Produto)
-- ---------------------------------------------------------------------
CREATE TABLE itens_pedido (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    pedido_id       INT UNSIGNED NOT NULL,
    produto_id      INT UNSIGNED NOT NULL,
    quantidade      INT NOT NULL,
    preco_unitario  DECIMAL(10,2) NOT NULL,         -- preço no momento da compra
    PRIMARY KEY (id),
    CONSTRAINT fk_itens_pedido
        FOREIGN KEY (pedido_id)  REFERENCES pedidos(id)  ON DELETE CASCADE,
    CONSTRAINT fk_itens_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE RESTRICT,
    CONSTRAINT ck_itens_qtd CHECK (quantidade > 0)
) ENGINE=InnoDB;

-- =====================================================================
--  DADOS INICIAIS PARA TESTE
-- =====================================================================

-- Senhas (geradas com password_hash):
--   admin@heitorzinpcs.com  -> admin123
--   gerente@heitorzinpcs.com -> admin123
--   cliente@teste.com       -> cliente123
INSERT INTO usuarios (nome, email, senha, perfil) VALUES
('Heitor Almeida Souza', 'admin@heitorzinpcs.com',   '$2y$10$kGcBd7E8xMhlK/doXkh1ReZAEjiwlY42pvIV6RFI.CmdNBoeHwUbW', 'admin'),
('Gerente da Loja',      'gerente@heitorzinpcs.com', '$2y$10$kGcBd7E8xMhlK/doXkh1ReZAEjiwlY42pvIV6RFI.CmdNBoeHwUbW', 'gerente');

INSERT INTO clientes (nome, email, telefone, senha, endereco, cidade) VALUES
('Cliente Teste',  'cliente@teste.com', '(38) 99999-0001', '$2y$10$edgZbrkDhUC.IXuGxtpN1.OycYYPw2XsuDB8RhcMyjXIEx1BjVtk6', 'Rua das Palmeiras, 100', 'Montes Claros'),
('Maria Oliveira', 'maria@teste.com',   '(31) 98888-0002', '$2y$10$edgZbrkDhUC.IXuGxtpN1.OycYYPw2XsuDB8RhcMyjXIEx1BjVtk6', 'Av. Brasil, 250',         'Belo Horizonte'),
('João Pereira',   'joao@teste.com',    '(38) 97777-0003', '$2y$10$edgZbrkDhUC.IXuGxtpN1.OycYYPw2XsuDB8RhcMyjXIEx1BjVtk6', 'Rua Minas Gerais, 45',    'Montes Claros');

INSERT INTO categorias (nome, descricao) VALUES
('Processadores',   'CPUs Intel e AMD para todos os perfis'),
('Placas de Vídeo', 'GPUs para jogos, edição e IA'),
('Placas-mãe',      'Motherboards para AM4, AM5 e LGA'),
('Memória RAM',     'Módulos DDR4 e DDR5'),
('Armazenamento',   'SSDs NVMe, SATA e HDs'),
('Fontes',          'Fontes de alimentação certificadas 80 Plus'),
('Gabinetes',       'Gabinetes com boa ventilação e design'),
('Periféricos',     'Teclados, mouses, headsets e monitores');

INSERT INTO fornecedores (nome, cnpj, email, telefone, cidade) VALUES
('TechDistribuidora LTDA', '12.345.678/0001-90', 'vendas@techdistribuidora.com', '(11) 4000-1000', 'São Paulo'),
('Minas Hardware Atacado', '98.765.432/0001-10', 'contato@minashardware.com',    '(31) 3000-2000', 'Belo Horizonte'),
('GamerParts Brasil',      '45.678.912/0001-33', 'comercial@gamerparts.com.br',  '(41) 3500-3000', 'Curitiba');

INSERT INTO produtos (nome, marca, descricao, preco, estoque, destaque, categoria_id, fornecedor_id) VALUES
('Ryzen 5 5600 3.5GHz AM4',         'AMD',      '6 núcleos e 12 threads, excelente custo-benefício para jogos em Full HD.', 749.90,  15, 1, 1, 1),
('Ryzen 7 5700X3D 3.0GHz AM4',      'AMD',      '8 núcleos com tecnologia 3D V-Cache, um dos melhores para jogos no AM4.',  1599.90, 8,  1, 1, 1),
('Core i5-12400F 2.5GHz LGA1700',   'Intel',    '6 núcleos de alto desempenho. Não possui vídeo integrado.',                 899.90,  12, 0, 1, 2),
('RTX 4060 8GB GDDR6',              'NVIDIA',   'Placa de vídeo com DLSS 3 e Ray Tracing para Full HD e 1440p.',             1999.90, 6,  1, 2, 3),
('RX 7600 8GB GDDR6',               'AMD',      'GPU eficiente para jogar em 1080p com altas taxas de quadros.',             1599.90, 7,  0, 2, 3),
('RTX 4070 Super 12GB GDDR6X',      'NVIDIA',   'Desempenho top para 1440p e 4K com DLSS 3.',                                4699.90, 3,  1, 2, 3),
('Placa-mãe B550M Aorus Elite',     'Gigabyte', 'Soquete AM4, DDR4, M.2 NVMe e PCIe 4.0.',                                   689.90,  10, 0, 3, 1),
('Placa-mãe B650M AM5 DDR5',        'ASRock',   'Soquete AM5, DDR5, PCIe 5.0 e Wi-Fi.',                                      999.90,  5,  0, 3, 2),
('Memória Kingston Fury 16GB DDR4 3200MHz', 'Kingston', 'Kit de 16GB (2x8GB) com perfil XMP.',                              289.90,  30, 1, 4, 1),
('Memória Corsair Vengeance 32GB DDR5 6000MHz', 'Corsair', 'Kit 2x16GB DDR5 de alta frequência.',                           799.90,  9,  0, 4, 2),
('SSD NVMe Kingston NV2 1TB',       'Kingston', 'Leitura de até 3500MB/s. Ideal para sistema e jogos.',                      329.90,  25, 1, 5, 1),
('SSD SATA WD Green 480GB',         'Western Digital', 'Upgrade simples e barato para PCs antigos.',                         179.90,  40, 0, 5, 2),
('Fonte Corsair CV650 650W 80 Plus Bronze', 'Corsair', 'Fonte confiável para PCs de entrada e intermediários.',              399.90,  14, 0, 6, 1),
('Gabinete Gamer NZXT H5 Flow Preto', 'NZXT',   'Excelente fluxo de ar e acabamento premium em preto.',                      599.90,  6,  1, 7, 3),
('Gabinete Cooler Master MasterBox Branco', 'Cooler Master', 'Lateral em vidro temperado, visual limpo em branco.',          379.90,  8,  0, 7, 3),
('Teclado Mecânico Redragon Kumara', 'Redragon', 'Switch outemu blue, ABNT2, compacto TKL.',                                 189.90,  20, 0, 8, 2),
('Mouse Gamer Logitech G203',       'Logitech', 'Sensor de 8000 DPI e iluminação RGB personalizável.',                       119.90,  35, 0, 8, 1),
('Headset HyperX Cloud Stinger',    'HyperX',   'Confortável, leve e com microfone ajustável.',                              229.90,  18, 0, 8, 3);

-- Pedidos de exemplo (para alimentar o dashboard e o relatório de vendas)
INSERT INTO pedidos (cliente_id, status, total, observacao, criado_em) VALUES
(1, 'entregue',  1329.70, 'Entregar em horário comercial.', '2026-10-20 14:30:00'),
(2, 'pago',      2289.80, NULL,                              '2026-11-02 09:15:00'),
(3, 'pendente',  329.90,  NULL,                              '2026-11-10 18:40:00'),
(1, 'cancelado', 599.90,  'Cliente desistiu.',               '2026-11-12 11:05:00');

INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario) VALUES
(1, 9,  2, 289.90),
(1, 1,  1, 749.90) ,
(2, 4,  1, 1999.90),
(2, 9,  1, 289.90),
(3, 11, 1, 329.90),
(4, 14, 1, 599.90);
