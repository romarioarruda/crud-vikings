<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização de dados</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" 
    integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" 
    crossorigin="anonymous">


</head>
<body>
    <main id="app" class="container">
        <h2 class="mb-5 mt-3 text-center">{{ titulo }}</h2>
        <div id="formulario">
            <div class="modal-dialog">
                <?php if(isset($_GET['img']) && $_GET['img'] == 'empty'): ?>
                    <div class="alert alert-danger">Imagem de perfil não preenchida.</div>
                <?php endif; ?>
                <div class="modal-content">
                    <div class="modal-header" style="display: flex;justify-content: flex-end;">
                        <div class="col-md-5">
                            <img v-if="avatar" :src="avatar" class="card-img" alt="avatar">
                            <form method="POST" :action="urlActionAvatar" enctype="multipart/form-data">
                                <input type="file" name="avatar" title="Atualizar foto" class="form-control" style="cursor:pointer">
                                <button type="submit" class="btn btn-info btn-block mt-2">Salvar</button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group row">
                                <label for="nome" class="col-sm-2 col-form-label">Nome</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="nome" :value="nome"
                                    placeholder="Digite seu nome" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control email" id="email" :value="email"
                                    placeholder="Digite seu Email" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="contato" class="col-sm-2 col-form-label">Telefone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control telefone"
                                        :value="telefone"
                                        id="telefone" placeholder="Para mais de um número, separe por vírgula." required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer" style="justify-content: space-between;">
                        <a href="/" class="btn btn-danger">
                            Voltar
                        </a>
                        <button type="button" class="btn btn-primary" @click="atualizarFuncionario()">Atualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Usando jquery só por causa do bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" 
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" 
    crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" 
    integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" 
    crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        // const baseUrl = ''
        var app = new Vue({
            el: '#app',
            data: {
                titulo: 'Informações do Funcionário',
                nome: '',
                email: '',
                telefone: '',
                id: 0,
                avatar: '/src/assets/funcionario_avatar/',
                urlActionAvatar: '/funcionario/upload/'
            },


            mounted() {
                this.getFuncionario()
            },

            methods: {
                getFuncionario() {
                    let regex = /\/(\d+)/
                    let url = window.location.href

                    let result = url.match(regex)

                    axios.get(`/funcionario/${result[1]}`)
                    .then((resp) => {
                        this.id = resp.data.funcionario.id_registro || 0
                        this.nome = resp.data.funcionario.nome || ''
                        this.email = resp.data.funcionario.email || ''
                        this.telefone = resp.data.funcionario.telefone || ''
                        this.avatar += resp.data.funcionario.avatar || '0.png'
                        this.urlActionAvatar += resp.data.funcionario.id_registro
                    })

                },

                atualizarFuncionario() {
                    let arrayTelefone = []
                    let nome = document.getElementById('nome').value
                    let email = document.getElementById('email').value
                    let telefone = document.getElementById('telefone').value

                    arrayTelefone.push(telefone.split(','))

                    let verificaDados = nome && email && telefone

                    if(verificaDados) {
                        axios.post(`/funcionario/${this.id}`, {
                            nome,
                            email,
                            telefone: arrayTelefone[0]
                        })
                        .then((resp) => {
                            window.location.href = window.location.href
                        })
                    }
                }
            }
        })
    </script>
</body>
</html>