$(document).ready(function () {
    $('#nombre, #apellido').prop("disabled", true);
    $("#tipo_nacionalidad_id, #documento_identidad").change(buscarPersona);
    $('.salvar-persona').click(crearPersona);
    $('.siguiente').click(validarPersonas);
});

function validarPersonas(evt)
{
    if ($('#div-jugador').find('#jugador_id').val() == "") {
        mostrarError("Debe guardar los datos del jugador");
        return false;
    }
    if ($('#div-representante').find('#representante_id').val() == "") {
        mostrarError("Debe guardar los datos del representante");
        return false;
    }
}


function buscarPersona(evt)
{
    var parent = $(evt.target).closest('.row').parent();
    var variables = parent.find('input, select').serialize();
    if (parent.find('#tipo_nacionalidad_id').val() == "" || parent.find('#documento_identidad').val() == "") {
        return;
    }
    $.ajax({
        url: baseUrl + "personas/buscar",
        data: variables,
        dataType: 'json',
        method: "GET",
        success: function (data)
        {
            if (data.persona.id != undefined) {
                parent.find('#jugador_id, #representante_id').val(data.persona.id);
                parent.find('#nombre').first().val(data.persona.nombre);
                parent.find('#apellido').val(data.persona.apellido);
                parent.find('#nombre, #apellido').prop("disabled", true);
                parent.find('.salvar-persona').hide();
            } else {
                parent.find('#jugador_id, #representante_id').val("");
                parent.find('#nombre, #apellido').prop("disabled", false);
                parent.find('#nombre, #apellido').val("");
                parent.find('.salvar-persona').show();
            }
            if (parent.attr('id') == 'div-representante') {
                $('#lista-relacionados').html(data.vistaFamiliares);
            }
        }
    });
}

function crearPersona(evt)
{
    var parent = $(evt.target).closest('.row').parent();
    var variables = parent.find('input, select').serialize();
    parent.find('input, textarea, select, checkbox, radio').parent().removeClass("has-error");
    parent.find('.help-block').remove();

    $.ajax({
        url: baseUrl + "personas/crear",
        data: variables,
        dataType: 'json',
        method: "POST",
        formulario: parent,
        success: function (data)
        {
            mostrarMensaje(data.mensaje);
            parent.find('#jugador_id, #representante_id').val(data.persona.id);
            if (parent.attr('id') == 'div-representante') {
                $.get(baseUrl + "personas/familiaressolicitante/" + data.persona.id, function (data) {
                    $('#lista-relacionados').html(data);
                });
            }
        },
        error: function (data)
        {
            var formulario = this.formulario;
            if (data.status == 400) {
                mostrarError(procesarErrores(data.responseJSON.errores));
                $.each(data.responseJSON.errores, function (key, value) {
                    $('#' + key).parent().addClass('has-error has-feedback');
                    $.each(value, function (key2, value2) {
                        $(formulario).find('#' + key).parent().append("<span class='help-block'>" + value2 + "</span>");
                    });
                });
            }
        }
    });
}


