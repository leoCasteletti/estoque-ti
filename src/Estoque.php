<?php

declare(strict_types=1);

final class Estoque
{
    public function __construct(private Armazem $armazem)
    {
    }

    /** @return list<array<string, mixed>> */
    public function itens(string $busca = '', string $categoria = ''): array
    {
        $busca = mb_strtolower(trim($busca));
        $itens = array_filter($this->armazem->ler('itens'), function (array $i) use ($busca, $categoria) {
            $texto = mb_strtolower($i['nome'] . ' ' . $i['codigo']);
            return ($busca === '' || str_contains($texto, $busca))
                && ($categoria === '' || $i['categoria'] === $categoria);
        });
        usort($itens, fn ($a, $b) => strcasecmp($a['nome'], $b['nome']));

        return $itens;
    }

    /** @return array<string, mixed>|null */
    public function item(string $id): ?array
    {
        foreach ($this->armazem->ler('itens') as $i) {
            if ($i['id'] === $id) {
                return $i;
            }
        }

        return null;
    }

    /** @return list<string> */
    public function categorias(): array
    {
        $categorias = array_unique(array_column($this->armazem->ler('itens'), 'categoria'));
        sort($categorias);

        return array_values(array_filter($categorias));
    }

    /** @return array{total: int, critico: int, zerado: int, unidades: int} */
    public function resumo(): array
    {
        $itens = $this->armazem->ler('itens');

        return [
            'total' => count($itens),
            'critico' => count(array_filter($itens, fn ($i) => $this->critico($i))),
            'zerado' => count(array_filter($itens, fn ($i) => $i['quantidade'] === 0)),
            'unidades' => array_sum(array_column($itens, 'quantidade')),
        ];
    }

    /** @return list<array<string, mixed>> */
    public function criticos(): array
    {
        return array_values(array_filter($this->itens(), fn ($i) => $this->critico($i)));
    }

    /** @param array<string, mixed> $i */
    public function critico(array $i): bool
    {
        return $i['quantidade'] <= $i['minimo'];
    }

    /** @param array{codigo: string, nome: string, categoria: string, minimo: int} $dados */
    public function salvarItem(?string $id, array $dados): void
    {
        $dados = $this->validarItem($dados);

        $this->armazem->alterar('itens', function (array $itens) use ($id, $dados) {
            foreach ($itens as $i) {
                if ($i['codigo'] === $dados['codigo'] && $i['id'] !== $id) {
                    throw new DomainException("O código {$dados['codigo']} já está em uso.");
                }
            }

            if ($id === null) {
                $itens[] = ['id' => bin2hex(random_bytes(6)), 'quantidade' => 0] + $dados;
                return $itens;
            }

            foreach ($itens as &$i) {
                if ($i['id'] === $id) {
                    $i = array_merge($i, $dados);
                }
            }

            return $itens;
        });
    }

    public function excluirItem(string $id): void
    {
        $item = $this->item($id) ?? throw new DomainException('Item não encontrado.');
        if ($item['quantidade'] > 0) {
            throw new DomainException('Zere o estoque antes de excluir o item.');
        }

        $this->armazem->alterar('itens', fn ($itens) => array_filter($itens, fn ($i) => $i['id'] !== $id));
    }

    public function movimentar(string $id, string $tipo, int $quantidade, string $usuario, string $destino = '', string $observacao = ''): void
    {
        if (!in_array($tipo, ['entrada', 'saida'], true)) {
            throw new DomainException('Tipo de movimentação inválido.');
        }
        if ($quantidade < 1) {
            throw new DomainException('Informe uma quantidade maior que zero.');
        }
        if ($tipo === 'saida' && trim($destino) === '') {
            throw new DomainException('Informe para quem ou para onde vai o item.');
        }

        $nome = '';
        $this->armazem->alterar('itens', function (array $itens) use ($id, $tipo, $quantidade, &$nome) {
            foreach ($itens as &$i) {
                if ($i['id'] !== $id) {
                    continue;
                }
                $novo = $i['quantidade'] + ($tipo === 'entrada' ? $quantidade : -$quantidade);
                if ($novo < 0) {
                    throw new DomainException("Saldo insuficiente: há {$i['quantidade']} em estoque.");
                }
                $i['quantidade'] = $novo;
                $nome = $i['nome'];
                return $itens;
            }
            throw new DomainException('Item não encontrado.');
        });

        $this->armazem->alterar('movimentos', function (array $movimentos) use ($id, $nome, $tipo, $quantidade, $usuario, $destino, $observacao) {
            $movimentos[] = [
                'data' => date('Y-m-d H:i:s'),
                'item_id' => $id,
                'item' => $nome,
                'tipo' => $tipo,
                'quantidade' => $quantidade,
                'destino' => trim($destino),
                'observacao' => trim($observacao),
                'usuario' => $usuario,
            ];
            return $movimentos;
        });
    }

    /** @return list<array<string, mixed>> */
    public function historico(string $busca = '', string $tipo = '', int $limite = 200): array
    {
        $busca = mb_strtolower(trim($busca));
        $movimentos = array_filter($this->armazem->ler('movimentos'), function (array $m) use ($busca, $tipo) {
            $texto = mb_strtolower($m['item'] . ' ' . $m['destino'] . ' ' . $m['usuario']);
            return ($busca === '' || str_contains($texto, $busca)) && ($tipo === '' || $m['tipo'] === $tipo);
        });

        return array_slice(array_reverse($movimentos), 0, $limite);
    }

    /** @param array<string, mixed> $d @return array{codigo: string, nome: string, categoria: string, minimo: int} */
    private function validarItem(array $d): array
    {
        $item = [
            'codigo' => mb_strtoupper(trim((string) ($d['codigo'] ?? ''))),
            'nome' => trim((string) ($d['nome'] ?? '')),
            'categoria' => trim((string) ($d['categoria'] ?? '')),
            'minimo' => max(0, (int) ($d['minimo'] ?? 0)),
        ];

        if ($item['codigo'] === '' || $item['nome'] === '') {
            throw new DomainException('Código e nome são obrigatórios.');
        }

        return $item;
    }
}
