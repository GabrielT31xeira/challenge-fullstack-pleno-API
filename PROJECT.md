# Informações de Contato

**Nome:** Gabriel Teixeira de Carvalho  
**E-mail:** gt3ixeira@gmail.com  
**LinkedIn:**  
[GABRIEL CARVALHO – Perfil Profissional](https://www.linkedin.com/in/gabriel-teixeira-de-carvalho-a255971b7/)

# Execução do projeto

Este repositório utiliza **Docker Compose** para rodar o backend.  
A estrutura planejada previa dois repositórios (backend e frontend separados), porém, devido a um problema ao realizar o *fork* do projeto principal, o frontend acabou sendo incluído dentro da pasta **`/frontend`**, onde está o código Vue.js.

---

### 🐳 **Como rodar o backend**

1. Certifique-se de ter o **Docker** e o **Docker Compose** instalados.
2. Na raiz do projeto, execute:

```bash
docker compose up -d
```

3. Para rodar as migrations entre no container do Laravel
```bash
docker exec -it <id_do_container> /bin/bash
```

rode para criar o banco:
```bash
php artisan migrate
```

e por fim popule o banco
```bash
php artisan db:seed
```

Para a documentação usei o postman na raiz do projeto temos a pasta "/postman"
lá tem um json que pode ser importado no postman com as rotas todas documentadas.

### **Como rodar o frontend**
O frontend só usei o npm sem docker então entre na pasta **`/frontend`** e rode:
```bash
npm install
```
e depois 
```bash
npm run dev
```
o frontend já está usando o container do laravel que tá na porta :84 se você mudou a porta vá ao arquivo **`/frontend/src/api/config.ts`** e mude para a url desejada.

### **Bibliotecas usadas**
Pra falar a verdade eu usei poucas bibliotecas as unicas usadas mesmo foi o Sanctum no backend e no frontend eu usei o pinia que foi novo pra mim e eo tailwind pra css e só.

### **Estrutura do projeto**
No **backend**, segui a estrutura solicitada. A única dificuldade que encontrei foi em relação aos DTOs, pois nunca havia trabalhado com eles. Pelo que pesquisei e estudei, eles são mais utilizados em respostas maiores e mais complexas. Fiquei me perguntando qual seria a diferença prática, já que o Resource e o Request do Laravel também fazem filtragem e tratamento de dados.
Ainda assim, implementei DTOs em algumas requisições, como login, pedidos e produtos. Caso, em um projeto futuro, seja solicitado o uso de DTOs de forma mais ampla, estarei preparado para aplicá-los em todos os pontos necessários.

No **frontend**, procurei manter cada página contendo apenas os modais essenciais, evitando sobrecarga e garantindo organização. Também separei ao máximo as responsabilidades, especialmente as chamadas para o backend, a fim de manter o código mais limpo, modular e fácil de manter e com comentários para o frontend.

### **Como executar os testes**
Aqui existe uma questão importante: durante os testes iniciais, eu atingi os 80% solicitados — inclusive acredito que registrei isso em um commit feito tarde da noite comemorando isso. No entanto, ao começar a integrar o backend com o frontend e entender melhor a lógica do sistema, percebi alguns problemas nas rotas, especialmente nas relacionadas aos carrinhos.
Por exemplo, o projeto prevê que um usuário possa ter vários carrinhos, mas, ao adicionar um produto, não havia lógica clara para definir em qual carrinho o item deveria ser inserido. Por isso, implementei praticamente um CRUD completo para carrinhos, incluindo regras de estoque e outras validações necessárias.
Essa refatoração acabou “quebrando” alguns dos testes que já estavam prontos, reduzindo a cobertura para algo em torno de 50–60% no momento.

Claro que em situações normais eu teria mandado esses problemas ao lider do projeto e pedido uma orientação melhor sobre o assunto mas como é só um teste fiz do jeito que achei melhor.
para rodar os testes basta entrar no container do laravel e rodar (já tá tudo configurado no docker):
```bash
php artisan test --coverage-html coverage
```
Ah e hoje (01/12) eu commitei o html do coverage então se quiser ver melhor o que foi testado basta entrar em **`/coverage/index.html`** e entrar com o navegador que vai ter um html bonitinho lá com o que foi testado e o que não foi.

### Bonus (Rotas)

