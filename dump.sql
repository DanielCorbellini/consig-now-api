-- WARNING: This schema is for context only and is not meant to be run.
-- Table order and constraints may not be valid for execution.

CREATE TABLE public.almoxarifados (
  id bigint NOT NULL DEFAULT nextval('almoxarifados_id_seq'::regclass),
  descricao character varying NOT NULL,
  tipo character varying NOT NULL CHECK (tipo::text = ANY (ARRAY['central'::character varying, 'representante'::character varying]::text[])),
  representante_id bigint,
);
CREATE TABLE public.condicionais (
  id bigint NOT NULL DEFAULT nextval('condicionais_id_seq'::regclass),
  representante_id bigint NOT NULL,
  data_entrega date NOT NULL,
  data_prevista_retorno date,
  status character varying NOT NULL DEFAULT 'aberta'::character varying CHECK (status::text = ANY (ARRAY['aberta'::character varying, 'finalizada'::character varying, 'em_cobranca'::character varying]::text[])),
);
CREATE TABLE public.condicional_itens (
  id bigint NOT NULL DEFAULT nextval('condicional_itens_id_seq'::regclass),
  condicional_id bigint NOT NULL,
  produto_id bigint NOT NULL,
  quantidade_entregue integer NOT NULL,
  quantidade_devolvida integer NOT NULL DEFAULT 0,
  quantidade_vendida integer NOT NULL DEFAULT 0,
);
CREATE TABLE public.estoques (
  id bigint NOT NULL DEFAULT nextval('estoques_id_seq'::regclass),
  almoxarifado_id bigint NOT NULL,
  produto_id bigint NOT NULL,
  quantidade integer NOT NULL DEFAULT 0,
);
CREATE TABLE public.movimentacoes_estoque (
  id bigint NOT NULL DEFAULT nextval('movimentacoes_estoque_id_seq'::regclass),
  produto_id bigint NOT NULL,
  almox_origem_id bigint,
  almox_destino_id bigint,
  tipo character varying NOT NULL CHECK (tipo::text = ANY (ARRAY['entrada'::character varying, 'saida'::character varying, 'transferencia'::character varying, 'ajuste'::character varying, 'devolucao'::character varying, 'consignação'::character varying]::text[])),
  quantidade integer NOT NULL,
  user_id bigint,
  referencia_type character varying NOT NULL,
  referencia_id bigint NOT NULL,
);
CREATE TABLE public.produtos (
  id bigint NOT NULL DEFAULT nextval('produtos_id_seq'::regclass),
  descricao character varying NOT NULL,
  categoria_id bigint NOT NULL,
  preco_custo numeric NOT NULL,
  preco_venda numeric NOT NULL,
);
CREATE TABLE public.venda_itens (
  id bigint NOT NULL DEFAULT nextval('venda_itens_id_seq'::regclass),
  venda_id bigint NOT NULL,
  produto_id bigint NOT NULL,
  quantidade integer NOT NULL,
  preco_unitario numeric NOT NULL,
);
CREATE TABLE public.vendas (
  id bigint NOT NULL DEFAULT nextval('vendas_id_seq'::regclass),
  representante_id bigint NOT NULL,
  cliente_id bigint,
  condicional_id bigint,
  data_venda timestamp without time zone NOT NULL,
  valor_total numeric NOT NULL,
  status character varying NOT NULL DEFAULT 'aberta'::character varying CHECK (status::text = ANY (ARRAY['aberta'::character varying, 'paga'::character varying, 'cancelada'::character varying]::text[])),
  forma_pagamento character varying NOT NULL CHECK (forma_pagamento::text = ANY (ARRAY['dinheiro'::character varying, 'cartao'::character varying, 'pix'::character varying, 'outro'::character varying]::text[])),
);


