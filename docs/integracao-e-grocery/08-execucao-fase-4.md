# 08 - Execucao da Fase 4 no familiaMogi

Data de execução: 2026-04-27

## 1. Objetivo entregue

Implementar exportação assíncrona de pedidos do `familiaMogi` para o painel E-grocery (`POST /api/v1/pedidos`), com idempotência por `external_order_id`.

## 2. Implementacoes realizadas

- Endpoint de exportação ativado: `POST /api/v1/integrations/e-grocery/orders`.
- Controller atualizado: `app/Http/Controllers/Integrations/EGroceryOrderExportController.php`.
- Serviço de exportação/idempotência: `app/Services/Integrations/EGroceryOrderExportService.php`.
- Job de envio ao painel: `app/Jobs/SendEGroceryOrderJob.php`.
- Cliente API recebeu método de criação de pedido: `app/Services/Integrations/EGroceryApiClient.php#createOrder`.
- Frontend atualizado para endpoint padrão da Fase 4: `VITE_EGROCERY_CHECKOUT_ENDPOINT=/api/v1/integrations/e-grocery/orders`.

Persistência de auditoria:

- Migration: `database/migrations/2026_04_27_120000_create_e_grocery_order_exports_table.php`.
- Model: `app/Models/EGroceryOrderExport.php`.

## 3. Comportamento do endpoint de pedidos

- Valida payload mínimo de pedido.
- Gera `external_order_id` quando não informado.
- Garante idempotência por `external_order_id`.
- Se novo, persiste registro `queued`.
- Se novo, enfileira `SendEGroceryOrderJob`.
- Se novo, retorna `202`.
- Se duplicado, retorna `200` com status atual do registro (`queued`, `processing`, `sent`, `failed`).

## 4. Trilhas de auditoria salvas

- Payload recebido (`request_payload`).
- Payload normalizado para contrato do painel (`normalized_payload`).
- Resposta do painel (`panel_response`).
- `panel_order_id` (quando disponível).
- `attempt_count`, `last_attempt_at`, `exported_at`, `error_message`, `status`.

## 5. Proximo passo (Fase 5)

- Migrar/garantir imagens em storage objeto + CDN.
- Usar `storage_key` e metadados como fonte padrão para catálogo no frontend.
