new DataTable('#table', {
    columnDefs: [
        { orderable: false, targets: [3,4,5] } // Deshabilita el orden en la primera columna (índice 0)
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