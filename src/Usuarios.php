<?php

declare(strict_types=1);

final class Usuarios
{
    public const PERFIS = ['admin' => 'Administrador', 'tecnico' => 'Técnico'];

    public function __construct(private Armazem $armazem)
    {
    }

    /** @return list<array<string, mixed>> */
    public function todos(): array
    {
        $usuarios = $this->armazem->ler('usuarios');
        usort($usuarios, fn ($a, $b) => strcmp($a['login'], $b['login']));

        return array_map(fn ($u) => array_diff_key($u, ['senha' => 1]), $usuarios);
    }

    /** @return array{login: string, nome: string, perfil: string}|null */
    public function autenticar(string $login, string $senha): ?array
    {
        foreach ($this->armazem->ler('usuarios') as $u) {
            if ($u['login'] === mb_strtolower(trim($login)) && password_verify($senha, $u['senha'])) {
                return ['login' => $u['login'], 'nome' => $u['nome'], 'perfil' => $u['perfil']];
            }
        }

        return null;
    }

    public function criar(string $login, string $nome, string $perfil, string $senha): void
    {
        $login = mb_strtolower(trim($login));
        if (!preg_match('/^[a-z0-9._-]{3,30}$/', $login)) {
            throw new DomainException('Login deve ter de 3 a 30 caracteres: letras, números, ponto, hífen ou sublinhado.');
        }
        if (!isset(self::PERFIS[$perfil])) {
            throw new DomainException('Perfil inválido.');
        }
        $this->validarSenha($senha);

        $this->armazem->alterar('usuarios', function (array $usuarios) use ($login, $nome, $perfil, $senha) {
            if (in_array($login, array_column($usuarios, 'login'), true)) {
                throw new DomainException("O login $login já existe.");
            }
            $usuarios[] = [
                'login' => $login,
                'nome' => trim($nome) ?: $login,
                'perfil' => $perfil,
                'senha' => password_hash($senha, PASSWORD_DEFAULT),
            ];
            return $usuarios;
        });
    }

    public function alterarSenha(string $login, string $senha): void
    {
        $this->validarSenha($senha);
        $this->atualizar($login, ['senha' => password_hash($senha, PASSWORD_DEFAULT)]);
    }

    public function alterarPerfil(string $login, string $perfil): void
    {
        if (!isset(self::PERFIS[$perfil])) {
            throw new DomainException('Perfil inválido.');
        }
        if ($perfil !== 'admin' && $this->ultimoAdmin($login)) {
            throw new DomainException('É preciso manter pelo menos um administrador.');
        }
        $this->atualizar($login, ['perfil' => $perfil]);
    }

    public function excluir(string $login): void
    {
        if ($this->ultimoAdmin($login)) {
            throw new DomainException('É preciso manter pelo menos um administrador.');
        }
        $this->armazem->alterar('usuarios', fn ($us) => array_filter($us, fn ($u) => $u['login'] !== $login));
    }

    public function vazio(): bool
    {
        return $this->armazem->ler('usuarios') === [];
    }

    private function ultimoAdmin(string $login): bool
    {
        $admins = array_column(array_filter($this->armazem->ler('usuarios'), fn ($u) => $u['perfil'] === 'admin'), 'login');

        return $admins === [$login];
    }

    /** @param array<string, string> $campos */
    private function atualizar(string $login, array $campos): void
    {
        $this->armazem->alterar('usuarios', function (array $usuarios) use ($login, $campos) {
            foreach ($usuarios as &$u) {
                if ($u['login'] === $login) {
                    $u = array_merge($u, $campos);
                    return $usuarios;
                }
            }
            throw new DomainException('Usuário não encontrado.');
        });
    }

    private function validarSenha(string $senha): void
    {
        if (mb_strlen($senha) < 8) {
            throw new DomainException('A senha precisa ter pelo menos 8 caracteres.');
        }
    }
}
