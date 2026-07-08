'use strict';
import { Component } from "..";
const $ = jQuery;
const defaultConf = {
  workshops: [],
  workshop: null,
  home: null,
  shipping: null,
  quantity: 1,
  amount: 0
};
export default class DeliveryForm extends Component {
  constructor(conf = {}) {
    super(Object.assign(defaultConf, conf));
    let df = this;
    df.renderWorkshopOpts();
    df.name = 'delivery-form';
    df.changeTab = df.changeTab.bind(this);
    df.changeWorkshop = df.changeWorkshop.bind(this);
    df.getZipCodes = df.getZipCodes.bind(this);

    $('input[name="product_delivery"]').click(df.changeTab);
    $('#d_workshop').change(df.changeWorkshop);
    $('[name="address[cp]"]').on('input', df.getZipCodes);
    $('#quantity').on('input', (e) => setTimeout(() => df.render(), 100));
  }
  renderWorkshopOpts() {
    $('#d_workshop').empty();
    this.conf.workshops.forEach(w => {
      let $opt = $('<option>', { value: w.id })
        .text(w.title);
      $('#d_workshop').append($opt);
    });
    $('#d_workshop').trigger('change');
  }
  render() {
    if (this.conf.workshop) {
      $('.workshop-address-link')
        .attr('href', 'https://www.google.com.mx/maps/search/' + this.conf.workshop.address)
        .text(this.conf.workshop.address);
      $('#wip .priceInst').text(this.formatN(this.conf.workshop.price, true));
      $('#wip .pricePerUnit').text(this.formatN(this.conf.workshop.pricePerUnit, true));
      $('#wip .totalTaller').text(this.formatN(this.subtotal(), true));
    }
    if (this.conf.home) {
      $('#homePrice').text(this.formatN(this.conf.home.price, true));
      $('#quantityHome').text(this.conf.quantity);
      $('#homePriceQuantity').val(
        this.formatN(this.conf.quantity * this.conf.home.pricePerUnit, true)
      );
    }
  }
  subtotal() {
    let tab = $('input[name="product_delivery"]:checked').val();
    switch (tab) {
      case 'workshop':
        this.conf[tab] = this.conf.workshops.find(w => w.id === parseInt($('#d_workshop').val()));
      case 'home':
      case 'shipping':
      default:
        break;
    }
    let price = parseFloat(this.conf[tab].price || 0);
    let pricePerUnit = parseFloat(this.conf[tab].pricePerUnit || 0);
    let subtotal = price + (this.conf.quantity * pricePerUnit);
    return subtotal;
  }
  changeTab(e) {
    this.conf.workshop = null;
    this.conf.amount = 0;
    const $this = $(e.target);
    $this.removeClass('active');
    $('input[name="product_delivery"]')
      .closest('.nav-link').removeClass('active border');
    $this.closest('.nav-link').addClass('active border');
    $this.tab('show');
    if ($this.val() !== 'workshop') {
      let id = this.conf[$this.val()].id;
      $('[name=addon]').val(id);
      $($this.attr('data-target')).append($('fieldset.address'));
    }
    $('.tab-content input:not([type=hidden])').val('');

    $('.tab-content .tab-pane').find('select, input, textarea').prop('required', false);
    $($this.attr('data-target')).find('select, input, textarea').prop('required', true);
  }
  changeWorkshop(e) {
    this.conf.workshop = this.conf.workshops.find(w => w.id === parseInt(e.target.value));
    $('[name=addon]').val(e.target.value);
    this.render();
  }
  getZipCodes(e) {
    if (this.ziptimeout) {
      clearTimeout(this.ziptimeout);
    }
    this.ziptimeout = setTimeout(() => {
      let zip = e.target.value;
      let zipcodesValid = /^(436|437|4371|4370|420|421|4211|4210)/;
      if (!zip.match(zipcodesValid)) {
        $('small.cp-error').text('No contamos con servicio para este código postal');
        $('#cps').empty();
      } else {
        $('small.cp-error').text('');
        $.ajax('https://api-sepomex.hckdrk.mx/query/search_cp/' + zip)
          .then(function (res) {
            var filter = $.Deferred();
            if (!res.error) {
              let zipcodes = res.response.cp.filter(z => z.startsWith(zip)).splice(0, 5);
              filter.resolve(zipcodes);
            } else {
              filter.reject(res);
            }

            return filter.promise();
          })
          .done((zipcodes) => {
            $('#cps').empty();
            zipcodes.forEach((z => {
              let $option = $('<option>', { value: z });
              $('#cps').append($option);
            }));
          })
          .fail((e) => {
            console.error(e);
          });
        if (zip.length === 5) {
          $.ajax('https://api-sepomex.hckdrk.mx/query/info_cp/' + zip + '?type=simplified')
            .then(function (res) {
              var filter = $.Deferred();
              if (!res.error) {
                filter.resolve(res.response);
              } else {
                filter.reject(res);
              }
              return filter.promise();
            })
            .done((data) => {
              $('[name="address[city]"]').val(data.municipio);
              $('[name="address[state]"]').val(data.estado);
              $('[name="address[neighborhood]"]').val(data.asentamiento[0]);
              $('#neighborhoods').empty();
              data.asentamiento.forEach((a => {
                let $option = $('<option>', { value: a });
                $('#neighborhoods').append($option);
              }));
            })
            .fail((e) => {
              console.error(e);
            });
        }
      }

    }, 660);
  }
}
