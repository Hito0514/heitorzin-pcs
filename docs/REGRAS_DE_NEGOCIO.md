# Regras de negócio – Heitorzin PCs

## Tema e descrição
Loja virtual de peças para computador. Clientes compram pela vitrine pública; a equipe administra tudo por um painel restrito.

## Atores
- **Visitante:** navega, busca, filtra, vê detalhes e usa o carrinho; precisa de conta para finalizar.
- **Cliente:** cadastrado pela própria interface; finaliza pedidos e consulta apenas os **seus** pedidos.
- **Gerente:** acessa o painel (produtos, categorias, fornecedores, clientes, pedidos, relatórios).
- **Administrador:** tudo do gerente + gerenciamento de usuários.

## Regras
1. **RN01** – E-mail é único em `usuarios` e `clientes`, e não pode se repetir entre as duas tabelas (o login é único).
2. **RN02** – Senhas têm no mínimo 6 caracteres e são guardadas com `password_hash()`.
3. **RN03** – Cliente se registra com nome, e-mail, telefone (com DDD) e senha.
4. **RN04** – A área `/admin` só é acessível por usuários ativos autenticados; clientes não entram.
5. **RN05** – Somente administradores gerenciam usuários. Ninguém exclui a própria conta nem o último administrador.
6. **RN06** – Todo produto pertence a uma categoria; o fornecedor é opcional.
7. **RN07** – Preço e estoque não podem ser negativos.
8. **RN08** – Produto inativo não aparece na loja nem pode ser comprado.
9. **RN09** – Categoria, produto vendido ou cliente com pedidos **não podem ser excluídos** (integridade referencial).
10. **RN10** – O carrinho não aceita quantidade acima do estoque disponível.
11. **RN11** – Ao criar um pedido, o estoque é baixado e o preço unitário é gravado no item (histórico). Tudo ocorre em **transação**: se um item falhar, nada é gravado.
12. **RN12** – Pedido cancelado devolve o estoque; reativar baixa novamente (se houver estoque). Excluir um pedido ativo devolve o estoque.
13. **RN13** – Status do pedido: `pendente → pago → enviado → entregue` ou `cancelado`.
14. **RN14** – Pedidos cancelados não entram no faturamento do dashboard/relatório.
15. **RN15** – Imagens de produto: JPG, PNG, WEBP ou GIF, até 2 MB, validadas pelo tipo real do arquivo.
16. **RN16** – Alerta de estoque baixo no dashboard para produtos com 5 unidades ou menos.
