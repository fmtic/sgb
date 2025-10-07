Sistema de Gestão de Biblioteca (SGB)
🛠️ Visão Geral

O Sistema de Gestão de Biblioteca (SGB) é uma aplicação completa em PHP com PostgreSQL, focada na gestão de acervo, usuários, empréstimos e devoluções. Ideal para bibliotecas que buscam automação e relatórios avançados.

Nota: Este sistema foi desenvolvido com o auxílio do ChatGPT (GPT-5 mini), que contribuiu na estruturação do projeto, sugestões de código e elaboração deste README.

📁 Estrutura do Repositório

assets/: Arquivos estáticos (CSS, JS, imagens).

config/: Arquivos de configuração do sistema e banco de dados.

controller/: Lógica de controle e operações do sistema.

model/: Modelos e interações com PostgreSQL.

view/: Interfaces de usuário (front-end).

Documentacao_SGB.md: Documentação técnica detalhada do sistema.

⚙️ Ambiente e Requisitos

Servidor Web: Apache (Linux).

PHP: 8.x recomendado.

Banco de Dados: PostgreSQL 12 ou superior.

Extensões PHP: PDO, pgsql, mbstring, json, cURL.

O Apache deve ter mod_rewrite habilitado para URLs amigáveis.

🚀 Configuração do Apache na Porta 80

Para rodar o sistema no Apache na porta 80, siga estas etapas:

Abra o arquivo de configuração do seu site no Apache (Ubuntu/Debian):

sudo nano /etc/apache2/sites-available/000-default.conf


Configure o DocumentRoot e o diretório do sistema:

<VirtualHost *:80>
    DocumentRoot /caminho/para/sgb
    <Directory /caminho/para/sgb>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>


Habilite o módulo rewrite:

sudo a2enmod rewrite


Reinicie o Apache:

sudo systemctl restart apache2


Agora o sistema estará acessível via http://localhost na porta 80.

🚀 Como Rodar o Sistema

Clone o repositório:

git clone https://github.com/fmtic/sgb.git
cd sgb


Configure o PostgreSQL com as credenciais em config/database.php.

Acesse pelo navegador:

http://localhost

📄 Licença

Licenciado sob a MIT License
.
