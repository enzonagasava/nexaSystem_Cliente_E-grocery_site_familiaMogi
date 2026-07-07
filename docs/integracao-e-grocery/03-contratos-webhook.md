# 03 - Contratos de Webhook

## 1. Objetivo

Publicar eventos de mudança do painel para o `familiaMogi` sem depender apenas de polling.

## 2. Endpoint de recepcao no familiaMogi

Proposta:

- `POST /api/v1/integrations/e-grocery/webhooks`

Headers obrigatórios:

- `X-Event-Id`: UUID do evento.
- `X-Event-Type`: tipo (`product.updated`, etc.).
- `X-Event-Time`: timestamp ISO-8601.
- `X-Signature`: assinatura HMAC-SHA256 do body bruto.

## 3. Catalogo inicial de eventos

- `ad.created`
- `ad.updated`
- `ad.deleted`
- `product.updated`
- `price.updated`
- `stock.updated`
- `image.updated`
- `image.deleted`
- `order.created`
- `order.paid`
- `order.cancelled`
- `order.fulfilled`

## 4. Payload padrao

```json
{
  "event_id": "bcb0f6c7-6de1-4fd8-bcd2-3092b9f03d9c",
  "event_type": "product.updated",
  "occurred_at": "2026-04-27T12:36:01Z",
  "source": "nexaSystem_E-grocery",
  "entity": {
    "type": "product",
    "id": "7891000100103",
    "version": 14
  },
  "data": {
    "sku": "7891000100103",
    "price": 27.9,
    "stock": 120,
    "status": "active",
    "image_id": "img_3930",
    "updated_at": "2026-04-27T12:35:54Z"
  }
}
```

## 5. Validacao de assinatura

Regra:

- `signature = HMAC_SHA256(raw_body, WEBHOOK_SECRET)`.
- Comparar com `X-Signature` usando comparação em tempo constante.

Se falhar:

- Retornar `401` e registrar tentativa no log de segurança.

## 6. Idempotencia

Regras:

- Persistir `event_id` processados.
- Se evento repetido, responder `200` sem reprocessar.

## 7. Entrega e retry

Regras recomendadas no emissor:

- Timeout de requisição curto (ex.: 5s).
- Retries com backoff: 1m, 5m, 15m, 1h.
- Após limite, mover para DLQ (dead-letter queue).

Regras no receptor (`familiaMogi`):

- Validar rápido e enfileirar processamento.
- Retornar `202` após aceite quando processamento for assíncrono.

## 8. Observabilidade

- Logar `event_id`, `event_type`, latência e resultado.
- Publicar métrica de falha por tipo de evento.
- Alertar se backlog da fila crescer acima do limite.

