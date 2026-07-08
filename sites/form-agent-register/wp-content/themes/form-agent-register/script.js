"use strict";
const zipCodes = 'https://dbl-codigos-postales.herokuapp.com';


$(function () {
  $('form>fieldset').each((i, e) => {
    $('<hr />').insertBefore($(e).find('>.form-group'));
  });
  $('fieldset.agreement>.form-group legend').css({
    fontSize: '1rem'
  });
  $('fieldset.agreement>.form-group>fieldset+fieldset')
    .each((i, e) => {
      $('<hr />').insertBefore($(e));
    });
  $('[data-toggle="tooltip"]').tooltip();

  $('input[type="file"]').on('change', (e) => {
    let fileName = e.target.files[0].name;
    $(e.target).parent().find('label')
      .addClass("selected").text(fileName);
  });

  // nacionalidad
  $('#general_nationality')
    .prepend($('<option>', { disabled: true }).text('------------'))
    .prepend($('<option>', { value: 'Mexicana' }).text('Mexicana'));

  // colonias
  $('.agreement_address input')
    .attr('autocomplete', 'nope');
  let $newSelect = $('<select>');
  $.each($('#agreement_address_neighborhood')[0].attributes, function () {
    if (this.specified) {
      let value = this.value;
      if (this.name === 'id') {
        value += '_select';
      }
      $newSelect.attr(this.name, value);
    }
  });
  $newSelect.append(
    $('<option>', { disabled: true, hidden: true, selected: true })
      .text($('#agreement_address_neighborhood').attr('placeholder'))
  );
  $newSelect.insertBefore('#agreement_address_neighborhood');
  $('#agreement_address_neighborhood').remove();
  // -----

  $("#agreement_address_zipcode")
    .tooltip({ title: "Escribe uno o más dígitos de tu código postal y selecciona un elemento de la lista.", trigger: "focus" });
  $("#agreement_address_zipcode").on("focus", function () {
    $(this).trigger("keyup");
  });
  $('#agreement_address_zipcode')
    .autoComplete({
      minLength: 1,
      resolver: 'custom',
      events: {
        searchPre: function (newValue) {
          $('#agreement_address_zipcode').parent()
            .find('i').remove();
          $('#agreement_address_zipcode').parent()
            .append('<i class="fa fa-spinner fa-pulse ml-2"></i>');
          return newValue;
        },
        search: function (qry, callback) {
          $.ajax(`${zipCodes}/v2/buscar`,
            {
              data: { 'codigo_postal': qry }
            }
          ).done(function (res) {
            callback(res.codigos_postales)
          }).always(() => {
            $('#agreement_address_zipcode').parent()
              .find('i').remove();
          });
        },
        searchPost: function (list) {
          return list;
        }
      }
    })
    .on('autocomplete.select', function (evt, item) {
      $.ajax(`${zipCodes}/v2/codigo_postal/${item}`)
        .done(function (res) {
          $('#agreement_address_state').trigger('change').val(res.estado);
          $('#agreement_address_city').trigger('change').val(res.municipio);
          $('#agreement_address_state').prop('readonly', true);
          $('#agreement_address_city').prop('readonly', true);
          $newSelect.html('');
          $.each(res.colonias, function (i, col) {
            $newSelect.append($('<option>', { value: col }).text(col));
          });
          $newSelect.trigger('change');
        });
    });

  $('form').submit(function (event) {
    event.preventDefault(); //prevent default action 
    $('#confirmModal').modal('show');
  });
  $('#submitConfirm').on('click', (e) => {
    var post_url = $('form').attr("action");
    var request_method = $('form').attr("method");
    var form_data = new FormData($('form')[0]);
    let $thisBtn = $(e.target);
    $thisBtn.prop('disabled', true);
    $thisBtn.append('<i class="fa fa-spinner fa-pulse ml-2"></i>');
    $.ajax({
      url: post_url,
      type: request_method,
      data: form_data,
      dataType: 'json',
      contentType: false,
      cache: false,
      processData: false
    })
      .then(function (res) {
        var filter = $.Deferred();
        if (res.success) {
          filter.resolve(res.data);
        } else {
          filter.reject(res);
        }
        return filter.promise();
      })
      .done(function (data) {
        $('#downloadBtn').
          attr('href', 'data:application/pdf;charset=utf-8;base64,' + data);
        $('form')[0].reset();
        $('form input, select').removeClass('is-valid is-invalid');
        $('.custom-file-label').text('Adjuntar...');
        $('#doneModal').modal('show');
      })
      .fail(errorHandler)
      .always(function () {
        $thisBtn.prop('disabled', false)
          .find('i').remove();
        $('form [type="submit"]').prop('disabled', true);
        $('#confirmModal').modal('hide');
      });
  });

  validations();
  //collapsables();
});

function validations() {
  $('form [type="submit"]').prop('disabled', true);
  // confirmación de correo
  let $emailCol = $('form input[type="email"]').closest('.col');
  let $confirm = $emailCol.clone();
  $confirm.insertAfter($emailCol);
  $emailCol.on('keydown', 'input', (e) => {
    $confirm.find('input').val('').trigger('change');
  })
  $confirm.find('input').attr({
    name: null,
    id: $emailCol.find('input').attr('id') + '-confirm',
    placeholder: 'Confirma ' + $emailCol.find('input').attr('placeholder')
  });
  $confirm.on('keyup', 'input', (e) => {
    let equals = $(e.target).val() === $emailCol.find('input').val();
    if (!equals) {
      e.target.setCustomValidity('El correo no coincide');
    } else {
      e.target.setCustomValidity('');
    }
  });
  // validar campos
  $('input, select').on('keyup change', (e) => {
    clearTimeout(e.target.timeout);
    e.target.timeout = setTimeout(() => {
      let disabled = true;
      if ($('form')[0].checkValidity()) {
        disabled = false;
      }
      $('form [type="submit"]').prop('disabled', disabled);
      if (e.target.id === 'terms') return true;
      if (e.target.checkValidity()) {
        $(e.target).addClass('is-valid').removeClass('is-invalid');
      } else {
        $(e.target).addClass('is-invalid').removeClass('is-valid');
      }
    }, 330);
  });
  $('input[type="date-bs"]')
    .attr({
      autocomplete: 'off'
    }).datepicker({
      language: 'es',
      startView: 'decade',
      format: 'dd/mm/yyyy',
      showClear: true,
      autoclose: true
    });
}

function collapsables() {
  $('fieldset').each((i, e) => {
    $(e).find('>legend')
      .addClass('hover-point d-flex align-items-center')
      .append('<i class="fa fa-chevron-up ml-2" aria-hidden="true" style="font-size:10px"></i>')
      .click(() => {
        $(e).find('>.form-group').collapse('toggle');
      });
    let $f = $(e).find('>.form-group')
    $f.collapse({ parent: $(e).parent() });
    $f.on('hide.bs.collapse show.bs.collapse', function (e) {
      $f.parent().find('>legend').find('.fa')
        .toggleClass('fa-chevron-down fa-chevron-up');
    });
    if (i > 0) {
      setTimeout(() => {
        $f.collapse('hide');
      }, 400);
    }
  });
}

function errorHandler(e) {
  console.error(e);
  let message = '<p class="text-center">¡Atención!</p>';
  switch (e.status) {
    case 400:
      message +=
        `<p class="text-justify">El correo con el cual estás intentando hacer tu registro, 
        ya ha sido utilizado previamente.
        Si ya te has registrado anteriormente y no has recibido una respuesta, comunícate con nosotros al correo <a href="mailto:contacto@tactika.mx">contacto@tactika.mx</a>
      </p>`;
      break;
    default:
      message +=
        `Ha ocurrido un error al hacer el registro, 
      por favor intenta más tarde
      <pre>${JSON.stringify(e, null, 2)}</pre>`;
      break;
  }
  $('#text-error').html(message);
  $('#errorModal').modal('show');
}