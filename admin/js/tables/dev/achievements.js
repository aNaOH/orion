new DataTable('#table', {
    columnDefs: [
        { orderable: false, targets: [6,7,8] }
    ],
    language: {
        entries: {
            _: 'logros',
            1: 'logro'
        },
        info: 'Mostrando página _PAGE_ de _PAGES_',
        infoEmpty: 'No hay logros',
        infoFiltered: '(filtrando de _MAX_ logros)',
        lengthMenu: 'Mostrar _MENU_ logros por página',
        zeroRecords: 'No se han encontrado resultados',
        search: 'Buscar'
    }
});