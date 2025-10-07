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
