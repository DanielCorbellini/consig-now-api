-- =============================================
-- DADOS INICIAIS PARA TESTES - ConsigNowAPI
-- =============================================
-- Execute este script após as migrations

-- Limpar dados existentes (cuidado em produção!)
-- TRUNCATE TABLE venda_itens CASCADE;
-- TRUNCATE TABLE vendas CASCADE;
-- TRUNCATE TABLE movimentacoes_estoque CASCADE;
-- TRUNCATE TABLE condicional_itens CASCADE;
-- TRUNCATE TABLE condicionais CASCADE;
-- TRUNCATE TABLE estoques CASCADE;
-- TRUNCATE TABLE almoxarifados CASCADE;
-- TRUNCATE TABLE produtos CASCADE;
-- TRUNCATE TABLE categorias_produto CASCADE;
-- TRUNCATE TABLE representantes CASCADE;
-- TRUNCATE TABLE personal_access_tokens CASCADE;
-- DELETE FROM users WHERE id > 0;

-- Resetar sequences
-- ALTER SEQUENCE users_id_seq RESTART WITH 1;
-- ALTER SEQUENCE representantes_id_seq RESTART WITH 1;
-- ALTER SEQUENCE categorias_produto_id_seq RESTART WITH 1;
-- ALTER SEQUENCE almoxarifados_id_seq RESTART WITH 1;
-- ALTER SEQUENCE produtos_id_seq RESTART WITH 1;
-- ALTER SEQUENCE estoques_id_seq RESTART WITH 1;
-- ALTER SEQUENCE condicionais_id_seq RESTART WITH 1;
-- ALTER SEQUENCE condicional_itens_id_seq RESTART WITH 1;
-- ALTER SEQUENCE vendas_id_seq RESTART WITH 1;
-- ALTER SEQUENCE venda_itens_id_seq RESTART WITH 1;
-- ALTER SEQUENCE movimentacoes_estoque_id_seq RESTART WITH 1;

-- =============================================
-- 1. USUÁRIOS (senha: password123)
-- =============================================
-- INSERT INTO users (id, name, email, password, perfil, created_at, updated_at) VALUES
-- (1, 'Admin Sistema', 'admin@consignow.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
-- (2, 'João Silva', 'joao@consignow.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'representante', NOW(), NOW()),
-- (3, 'Maria Santos', 'maria@consignow.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'representante', NOW(), NOW()),
-- (4, 'Pedro Oliveira', 'pedro@consignow.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'representante', NOW(), NOW());

-- {
--     "name": "Admin Sistema",
--     "email": "admin@gmail.com",
--     "password": "admin123",
--     "perfil": "admin"
-- }
-- {
--     "name": "João Silva",
--     "email": "joao@consignow.com",
--     "password": "password123",
--     "perfil": "representante"
-- }
-- {
--     "name": "Maria Santos",
--     "email": "maria@consignow.com",
--     "password": "password123",
--     "perfil": "representante"
-- }
-- {
--     "name": "Pedro Oliveira",
--     "email": "pedro@consignow.com",
--     "password": "password123",
--     "perfil": "representante"
-- }
-- =============================================
-- 2. REPRESENTANTES
-- =============================================
-- INSERT INTO representantes (id, user_id, created_at, updated_at) VALUES
-- (1, 2, NOW(), NOW()),
-- (2, 3, NOW(), NOW()),
-- (3, 4, NOW(), NOW());
-- ITEM 5 NA HORA DE ADICIONAR
-- =============================================
-- 3. CATEGORIAS DE PRODUTOS
-- =============================================
INSERT INTO categorias_produto (id, descricao, created_at, updated_at) VALUES
(1, 'Leggings', NOW(), NOW()),
(2, 'Tops', NOW(), NOW()),
(3, 'Camisetas', NOW(), NOW()),
(4, 'Shorts', NOW(), NOW()),
(5, 'Acessórios Fitness', NOW(), NOW());

-- =============================================
-- 4. ALMOXARIFADOS
-- =============================================
INSERT INTO almoxarifados (id, descricao, tipo, representante_id, created_at, updated_at) VALUES
(1, 'Estoque Central', 'central', NULL, NOW(), NOW()),
(2, 'Maleta João', 'representante', 1, NOW(), NOW()),
(3, 'Maleta Maria', 'representante', 2, NOW(), NOW()),
(4, 'Maleta Pedro', 'representante', 3, NOW(), NOW());

-- =============================================
-- 5. PRODUTOS
-- =============================================
INSERT INTO produtos (id, descricao, categoria_id, preco_custo, preco_venda, created_at, updated_at) VALUES
-- Leggings
(1, 'Legging Cintura Alta Compressão', 1, 45.00, 119.90, NOW(), NOW()),
(2, 'Legging Seamless Anticelulite', 1, 38.00, 99.90, NOW(), NOW()),
(3, 'Legging Estampada Performance', 1, 52.00, 139.90, NOW(), NOW()),

-- Tops
(4, 'Top Fitness Suporte Médio', 2, 25.00, 69.90, NOW(), NOW()),
(5, 'Top Cross Strappy', 2, 32.00, 89.90, NOW(), NOW()),
(6, 'Top Alta Sustentação Running', 2, 48.00, 129.90, NOW(), NOW()),

-- Camisetas
(7, 'Camiseta Dry Fit Masculina', 3, 22.00, 59.90, NOW(), NOW()),
(8, 'Camiseta Oversized Treino', 3, 30.00, 79.90, NOW(), NOW()),
(9, 'Camiseta Regata Cavada Fitness', 3, 18.00, 49.90, NOW(), NOW()),

-- Shorts
(10, 'Shorts Cintura Alta com Bolso', 4, 28.00, 74.90, NOW(), NOW()),
(11, 'Shorts Corrida Leve e Respirável', 4, 20.00, 59.90, NOW(), NOW()),
(12, 'Shorts Térmico Masculino', 4, 26.00, 69.90, NOW(), NOW()),

-- Acessórios Fitness
(13, 'Luva de Treino Antiderrapante', 5, 18.00, 49.90, NOW(), NOW()),
(14, 'Meia Cano Médio Esportiva', 5, 8.00, 24.90, NOW(), NOW()),
(15, 'Toalha Fitness de Microfibra', 5, 12.00, 34.90, NOW(), NOW());

-- =============================================
-- 6. ESTOQUES (Central com bastante estoque)
-- =============================================
-- Estoque Central
INSERT INTO estoques (id, almoxarifado_id, produto_id, quantidade, created_at, updated_at) VALUES
(1, 1, 1, 50, NOW(), NOW()),
(2, 1, 2, 20, NOW(), NOW()),
(3, 1, 3, 30, NOW(), NOW()),
(4, 1, 4, 60, NOW(), NOW()),
(5, 1, 5, 40, NOW(), NOW()),
(6, 1, 6, 15, NOW(), NOW()),
(7, 1, 7, 80, NOW(), NOW()),
(8, 1, 8, 35, NOW(), NOW()),
(9, 1, 9, 25, NOW(), NOW()),
(10, 1, 10, 45, NOW(), NOW()),
(11, 1, 11, 28, NOW(), NOW()),
(12, 1, 12, 55, NOW(), NOW()),
(13, 1, 13, 22, NOW(), NOW()),
(14, 1, 14, 18, NOW(), NOW()),
(15, 1, 15, 32, NOW(), NOW());

-- Resetar sequences
ALTER SEQUENCE users_id_seq RESTART WITH 1;
ALTER SEQUENCE representantes_id_seq RESTART WITH 1;
ALTER SEQUENCE categorias_produto_id_seq RESTART WITH 1;
ALTER SEQUENCE almoxarifados_id_seq RESTART WITH 1;
ALTER SEQUENCE produtos_id_seq RESTART WITH 1;
ALTER SEQUENCE estoques_id_seq RESTART WITH 1;
ALTER SEQUENCE condicionais_id_seq RESTART WITH 1;
ALTER SEQUENCE condicional_itens_id_seq RESTART WITH 1;
ALTER SEQUENCE vendas_id_seq RESTART WITH 1;
ALTER SEQUENCE venda_itens_id_seq RESTART WITH 1;
ALTER SEQUENCE movimentacoes_estoque_id_seq RESTART WITH 1;

-- Estoque Representantes (maletas)
INSERT INTO estoques (id, almoxarifado_id, produto_id, quantidade, created_at, updated_at) VALUES
-- João (Almoxarifado 2)
(16, 2, 1, 5, NOW(), NOW()),
(17, 2, 4, 8, NOW(), NOW()),
(18, 2, 7, 10, NOW(), NOW()),
(19, 2, 10, 6, NOW(), NOW()),
(20, 2, 13, 3, NOW(), NOW()),
-- Maria (Almoxarifado 3)
(21, 3, 2, 4, NOW(), NOW()),
(22, 3, 5, 7, NOW(), NOW()),
(23, 3, 8, 6, NOW(), NOW()),
(24, 3, 11, 5, NOW(), NOW()),
(25, 3, 14, 2, NOW(), NOW()),
-- Pedro (Almoxarifado 4)
(26, 4, 3, 3, NOW(), NOW()),
(27, 4, 6, 5, NOW(), NOW()),
(28, 4, 9, 4, NOW(), NOW()),
(29, 4, 12, 8, NOW(), NOW()),
(30, 4, 15, 4, NOW(), NOW());

-- =============================================
-- 7. CONDICIONAIS (diferentes status)
-- =============================================
INSERT INTO condicionais (id, representante_id, almoxarifado_id, data_entrega, data_prevista_retorno, status, created_at, updated_at) VALUES
-- João - Condicionais
(1, 1, 2, '2024-12-01', '2024-12-15', 'aberta', NOW(), NOW()),
(2, 1, 2, '2024-11-15', '2024-11-30', 'finalizada', NOW(), NOW()),
-- Maria - Condicionais
(3, 2, 3, '2024-12-05', '2024-12-20', 'aberta', NOW(), NOW()),
(4, 2, 3, '2024-11-20', '2024-12-05', 'em_cobranca', NOW(), NOW()),
-- Pedro - Condicionais
(5, 3, 4, '2024-12-07', '2024-12-22', 'aberta', NOW(), NOW()),
(6, 3, 4, '2024-11-10', '2024-11-25', 'finalizada', NOW(), NOW());

-- =============================================
-- 8. ITENS DAS CONDICIONAIS
-- =============================================
INSERT INTO condicional_itens (condicional_id, produto_id, quantidade_entregue, quantidade_devolvida, quantidade_vendida, created_at, updated_at) VALUES
-- Condicional 1 (João - aberta)
(1, 1, 5, 0, 2, NOW(), NOW()),
(1, 4, 8, 2, 3, NOW(), NOW()),
(1, 7, 10, 4, 4, NOW(), NOW()),
-- Condicional 2 (João - finalizada)
(2, 10, 6, 2, 4, NOW(), NOW()),
(2, 13, 3, 1, 2, NOW(), NOW()),
-- Condicional 3 (Maria - aberta)
(3, 2, 4, 0, 1, NOW(), NOW()),
(3, 5, 7, 1, 2, NOW(), NOW()),
(3, 8, 6, 0, 3, NOW(), NOW()),
-- Condicional 4 (Maria - em_cobranca)
(4, 11, 5, 0, 3, NOW(), NOW()),
(4, 14, 2, 0, 1, NOW(), NOW()),
-- Condicional 5 (Pedro - aberta)
(5, 3, 3, 0, 0, NOW(), NOW()),
(5, 6, 5, 1, 2, NOW(), NOW()),
(5, 9, 4, 0, 1, NOW(), NOW()),
-- Condicional 6 (Pedro - finalizada)
(6, 12, 8, 3, 5, NOW(), NOW()),
(6, 15, 4, 2, 2, NOW(), NOW());

-- =============================================
-- 9. VENDAS (diferentes status e formas de pagamento)
-- =============================================
INSERT INTO vendas (id, representante_id, cliente_id, condicional_id, data_venda, valor_total, status, forma_pagamento, created_at, updated_at) VALUES
-- Vendas do João
(1, 1, NULL, 1, '2024-12-05 10:30:00', 519.70, 'paga', 'pix', NOW(), NOW()),
(2, 1, NULL, 1, '2024-12-06 14:15:00', 269.70, 'paga', 'cartao', NOW(), NOW()),
(3, 1, NULL, 2, '2024-11-20 09:00:00', 599.40, 'paga', 'dinheiro', NOW(), NOW()),
(4, 1, NULL, 2, '2024-11-22 16:45:00', 919.80, 'paga', 'cartao', NOW(), NOW()),
-- Vendas da Maria
(5, 2, NULL, 3, '2024-12-08 11:00:00', 899.90, 'aberta', 'pix', NOW(), NOW()),
(6, 2, NULL, 3, '2024-12-09 15:30:00', 499.80, 'aberta', 'cartao', NOW(), NOW()),
(7, 2, NULL, 4, '2024-11-25 10:15:00', 1449.60, 'paga', 'pix', NOW(), NOW()),
(8, 2, NULL, 4, '2024-11-28 14:00:00', 549.90, 'cancelada', 'dinheiro', NOW(), NOW()),
-- Vendas do Pedro
(9, 3, NULL, 5, '2024-12-10 09:30:00', 1399.80, 'aberta', 'outro', NOW(), NOW()),
(10, 3, NULL, 5, '2024-12-10 16:00:00', 389.90, 'paga', 'pix', NOW(), NOW()),
(11, 3, NULL, 6, '2024-11-15 11:30:00', 399.50, 'paga', 'cartao', NOW(), NOW()),
(12, 3, NULL, 6, '2024-11-18 13:45:00', 759.80, 'paga', 'dinheiro', NOW(), NOW());

-- =============================================
-- 10. ITENS DAS VENDAS
-- =============================================
INSERT INTO venda_itens (venda_id, produto_id, quantidade, preco_unitario, created_at, updated_at) VALUES
-- Venda 1 (João - pix)
(1, 1, 2, 129.90, NOW(), NOW()),
(1, 4, 3, 89.90, NOW(), NOW()),
-- Venda 2 (João - cartão)
(2, 7, 4, 69.90, NOW(), NOW()),
-- Venda 3 (João - dinheiro)
(3, 10, 4, 99.90, NOW(), NOW()),
(3, 13, 2, 459.90, NOW(), NOW()),
-- Venda 4 (João - cartão)
(4, 13, 2, 459.90, NOW(), NOW()),
-- Venda 5 (Maria - aberta pix)
(5, 2, 1, 899.90, NOW(), NOW()),
-- Venda 6 (Maria - aberta cartão)
(6, 5, 2, 149.90, NOW(), NOW()),
(6, 8, 1, 249.90, NOW(), NOW()),
-- Venda 7 (Maria - paga pix)
(7, 11, 3, 299.90, NOW(), NOW()),
(7, 14, 1, 549.90, NOW(), NOW()),
-- Venda 8 (Maria - cancelada)
(8, 14, 1, 549.90, NOW(), NOW()),
-- Venda 9 (Pedro - aberta outro)
(9, 6, 2, 699.90, NOW(), NOW()),
-- Venda 10 (Pedro - paga pix)
(10, 9, 1, 389.90, NOW(), NOW()),
-- Venda 11 (Pedro - paga cartão)
(11, 12, 5, 79.90, NOW(), NOW()),
-- Venda 12 (Pedro - paga dinheiro)
(12, 15, 2, 379.90, NOW(), NOW());

-- =============================================
-- RESUMO DOS DADOS CRIADOS
-- =============================================
-- Usuários: 4 (1 admin, 3 representantes)
-- Representantes: 3
-- Categorias: 5
-- Almoxarifados: 4 (1 central + 3 representantes)
-- Produtos: 15
-- Condicionais: 6 (3 abertas, 1 em_cobranca, 2 finalizadas)
-- Vendas: 12 (7 pagas, 3 abertas, 1 cancelada)
--   - Formas: pix(4), cartao(4), dinheiro(3), outro(1)
--   - Status: paga(7), aberta(4), cancelada(1)

SELECT 'Dados de teste inseridos com sucesso!' as status;
