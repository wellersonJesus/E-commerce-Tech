# E-commerce Tech

Plataforma Fullstack para venda de serviços tecnológicos, integrando uma Landing Page pública e um Dashboard Administrativo com autenticação e métricas. Desenvolvido com PHP nativo (API REST), SQLite e AngularJS.

<div align="center">
  <img src="assets/ecommerce.png" alt="Preview da Home" width="45%">
  <img src="assets/ecommerce-serviços.png" alt="Preview do Serviços" width="45%">
</div>
<br>
<div align="center">
  <img src="assets/ecommerce-fale.png" alt="Preview Fale" width="30%">
  <img src="assets/ecommerce-login.png" alt="Preview do Login" width="30%">
  <img src="assets/ecommerce-docs.png" alt="Preview da Documentação" width="30%">
</div>

## 🚀 Quick Start

1. **Configurar Ambiente**
   ```bash
   cp .env.example .env
   # Edite o arquivo .env com suas credenciais de Admin
   ```

2. **Banco de Dados (Reset & Seeds)**
   Para recriar o banco de dados, limpar tabelas antigas e inserir os dados de exemplo (Seeds):
   ```bash
   php backend/scripts/init_db.php
   ```
   *Nota: Execute este comando sempre que quiser resetar o banco para o estado inicial definido em `seeds.sql`.*

3. **Executar Servidor**
   ```bash
   php -S localhost:8000
   ```

## 🔗 Acesso

----
| Área | URL | Credenciais |
|---|---|---|
| **Landing Page** | http://localhost:8000/frontend/public/ | - |
| **Admin** | http://localhost:8000/frontend/admin/login.html | Admin (`.env`) ou `user@example.com` / `123456` |
| **API Docs** | http://localhost:8000/backend/docs/ | - |

## 📡 Endpoints da API

Principais rotas utilizadas pelos controllers e disponíveis para consumo:

| Método | Endpoint | Descrição |
|---|---|---|
| **POST** | `/backend/api/auth/login.php` | Autenticação (Login) |
| **GET** | `/backend/api/auth/logout.php` | Encerrar sessão (Logout) |
| **GET** | `/backend/api/auth/me.php` | Dados do usuário logado |
| **GET** | `/backend/api/dashboard/metrics.php` | Métricas do Dashboard |
| **GET** | `/backend/api/services/services.php` | Listar serviços (Admin) ou `?public=1` |
| **POST/PUT/DELETE** | `/backend/api/services/services.php` | Gerenciar serviços (Admin) |

---

<div align="center">
© JesusWellerson | Development | SRE | Software Architect | Software Engineer
<br>
</div>
