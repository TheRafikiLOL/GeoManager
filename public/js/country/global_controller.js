$(document).ready(function() {

    let $table = $('#countriesTable');

    $('#syncCountries').on('click', function(e) {
        e.preventDefault();
        
        loadPageModal("Sincronizar paises", "¿Está seguro de que desea sincronizar los datos de países desde la API?", "Cancelar", "secondary", "Sincronizar", "info", function() {
            $('#loader').removeClass('d-none');

            $.ajax({
                url: 'countries/api/all',
                type: 'GET',
                success: function(response) {
                    $('#loader').addClass('d-none');

                    if (response.success) {
                        $table.DataTable().ajax.reload();

                        loadToast("Sincronización Completa", response.message);
                    } else {
                        loadToast("ERROR", response.message);
                    }
                },
                error: function(xhr, status, error) {
                    $('#loader').addClass('d-none');
                    loadToast("ERROR", "Error en la llamada a la API:", error);
                }
            });
        });

        $('#pageModal').modal('hide');
    });

});
