# api-projeto-restaurante

## REST API com os endpoints:

### Login
```    
`GET /auth/login`: faz o login do usuário

```

### Logout
```    
`POST /auth/logout`: faz o logout do usuário

```

### Registro de usuário
```    
`POST /registrar`: registra um novo usuário

```

### Restaurantes
```    
`GET /restaurantes`: return todos os restaurantes com os cardápios associados e os produtos associados aos cardápios

`POST /restaurantes`: registra um restaurante

`PUT /restaurantes/{restaurante}`: atualiza um restaurante

`DELETE /restaurantes/{restaurante}`: deleta um restaurante

`GET /restaurantes/{restaurante}`: retorna um registro de restaurante

```
### Cardápios
```    
`GET /cardapios`: return todos os cardápios e os produtos associados aos cardápios

`POST /cardapios`: registra um cardápio

`PUT /cardapios/{cardapio}`: atualiza um cardápio

`DELETE /cardapios/{cardapio}`: deleta um cardápio

`GET /cardapios/{cardapio}`: retorna um registro de cardápio

```
### Produtos
```    
`GET /produtos`: return todos os produtos

`POST /produtos`: registra um produto

`PUT /produtos/{produto}`: atualiza um produto

`DELETE /produtos/{produto}`: deleta um produto

`GET /produtos/{produto}`: retorna um registro de produto

```

## Instalação do ambiente
```
docker-compose up --build -d
```

```
composer install (dentro do docker)
```

## Configuração do ambiente
#### Geração da APP_KEY
```
php artisan key:generate (dentro do docker)
```
#### Geração da JWT_SECRET
```
php artisan jwt:secret (dentro do docker)
```
#### Migração das tabelas
```
php artisan migrate (dentro do docker)
```
## Copie o arquivo ".env.example", cole na raiz do projeto e renomeie para ".env"


## Acesse a API em:
```
http://localhost:8081/api/
```
OU

```
http://host.docker.internal:8081/api/
```
