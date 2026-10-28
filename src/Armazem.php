<?php

declare(strict_types=1);

final class Armazem
{
    public function __construct(private string $pasta)
    {
        if (!is_dir($pasta)) {
            mkdir($pasta, 0775, true);
        }
    }

    /** @return list<array<string, mixed>> */
    public function ler(string $colecao): array
    {
        $arquivo = $this->caminho($colecao);
        if (!is_file($arquivo)) {
            return [];
        }

        $h = fopen($arquivo, 'r');
        flock($h, LOCK_SH);
        $conteudo = stream_get_contents($h);
        flock($h, LOCK_UN);
        fclose($h);

        $dados = json_decode((string) $conteudo, true);

        return is_array($dados) ? $dados : [];
    }

    /** @param callable(list<array<string, mixed>>): list<array<string, mixed>> $alteracao */
    public function alterar(string $colecao, callable $alteracao): void
    {
        $h = fopen($this->caminho($colecao), 'c+');
        flock($h, LOCK_EX);

        try {
            $dados = json_decode((string) stream_get_contents($h), true);
            $novos = $alteracao(is_array($dados) ? $dados : []);

            ftruncate($h, 0);
            rewind($h);
            fwrite($h, json_encode(array_values($novos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            fflush($h);
        } finally {
            flock($h, LOCK_UN);
            fclose($h);
        }
    }

    private function caminho(string $colecao): string
    {
        return $this->pasta . '/' . preg_replace('/[^a-z_]/', '', $colecao) . '.json';
    }
}
