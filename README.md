 
Sistema completo em PHP integrando com a API pública do Fortnite, com sincronização de dados, compra de itens, filtros avançados, histórico e gerenciamento de jogadores.

 Sobre o Projeto

Este projeto foi desenvolvido como parte de um desafio técnico, com o objetivo de consumir dados da API pública do Fortnite e criar um sistema funcional de loja virtual com:

- Exibição de cosméticos de forma paginada  
- Indicação de itens novos e à venda  
- Sistema de login e registro  
- Compra de cosméticos usando créditos  
- Histórico de compras  
- Perfil de jogadores  
- Sincronização automática com a API  
- Sistema totalmente navegável sem login  



##  Tecnologias Utilizadas

- **PHP 8+**
- **PDO (MySQL)**
- **MySQL**
- **JavaScript**
- **HTML5 / CSS3**
- **API Fortnite (https://dash.fortnite-api.com/)**
- **Git e GitHub**
- **Arquitetura modular organizada por pastas**

---

##  Como rodar o projeto localmente

1. Instale o XAMPP ou outro servidor PHP 8+  
2. Coloque os arquivos do projeto em:

C:/xampp/htdocs/fortnite_shop/



3. Acesse o phpMyAdmin e crie o banco:

fortnite_shop



4. Importe o arquivo incluído no repositório:

database.sql



5. No diretório `config/`, crie um arquivo chamado:

conexao.php



###  Arquivo de conexão (`conexao.php`)

Dentro da pasta `/config/`, crie o arquivo `conexao.php` e cole o código abaixo:

```php
<?php
$servidor = "localhost";
$usuario  = "root";
$senha    = "";
$banco    = "fortnite_shop";

$base_url = (isset($_SERVER['HTTPS']) ? "https://" : "http://")
            . $_SERVER['HTTP_HOST'];
$base_url .= "/fortnite_shop";
try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$banco;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}

 Importante:
O arquivo conexao.php não está no repositório.


✔️ Acesse o sistema:

http://localhost/fortnite_shop/
Decisões Técnicas Relevantes
PHP Puro
Optado propositalmente para manter simplicidade e foco no desafio proposto.

PDO com prepared statements
Toda comunicação com o banco usa prepared statements, garantindo segurança contra SQL Injection.

Sincronização com API externa
Os endpoints /cosmetics, /cosmetics/new e /shop são consumidos, processados e armazenados em banco para melhor performance.

Paginação e filtros no servidor
Evita carregar milhares de itens simultaneamente e garante buscas mais rápidas.

Arquitetura modular por pastas
Separação clara entre ações, visualizações, estilos e configuração.

Autenticação por sessão
Sistema simples e funcional de login, com senha criptografada usando password_hash.

Arquivo .gitignore configurado
conexao.php é ignorado por conter credenciais.

Modal personalizada para confirmação de compras
UX melhorada evitando compras acidentais.

Estrutura de Pastas

fortnite_shop/
│
├── index.php               # Página inicial (loja)
│
├── actions/                # Ações do sistema
│   ├── comprar.php
│   ├── devolver.php
│   ├── logout.php
│   ├── registrar.php
│   └── sincronizar.php
│
├── assets/                 # Arquivos estáticos
│   └── css/
│       ├── auth.css
│       ├── modal.css
│       ├── perfil.css
│       ├── style.css
│       └── usuarios.css
│
├── config/
│   ├── conexao.example.php # Modelo de conexão
│   └── conexao.php         # (Ignorado no Git)
│
├── views/                   # Telas do sistema
│   ├── detalhes.php
│   ├── header.php
│   ├── historico.php
│   ├── login.php
│   ├── meus_cosmeticos.php
│   ├── perfil.php
│   └── usuarios.php
│
├── database.sql            # Banco de dados completo
└── README.md               # Este arquivo




