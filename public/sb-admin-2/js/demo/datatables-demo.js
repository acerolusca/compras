$(document).ready(function() {

    $('#usersTable').DataTable({
        "language": {
            "decimal": "",
            "emptyTable": "Nenhum usuário foi encontrado.",
            "info": "",  // Desativa a informação sobre os registros
            "infoEmpty": "",  // Desativa a informação quando não há registros
            "infoFiltered": "",  // Desativa a informação de filtragem
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "",  // Desativa o seletor de quantidade de registros por página
            "loadingRecords": "Carregando...",
            "processing": "Processando...",
            "search": "Pesquisar:",
            "zeroRecords": "Nenhum registro correspondente encontrado",
            "paginate": {
                "first": "Primeiro",
                "last": "Último",
                "next": ">",
                "previous": "<"
            },
            "aria": {
                "sortAscending": ": ativar para classificar coluna ascendente",
                "sortDescending": ": ativar para classificar coluna descendente"
            }
        },
        "paging": false,  // Habilita paginação
        "searching": true,  // Habilita a barra de pesquisa
        "info": false,  // Desativa a exibição de informações sobre os registros
        "lengthChange": false, // Desativa o seletor de quantidade de registros por página
        "columnDefs": [
            { "orderable": false, "targets": 0 }, // Desativa a ordenação para primeira coluna
            { "orderable": false, "targets": 3 }, // Desativa a ordenação para quarta coluna
            { "orderable": false, "targets": 4 }, // Desativa a ordenação para quinta coluna
        ],
        "order": [] // Define que não há coluna de ordenação por padrão
    });


});