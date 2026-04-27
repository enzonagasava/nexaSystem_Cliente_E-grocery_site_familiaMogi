# Integracao familiaMogi <-> nexaSystem_E-grocery

Este diretório centraliza o plano de integração entre o site `familiaMogi` (canal de venda) e o painel `nexaSystem_E-grocery` (fonte de verdade operacional).

Ordem recomendada de leitura e execução:

1. [01-arquitetura.md](./01-arquitetura.md)
2. [02-contratos-api.md](./02-contratos-api.md)
3. [03-contratos-webhook.md](./03-contratos-webhook.md)
4. [04-plano-implementacao-familiaMogi.md](./04-plano-implementacao-familiaMogi.md)
5. [05-execucao-fase-1.md](./05-execucao-fase-1.md)
6. [06-execucao-fase-2.md](./06-execucao-fase-2.md)
7. [07-execucao-fase-3.md](./07-execucao-fase-3.md)
8. [08-execucao-fase-4.md](./08-execucao-fase-4.md)
9. [09-execucao-fase-5.md](./09-execucao-fase-5.md)
10. [10-execucao-fase-6.md](./10-execucao-fase-6.md)

Objetivo principal:

- Padronizar contratos entre sistemas.
- Permitir sincronização confiável de anúncios, imagens, catálogo e vendas.
- Deixar o fluxo pronto para evolução incremental sem quebrar produção.
