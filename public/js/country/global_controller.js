$(document).ready(function() {

    console.log("dsa")

    // Cargar el modal del index para sincronizar todos los paises
    $('#syncCountries').on('click', function(e) {
        e.preventDefault();
        
        loadPageModal("Sincronizar paises", "¿Está seguro de que desea sincronizar los datos de países desde la API?", "Cancelar", "secondary", "Sincronizar", "info", function() {
            $.ajax({
                url: 'countries/api/all',
                type: 'GET',
                success: function(response) {

                    /*if (response.success) {
                        alert(response.message);
                         location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }*/

                },
                error: function(xhr, status, error) {
                    console.error("Error en la llamada a la API:", error);
                }
            });
        });
        $('#pageModal').modal('hide');
    });

});