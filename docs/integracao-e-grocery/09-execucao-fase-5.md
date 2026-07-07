# 09 - Execucao da Fase 5 no familiaMogi (MinIO local S3)

Data de execução: 2026-04-27

## 1. Objetivo entregue

Ativar storage externo de imagens usando MinIO (compatível com S3) no ambiente local, com persistência de metadados no banco e API de upload/listagem.

## 2. Infraestrutura adicionada

- Serviço `minio` no `docker-compose.yml`.
- Serviço `minio-init` no `docker-compose.yml` para criar bucket `familia-mogi`.
- Volume persistente `miniodata`.

Portas locais:

- API S3: `9000`
- Console MinIO: `9001`

## 3. Configuracao de ambiente

Variáveis configuradas para MinIO local:

- `FILESYSTEM_DISK=s3`
- `AWS_ACCESS_KEY_ID=minioadmin`
- `AWS_SECRET_ACCESS_KEY=minioadmin`
- `AWS_BUCKET=familia-mogi`
- `AWS_URL=http://localhost:9000/familia-mogi`
- `AWS_ENDPOINT=http://minio:9000`
- `AWS_USE_PATH_STYLE_ENDPOINT=true`

Arquivos atualizados:

- `.env`
- `.env.example`

## 4. Endpoints de imagem (storage externo)

Base: `/api/v1/integrations/e-grocery`

- `POST /images/upload`
- Upload de imagem para MinIO (disk `s3`).
- Persiste/atualiza metadados em `e_grocery_images`.
- `GET /images`
- Lista imagens persistidas.
- `GET /images/{externalImageId}`
- Busca uma imagem específica por id externo.

Implementação:

- Controller: `app/Http/Controllers/Integrations/EGroceryImageStorageController.php`
- Rotas: `routes/api.php`

## 5. Fluxo de upload implementado

- Recebe arquivo de imagem.
- Gera `external_image_id` quando ausente.
- Monta `storage_key` com data e SKU.
- Envia para MinIO via `Storage::disk('s3')`.
- Coleta metadados (`mime`, `width`, `height`, `checksum`).
- Faz `upsert` em `e_grocery_images`.

## 6. Como operar localmente

Subir serviços:

```bash
docker compose up -d --build
docker compose up -d minio minio-init
```

Aplicar migrations:

```bash
docker compose exec app php artisan migrate
```

Exemplo de upload:

```bash
curl -X POST http://localhost:8040/api/v1/integrations/e-grocery/images/upload \
  -F "image=@/caminho/arquivo.jpg" \
  -F "product_sku=7891000100103"
```

## 7. Proximo passo (Fase 6)

- Reconciliação noturna automática (`updated_at`).
- Painel de saúde da integração (eventos, retries, falhas).
- Alertas para fila acumulada/falha de exportação.

