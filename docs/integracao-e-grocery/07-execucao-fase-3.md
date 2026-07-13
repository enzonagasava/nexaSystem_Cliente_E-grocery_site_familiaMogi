# 07 - Execucao da Fase 3 no familiaMogi

Data de execução: 2026-04-27

## 1. Objetivo entregue

Implementar sincronização incremental de catálogo (`anuncios`, `produtos`, `imagens`) com persistência local.

## 2. Implementacoes realizadas

- Cliente HTTP do E-grocery: `app/Services/Integrations/EGroceryApiClient.php`.
- Serviço de sync incremental: `app/Services/Integrations/EGroceryCatalogSyncService.php`.
- Handler por tipo de evento de webhook: `app/Services/Integrations/EGroceryWebhookEventHandler.php`.
- Integração do job de webhook com handler de domínio em `app/Jobs/ProcessEGroceryWebhookEventJob.php`.
- Comando Artisan de sync: `integrations:e-grocery:sync-catalog` em `routes/console.php`.

Persistência local criada:

- `database/migrations/2026_04_27_110000_create_e_grocery_ads_table.php`.
- `database/migrations/2026_04_27_110100_create_e_grocery_images_table.php`.
- `database/migrations/2026_04_27_110200_create_e_grocery_products_table.php`.
- Model: `app/Models/EGroceryAd.php`.
- Model: `app/Models/EGroceryImage.php`.

## 3. Comando de sync (via container)

Execução padrão:

```bash
docker compose exec app php artisan integrations:e-grocery:sync-catalog
```

Execução incremental:

```bash
docker compose exec app php artisan integrations:e-grocery:sync-catalog --updated-since=2026-04-27T00:00:00Z
```

Execução sem buscar imagens:

```bash
docker compose exec app php artisan integrations:e-grocery:sync-catalog --skip-images
```

Execução com limite de páginas (teste controlado):

```bash
docker compose exec app php artisan integrations:e-grocery:sync-catalog --max-pages=1
```

## 4. Comportamento atual da Fase 3

- Sync consulta anúncios e produtos com paginação por `next_cursor`.
- Sync faz `upsert` de anúncios por `external_ad_id`.
- Sync faz `upsert` de produtos por `external_sku`.
- Sync faz `upsert` de imagens por `external_image_id`.
- Quando `syncImages=true`, busca imagem por `image_id` e persiste metadados/URL.
- Job de webhook agora roteia eventos de catálogo para atualização local.

## 5. Proximo passo (Fase 4)

- Exportar pedidos fechados do `familiaMogi` para `POST /api/v1/pedidos` no painel.
- Garantir idempotência por `external_order_id`.
- Tratar retry assíncrono e auditoria de envio.
