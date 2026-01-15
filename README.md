# LandPage — Projeto scaffold

Pequeno scaffold com uma landing pública e painel administrativo que usa PHP + SQLite.

**Requisitos**

- PHP 8+ com suporte a PDO/SQLite
- `curl` (para testes) ou um navegador

**Arquivos importantes**

- Especificação API: [backend/docs/swagger.yaml](backend/docs/swagger.yaml)
- Landing pública: [frontend/public/index.html](frontend/public/index.html)
- Painel admin: [frontend/admin/login.html](frontend/admin/login.html) e [frontend/admin/dashboard.html](frontend/admin/dashboard.html)

## Setup (rápido)

1. Copie o exemplo de ambiente e edite valores sensíveis localmente:

```bash
cp .env.example .env
# Edite .env para definir 
ADMIN_EMAIL=change@change.com
ADMIN_PASSWORD=change_this_password
JWT_SECRET=change_this_secret
```

2. Criar banco SQLite e aplicar schema + seeds (via PHP se `sqlite3` não estiver instalado):

```bash
# usando sqlite3 (se disponível):
sqlite3 backend/database/landpage.sqlite < backend/database/schema.sql
sqlite3 backend/database/landpage.sqlite < backend/database/seeds.sql

# ou com PHP/PDO (sem instalar sqlite3):
php -r ' $db=new PDO("sqlite:backend/database/landpage.sqlite"); $db->exec(file_get_contents("backend/database/schema.sql")); $db->exec(file_get_contents("backend/database/seeds.sql")); echo "DB_CREATED\n"; '
```

3. Iniciar o servidor de desenvolvimento (recomendado: servir a pasta `public` como docroot):

```bash
php -S localhost:8000 -t public
```

Se estiver rodando na raiz do repositório, há um `index.html` que redireciona para `/public/index.html`.

## URLs úteis

- Landing Page: http://localhost:8000/frontend/public/index.html
- Admin Login: http://localhost:8000/frontend/admin/login.html
- API Docs (ReDoc): http://localhost:8000/backend/docs/index.html

## Credenciais seeds

- Usuário admin: Configurado via `.env` (`ADMIN_EMAIL` / `ADMIN_PASSWORD`).
- Usuário comum (seed): `user@example.com` / `123456` (acesso restrito).

## Como funciona o login/logout

- `POST /backend/api/auth/login.php` (JSON): {"email":"...","password":"..."} → cria sessão PHP (cookie `PHPSESSID`).
- `GET /backend/api/auth/logout.php` → destrói sessão e limpa cookie.

## Segurança e boas práticas

- Nunca comite `.env` ou chaves reais. Use `.env.example` para documentar variáveis.
- Troque imediatamente `ADMIN_PASSWORD` e `JWT_SECRET` no ambiente de produção.
- Adicione validação, proteção CSRF e uso de HTTPS antes de colocar em produção.

## Documentação OpenAPI

O arquivo `backend/docs/swagger.yaml` descreve os endpoints disponíveis. Abra backend/docs/index.html no navegador para uma UI ReDoc.

## Testes rápidos

Recomenda-se testar:

```bash
# listar serviços públicos
curl http://localhost:8000/backend/api/services/services.php?public=1

# login via API
curl -c cookies.txt -H "Content-Type: application/json" -d '{"email":"admin@admin.com","password":"123456"}' http://localhost:8000/backend/api/auth/login.php

# chamar endpoint protegido usando o cookie salvo
curl -b cookies.txt http://localhost:8000/backend/api/dashboard/metrics.php
```

---

Se quiser que eu inicialize um repositório Git local, crie um repositório no GitHub e faça o push automático daqui, diga-me e eu realizo os passos necessários (preciso de sua confirmação para criar o repo remoto ou das credenciais/token se preferir que eu faça isso). 

**Configuração de ambiente**

- **Criar `.env`**: copie o arquivo de exemplo e preencha os valores sensíveis localmente (não commite `.env`):

```bash
cp .env.example .env
# então edite .env e preencha senhas e chaves
```

- **Chaves e senhas**: use valores fortes para `JWT_SECRET`, `ADMIN_PASSWORD` e outras credenciais. Nunca comite valores reais no repositório.

- **Banco de dados SQLite**: o padrão usa `backend/database/landpage.sqlite`. Se desejar caminhos absolutos, ajuste as variáveis correspondentes em `.env`.
