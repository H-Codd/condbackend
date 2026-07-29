# API para Condomínio

Esta API foi desenvolvida para gerenciar informações e serviços de um condomínio de forma organizada e escalável. O projeto permite controlar unidades, moradores, reservas de áreas comuns, avisos, itens achados e perdidos e boletos para pagamento.

## Funcionalidades principais

- Gestão de unidades do condomínio
- Cadastro de usuários e moradores
- Reservas de áreas comuns
- Publicação de avisos e comunicados
- Registro de itens achados e perdidos
- Controle de boletos para pagamento

## Estrutura do projeto

O projeto foi criado com o padrão MVC do Laravel:

- Models: representam as entidades do sistema, como unidades, usuários, reservas e boletos
- Controllers: recebem as requisições e organizam a lógica de negócio
- Rotas: definem os endpoints da API

## Criação de tabelas

As tabelas foram criadas por meio de migrações no arquivo [database/migrations/2026_07_26_013756_create_all_table.php](database/migrations/2026_07_26_013756_create_all_table.php), incluindo estruturas para:

- users
- units
- unitpeoples
- unitpets
- unitvehicles
- walls
- wallikes
- docs
- billets
- warnings
- foundandlost
- areas
- areadisableddays
- reservations

## Seeders

O projeto também conta com seeders para popular o banco com dados iniciais, como unidades de exemplo e áreas comuns, no arquivo [database/seeders/DatabaseSeeder.php](database/seeders/DatabaseSeeder.php).

## Ferramentas e tecnologias utilizadas

- Laravel 13
- PHP 8.3
- Sanctum para autenticação de API
- JWT Auth para tokens de acesso
- Faker para geração de dados fictícios
- Pest para testes
- Laravel Pint para formatação do código

## Como rodar o projeto

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

## Objetivo

A proposta desta API é servir como base para um sistema de condomínio com funcionalidades práticas, organizada em módulos e pronta para evoluir com novas regras de negócio.
