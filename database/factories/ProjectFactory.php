<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $titulos = [
            'Sistema de Gestão de Vendas',
            'Aplicativo de Controle Financeiro Pessoal',
            'Plataforma de Ensino a Distância',
            'Portal de Notícias Regionais',
            'Sistema de Agendamento Online para Clínicas',
            'Dashboard de Monitoramento Logístico',
            'Site Institucional para Escritório de Contabilidade',
            'Sistema de RH com Controle de Ponto',
            'E-commerce de Moda com Integração a Marketplaces',
            'App de Delivery para Restaurantes Locais',
            'Sistema de Controle de Estoque com Scanner',
            'Plataforma de Freelancers e Contratação Remota',
            'Aplicativo de Saúde e Bem-estar com Monitoramento',
            'Portal de Imóveis com Filtros Avançados',
            'Sistema de Gestão de Projetos e Kanban',
            'App de Reservas de Hotéis e Pousadas',
            'Sistema de Atendimento ao Cliente com Chatbot',
        ];

        return [
            'title' => $this->faker->randomElement($titulos),
            'description' => $this->generateProjectDescription(),
            'budget' => $this->faker->numberBetween(3000, 50000),
            'deadline' => $this->faker->dateTimeBetween('+10 days', '+6 months'),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'completed']),
        ];
    }

    private function generateProjectDescription(): string
    {
        $descricoes = [
            <<<DESC
Objetivo: Criar um sistema web para controle de vendas, com cadastro de clientes, produtos, estoque e geração de relatórios detalhados.

Escopo: Desenvolvimento completo com painel administrativo, controle de permissões, dashboard com gráficos de performance e relatórios mensais.

Competências esperadas: Experiência em Laravel, Livewire, MySQL, controle de versionamento e boas práticas de segurança.

Tempo estimado: 2 a 3 meses.

Resultados esperados: Melhor organização comercial, otimização das vendas e aumento da produtividade da equipe.
DESC,

            <<<DESC
Objetivo: Desenvolver um aplicativo mobile para gestão financeira pessoal, com monitoramento de gastos, metas de economia e integração com contas bancárias.

Escopo: App com autenticação, dashboards interativos, notificações push e integração via API Open Finance.

Competências esperadas: Flutter ou React Native, Firebase, integração com APIs financeiras, UX/UI design mobile.

Tempo estimado: 1 a 2 meses.

Resultados esperados: Usuários mais conscientes financeiramente e controle efetivo das finanças pessoais.
DESC,

            <<<DESC
Objetivo: Criar uma plataforma de cursos online, com áreas para alunos e instrutores, fórum, quizzes e emissão de certificados digitais.

Escopo: Desenvolvimento completo com upload de vídeos, questionários interativos, painel de administração e relatórios de desempenho.

Competências esperadas: Laravel, Vue.js ou React, PostgreSQL, AWS S3, experiência em e-learning.

Tempo estimado: 3 a 4 meses.

Resultados esperados: Maior alcance da instituição de ensino e melhor experiência de aprendizado online.
DESC,

            <<<DESC
Objetivo: Construir um portal de notícias locais com categorização, busca avançada, comentários e integração com redes sociais.

Escopo: Painel de administração para jornalistas, métricas de leitura em tempo real, sistema de notificação e SEO otimizado.

Competências esperadas: Laravel, Tailwind CSS, Redis, otimização de performance e boas práticas de SEO.

Tempo estimado: 2 meses.

Resultados esperados: Engajamento da comunidade local e maior visibilidade para conteúdos regionais.
DESC,

            <<<DESC
Objetivo: Implementar um sistema de agendamento online para clínicas, com confirmação por e-mail, lembretes via WhatsApp e gestão de horários.

Escopo: Cadastro de serviços e profissionais, agendamento automático, cancelamentos e dashboard do administrador com métricas de atendimento.

Competências esperadas: Laravel, Vue.js, Twilio API, integração com serviços de email e WhatsApp.

Tempo estimado: 1 a 2 meses.

Resultados esperados: Redução de faltas em consultas, organização eficiente da agenda e melhor experiência para pacientes.
DESC,

            <<<DESC
Objetivo: Criar um sistema de gestão de projetos com Kanban, controle de tarefas, colaboradores e relatórios de progresso.

Escopo: Desenvolvimento de dashboards para equipes, notificações de prazos, filtros por status e gráficos de desempenho.

Competências esperadas: Laravel, Vue.js, Tailwind, experiência em metodologias ágeis (Scrum/Kanban).

Tempo estimado: 2 a 3 meses.

Resultados esperados: Maior controle sobre tarefas e projetos, melhoria na comunicação da equipe e produtividade.
DESC,
        ];

        return $this->faker->randomElement($descricoes);
    }
}
