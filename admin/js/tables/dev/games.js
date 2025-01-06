new DataTable('#table', {
    columnDefs: [
        { className: 'text-left-important', targets: [2,3] },
        { orderable: false, targets: [7,8,9] }
    ],
    language: {
        entries: {
            _: 'juegos',
            1: 'juego'
        },
        info: 'Mostrando página _PAGE_ de _PAGES_',
        infoEmpty: 'No hay juegos',
        infoFiltered: '(filtrando de _MAX_ juegos)',
        lengthMenu: 'Mostrar _MENU_ juegos por página',
        zeroRecords: 'No se han encontrado resultados',
        search: 'Buscar'
    }
});