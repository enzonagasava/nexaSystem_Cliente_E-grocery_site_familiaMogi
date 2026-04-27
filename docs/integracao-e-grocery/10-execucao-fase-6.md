# 10 - Execucao da Fase 6 no familiaMogi (Reconciliacao + Health + Alertas)

Data de execução: 2026-04-27

## 1. Objetivo entregue

Implementar operação contínua da integração com:

- Reconciliação noturna automática de catálogo.
- Health endpoint com métricas e alertas.
- Alertas periódicos por comando agendado.

## 2. Componentes implementados

- Serviço de health da integração: `app/Services/Integrations/EGroceryIntegrationHealthService.php`.
- Endpoint de health: `GET /api/v1/integrations/e-grocery/health`.
- Controller de health: `app/Http/Controllers/Integrations/EGroceryIntegrationHealthController.php`.
- Comando de reconciliação: `integrations:e-grocery:reconcile`.
- Comando de health check: `integrations:e-grocery:health-check`.
- Agendamento de reconciliação diária às `02:00`.
- Agendamento de health-check a cada `15` minutos.

## 3. Automacao de schedule em container

Adicionado serviço:

- `scheduler` no `docker-compose.yml`

Comando:

- `php artisan schedule:work`

## 4. Alertas configuraveis

Thresholds adicionados em config/env:

- `EGROCERY_MAX_PROCESSING_EVENTS`
- `EGROCERY_MAX_FAILED_EVENTS`
- `EGROCERY_MAX_QUEUED_ORDER_EXPORTS`
- `EGROCERY_MAX_FAILED_ORDER_EXPORTS`
- `EGROCERY_MAX_STALE_PROCESSING_MINUTES`

## 5. Operacao manual (quando necessario)

Rodar reconciliação:

```bash
docker compose exec app php artisan integrations:e-grocery:reconcile --retry-failed-orders --retry-limit=200
```

Rodar health-check:

```bash
docker compose exec app php artisan integrations:e-grocery:health-check --hours=24
```

Consultar health endpoint:

```bash
curl http://localhost:8040/api/v1/integrations/e-grocery/health
```

## 6. Resultado esperado

- Drift entre sistemas detectado e corrigido pela reconciliação.
- Situação operacional visível por endpoint e comando.
- Alertas registrados no canal `e_grocery_integration` quando thresholds são ultrapassados.
