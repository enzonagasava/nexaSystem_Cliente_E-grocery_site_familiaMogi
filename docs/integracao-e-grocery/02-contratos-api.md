# 02 - Contratos de API (Base v1)

Base path recomendado:

- `/api/v1`

Formato padrão:

- `Content-Type: application/json`
- Datas em ISO-8601 UTC
- Paginação cursor ou `page/per_page` (definir um padrão único antes de produção)

## 1. Catalogo e anuncios

### `GET /api/v1/anuncios`

Query params:

- `updated_since` (opcional)
- `status` (opcional)

Resposta (exemplo):

```json
{
  "data": [
    {
      "id": "ad_1029",
      "title": "Oferta de fim de semana",
      "description": "Descontos em hortifruti",
      "status": "active",
      "priority": 10,
      "starts_at": "2026-04-27T09:00:00Z",
      "ends_at": "2026-04-30T23:59:59Z",
      "updated_at": "2026-04-27T10:22:11Z"
    }
  ],
  "meta": {
    "next_cursor": "eyJ1cGRhdGVkX2F0Ijoi..."
  }
}
```

### `GET /api/v1/produtos`

Query params:

- `updated_since` (opcional)
- `category_id` (opcional)
- `active` (opcional)

Resposta (exemplo):

```json
{
  "data": [
    {
      "sku": "7891000100103",
      "name": "Arroz Tipo 1 5kg",
      "category": "Mercearia",
      "price": 29.9,
      "stock": 85,
      "status": "active",
      "image_id": "img_3930",
      "updated_at": "2026-04-27T10:20:00Z"
    }
  ],
  "meta": {
    "next_cursor": null
  }
}
```

### `GET /api/v1/produtos/{sku}`

Resposta deve incluir:

- Dados completos para PDP (descrição longa, pesos, variações, lista de imagens).

## 2. Imagens

### `GET /api/v1/imagens/{image_id}`

Resposta (exemplo):

```json
{
  "id": "img_3930",
  "storage_key": "catalog/2026/04/27/7891000100103/main.jpg",
  "url": "https://cdn.familiamogi.com/catalog/2026/04/27/7891000100103/main.jpg",
  "mime_type": "image/jpeg",
  "width": 1200,
  "height": 1200,
  "checksum": "sha256:0f6c..."
}
```

Observação:

- Se a imagem for privada, retornar URL assinada com expiração.

## 3. Pedidos e vendas

### `POST /api/v1/pedidos`

Uso:

- `familiaMogi` envia pedido finalizado para o painel.

Request (exemplo):

```json
{
  "external_order_id": "fm-20260427-00091",
  "created_at": "2026-04-27T12:30:10Z",
  "customer": {
    "name": "Maria Silva",
    "phone": "+55-11-99999-9999"
  },
  "items": [
    {
      "sku": "7891000100103",
      "qty": 2,
      "unit_price": 29.9
    }
  ],
  "totals": {
    "subtotal": 59.8,
    "delivery_fee": 8.0,
    "discount": 0.0,
    "grand_total": 67.8
  },
  "payment": {
    "method": "pix",
    "status": "paid"
  },
  "delivery": {
    "type": "delivery",
    "address": {
      "zip": "08770-000",
      "street": "Rua Exemplo",
      "number": "123",
      "city": "Mogi das Cruzes",
      "state": "SP"
    }
  }
}
```

Response (exemplo):

```json
{
  "order_id": "eg-550198",
  "status": "received",
  "received_at": "2026-04-27T12:30:11Z"
}
```

### `GET /api/v1/pedidos/{order_id}`

Uso:

- Consultar status de processamento no painel.

## 4. Regras de contrato

- Campo monetário em decimal com 2 casas.
- `sku` é chave estável de produto.
- `external_order_id` deve ser único por origem (`familiaMogi`).
- Erros no formato `4xx` com payload `{ "message": "...", "code": "..." }`.
