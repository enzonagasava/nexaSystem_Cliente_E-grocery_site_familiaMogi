# 04 - Plano de Implementacao no familiaMogi

Este passo transforma os contratos em execução técnica no projeto Laravel atual.

## 1. Fase 1 - Foundation (familiaMogi)

Objetivo:

- Preparar configuração, segurança e estrutura de integração sem alterar o checkout existente.

Checklist:

- Criar `config/integrations.php`.
- Definir `e_grocery.base_url`.
- Definir `e_grocery.api_token` ou credenciais OAuth de cliente.
- Definir `e_grocery.webhook_secret`.
- Definir `e_grocery.timeout_seconds`.
- Criar variáveis no `.env.example` para os campos acima.
- Definir rota `POST /api/v1/integrations/e-grocery/webhooks`.
- Definir rota `POST /api/v1/integrations/e-grocery/orders` (opcional, se houver gateway interno).
- Adicionar logs dedicados de integração (`stack` ou canal próprio).

Critério de aceite:

- Configuração carregando corretamente em ambiente local/docker.

## 2. Fase 2 - Recepcao de webhook

Objetivo:

- Receber eventos do painel com segurança e idempotência.

Checklist:

- Implementar `WebhookController`.
- Validar `X-Signature`.
- Validar schema básico (`event_id`, `event_type`, `occurred_at`, `data`).
- Persistir `event_id` em tabela de controle (`integration_events`).
- Enfileirar Job por tipo (`ProcessProductUpdatedJob`, etc.).
- Retornar `202` para processamento assíncrono.

Critério de aceite:

- Mesmo `event_id` não processa duas vezes.

## 3. Fase 3 - Sincronizacao de catalogo

Objetivo:

- Alimentar vitrine com dados vindos do painel.

Checklist:

- Criar serviço `EgroceryApiClient` para `GET /anuncios`, `GET /produtos`, `GET /imagens/{id}`.
- Criar comando Artisan `php artisan integrations:e-grocery:sync-catalog --updated-since=...`.
- Persistir catálogo em tabelas locais (ou cache/materialized view) para leitura rápida no frontend.

Critério de aceite:

- Catálogo visível no site com atualização incremental.

## 4. Fase 4 - Envio de pedidos/vendas

Objetivo:

- Publicar pedidos fechados no `familiaMogi` para o painel.

Checklist:

- Criar `OrderExportService` com idempotência por `external_order_id`.
- Enviar para `POST /api/v1/pedidos`.
- Tratar retry com fila em caso de falha transitória.
- Registrar auditoria de envio e resposta do painel.

Critério de aceite:

- Pedido criado no site aparece no painel com rastreabilidade.

## 5. Fase 5 - Imagens em storage separado

Objetivo:

- Manter caminho de imagem desacoplado do servidor da aplicação.

Checklist:

- Garantir uso de disk de objeto (`s3`/compatível) para novas imagens.
- Salvar apenas `storage_key` e metadados no banco.
- Expor URL CDN ou URL assinada dependendo da política de privacidade.

Critério de aceite:

- Troca de servidor de aplicação sem impacto em imagens.

## 6. Fase 6 - Operacao e reconciliacao

Objetivo:

- Operar integração com visibilidade e recuperação.

Checklist:

- Job noturno de reconciliação por `updated_at`.
- Painel simples de saúde: eventos recebidos, falhas, retries.
- Alertas para pico de erro e fila acumulada.

Critério de aceite:

- Drift entre sistemas detectável e corrigível.

## 7. Ordem de execucao recomendada

1. Fase 1
2. Fase 2
3. Fase 3
4. Fase 4
5. Fase 5
6. Fase 6
