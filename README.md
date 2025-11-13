# Fortnite Shop – Desafio Técnico  
Sistema PHP com integração real com API externa, compra de itens, filtros avançados, histórico e perfil de usuários.



## Sobre o Projeto

Este sistema foi desenvolvido como parte de um desafio técnico.  
A proposta consiste em consumir a API pública do **Fortnite**, exibir seus cosméticos e permitir que usuários possam:

- Ver itens da loja  
- Comprar cosméticos usando créditos 
- Consultar histórico de compras  
- Ver detalhes dos itens  
- Ver jogadores e perfis  
- Filtrar itens por vários critérios  
- Ver itens novos e os que estão na loja real  
- Sincronizar dados com a API externa  
- Acessar a loja mesmo sem login  



## Tecnologias Utilizadas

- **PHP 8+**
- **PDO (Acesso seguro ao MySQL)**
- **MySQL**
- **HTML5 / CSS3**
- **JavaScript**
- **API Fortnite (https://dash.fortnite-api.com/)**
- **AJAX para sincronização**
- **Arquitetura organizada em pastas**


##  Estrutura de Pastas

fortinet_shop/
│
├── index.php              
│
├── actions/               
│   ├── devolver.php
│   ├── logout.php
│   ├── registrar.php
│   └── sincronizar.php
│
├── assets/             
│   └── css/
│       ├── auth.css      
│       ├── modal.css     
│       ├── perfil.css     
│       ├── style.css      
│       └── usuarios.css    
│
├── config/
│   └── conexao.php        
│
├── views/                 
│   ├── detalhes.php
│   ├── header.php
│   ├── historico.php
│   ├── login.php
│   ├── meus_cosmeticos.php
│   ├── perfil.php
│   └── usuarios.php
│
└── README.md              


