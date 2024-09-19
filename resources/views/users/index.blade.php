@extends('layouts.app')

@section('title', 'Users')

@section('content')
    <div class="section mt-2">
        <div class="section-title d-flex justify-content-between align-items-baseline">
            <span>Clientes</span>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#DialogFormUser">
                <ion-icon name="person-add-outline"></ion-icon> Novo
            </button>
        </div>
        <div class="card">

            <div class="table-responsive p-2">
                @if(session('msg'))
                    <div class="alert alert-primary alert-dismissible fade show mb-2" role="alert">
                        {{ session('msg') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                {{ $users->links() }}
                <table class="table table-sm table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th scope="row">ID</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            @can('manager')
                                <th>Empresa</th>
                            @endcan
                            @if (Gate::any(['manager', 'user']))
                                <th>Ações</th>
                            @endif


                        </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="text-end">{{ $user->id }}</td>
                            <td class="text-start">{{ $user->name }}</td>
                            <td class="text-start">{{ $user->email }}</td>
                            <td class="text-start">{{ optional($user->client_data)->phone ?? "Não informado" }}</td>
                            @can('manager')
                                <td class="text-start text-primary">
                                    {{ $user->entity->name }}
                                </td>
                            @endcan
                            @if (Gate::any(['manager', 'user']))
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary btn-icon" data-bs-toggle="modal" data-bs-target="#DialogFormUser" onclick="editUser({{ $user->toJson() }})">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </button>
                                    @can('manager')
                                        <a href="{{ route('users.destroy', $user->id) }}" class="btn btn-sm btn-danger btn-icon">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </a>
                                    @endcan
                                </td>
                            @endif


                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="modal fade dialogbox" id="DialogFormUser" data-bs-backdrop="static" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cliente</h5>
                </div>
                <form method="POST" id="user" action="{{ route('users.store') }}">
                    @csrf

                    <div class="modal-body text-start mb-2">

                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="level">Nível</label>
                                <select class="form-control custom-select" name="level" id="level" required>
                                    <option value="client">Cliente</option>
                                    <option value="user">Usuário</option>
                                    @can('manager')
                                        <option value="manager">Administrador</option>
                                    @endcan
                                </select>
                            </div>
                            <div class="input-info">Selecione um nivel de permissão</div>
                        </div>

                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="name">Nome</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                                <i class="clear-input">
                                    <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <i class="clear-input">
                                    <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="phone">Telefone (Opcional)</label>
                                <input type="tel" class="form-control" id="phone" name="phone" minlength="15" maxlength="15">
                                <i class="clear-input">
                                    <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="password">Senha</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <i class="clear-input">
                                    <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="btn-inline">
                            <button type="button" id="close-modal" onclick="window.location.reload()" class="btn btn-text-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" id="btn-action" class="btn btn-text-primary">Adicionar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editUser(data) {
            console.log(data);

            for (let [key, value] of Object.entries(data)) {
                const input = $(`#${key}`);
                if(input){
                    input.val(value).trigger('change');
                }
            }

            $('form#user').prop('action', `users/${ data.id }`).append(`@method('PUT')`)
            $("input#password").prop('required', false);
            $('#btn-action').html('Salvar')
        }


        let table = new DataTable('.table', configDataTable);
        $("#phone").mask("(00) 00000-0000")

        $("input#password").parent().parent().hide().prop('required', false);
        $('#level').on('change', function (){

            switch (this.value){
                case 'client':
                    $("input#password").parent().parent().hide(500).prop('required', false);
                    break;
                @can('manager')
                    case 'manager':
                @endcan
                case 'user':
                    $("input#password").parent().parent().show(500).prop('required', true);
                    break;
            }

        })
    </script>
@endsection
