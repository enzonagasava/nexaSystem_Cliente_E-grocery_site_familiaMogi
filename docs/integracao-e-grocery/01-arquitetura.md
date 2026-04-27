# 01 - Arquitetura de Integracao

## 1. Objetivo

Integrar `familiaMogi` e `nexaSystem_E-grocery` usando:

- API REST para consultas e carga inicial.
- Webhook para propagação de mudanças em tempo quase real.

## 2. Papeis dos sistemas

- `nexaSystem_E-grocery`: fonte de verdade de catálogo, anúncios, preço, estoque e ciclo do pedido.
- `familiaMogi`: vitrine e checkout web do cliente final.
- `familiaMogi` replica dados relevantes para exibição rápida e cria pedidos enviados ao painel.

## 3. Modelo de sincronizacao

- Pull (API): carga inicial de dados e reconciliação periódica por `updated_at`.
- Push (Webhook): atualizações imediatas após mudanças no painel.

Estratégia combinada:

- API resolve estado completo.
- Webhook reduz latência e evita polling agressivo.

## 4. Dominios de dados

- Anúncios: título, descrição, prioridade, vigência, status.
- Produtos: SKU, nome, categoria, preço, estoque, status.
- Imagens: `image_id`, `storage_key`, URL/CDN, metadados.
- Pedidos/Vendas: pedido, itens, pagamento, status de atendimento.

## 5. Imagens em storage separado

Diretriz:

- Arquivo físico fora do app principal (Object Storage).
- Banco armazena metadados e caminho lógico (`storage_key`).

Benefícios:

- Escala horizontal sem acoplar arquivo ao servidor web.
- Melhor cache e entrega via CDN.
- Menor custo de CPU/disco no app transacional.

## 6. Princípios tecnicos

- Versionamento de API (`/api/v1`).
- Idempotência por `event_id`.
- Rastreabilidade por `correlation_id`.
- Resiliência com fila e retry exponencial.
- Segurança por assinatura HMAC em webhooks.
