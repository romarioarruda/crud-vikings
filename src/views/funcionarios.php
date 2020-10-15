<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" 
    integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" 
    crossorigin="anonymous">


</head>
<body>

    <main id="app" class="container">
        <h2 class="mb-5 mt-3">{{ message }}</h2>
        <button type="button" class="btn btn-primary btn-sm mb-3" data-toggle="modal" data-target="#modalAdd">
            Adicionar novo funcionário
        </button>
        <table class="table" v-if="arrayFuncionarios.length">
            <thead class="thead-dark">
                <tr>
                <th scope="col">#</th>
                <th scope="col">Nome</th>
                <th scope="col">Email</th>
                <th scope="col">Telefone</th>
                <th scope="col">Ação</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="funcioario in arrayFuncionarios" :key="funcioario.id_registro">
                    <th scope="row">{{funcioario.id_registro}}</th>
                    <td>{{funcioario.nome}}</td>
                    <td>{{funcioario.email}}</td>
                    <td v-if="funcioario.telefone">
                        <a href="#" @click="addTelefoneModal(funcioario.telefone)" data-toggle="modal" data-target="#modalTelefone">Visualizar telefones</a>
                    </td>
                    <td v-else>Sem telefone cadastrado</td>
                    <td>
                        <button class="btn btn-danger btn-sm" @click="deletar(funcioario.id_registro)">Deletar</button>
                        <button class="btn btn-info btn-sm" @click="linkEditar(funcioario.id_registro)">Editar</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-else class="alert alert-danger">Sem funcionários cadastrados</div>


        <!-- Modal telefones -->
        <div class="modal" id="modalTelefone" tabindex="-1">
            <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Telefones cadastrados</h5>
                            <button type="button" class="close" 
                                data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group" v-for="telefone in telefoneModal" :key="telefone">
                                <li class="list-group-item">{{ telefone }}</li>
                            </ul>
                        </div>
                    </div>
            </div>
        </div>
        <!-- Fim modal telefone -->

        <!-- Modal add funcionario -->
        <div class="modal" id="modalAdd" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Área de Cadastro</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group row">
                                <label for="nome" class="col-sm-2 col-form-label">Nome</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="nome"
                                    placeholder="Digite seu nome" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control email" id="email"
                                    placeholder="Digite seu Email" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="contato" class="col-sm-2 col-form-label">Telefone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control telefone"
                                        id="telefone" placeholder="Para mais de um número, separe por vírgula." required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" @click="salvarFuncionario()">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIm do modal -->
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
                message: 'Lista dos funcionários',
                arrayFuncionarios: [],
                telefoneModal: []
            },


            mounted() {
                this.getFuncionarios()   
            },

            methods: {
                getFuncionarios() {
                    axios.get('/funcionarios')
                    .then((resp) => {
                        this.arrayFuncionarios = resp.data.funcionarios
                    })
                },

                salvarFuncionario() {
                    let arrayTelefone = []
                    let nome = document.getElementById('nome').value
                    let email = document.getElementById('email').value
                    let telefone = document.getElementById('telefone').value

                    arrayTelefone.push(telefone.split(','))

                    let verificaDados = nome && email && telefone

                    if(verificaDados) {
                        axios.post('/novo-funcionario', {
                            nome,
                            email,
                            telefone: arrayTelefone[0]
                        })
                        .then((resp) => {
                            window.location.href = window.location.href
                        })
                    }
                },

                deletar(idFuncionario) {
                    axios.delete(`/funcionario/${idFuncionario}`)
                    .then((resp) => {
                        window.location.href = window.location.href
                    })
                },


                linkEditar(url) {
                    window.location.href = `funcionario/editar/${url}`
                },

                addTelefoneModal(arrayTelefone) {
                    this.limpaTelefoneModal()
                    this.telefoneModal = arrayTelefone
                },

                limpaTelefoneModal() {
                    this.telefoneModal = []
                }
            }
        })
    </script>
</body>
</html>