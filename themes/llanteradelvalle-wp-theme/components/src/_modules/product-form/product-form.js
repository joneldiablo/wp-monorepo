'use strict';
import { Component } from "..";
import wp from "../../_scripts/wp";
import DeliveryForm from "../delivery/delivery";

const $ = jQuery;
const defaultConf = {
  price: 3000,
  quantity: 1,
  amount: 6000,
  product: 'Llanta default',
  id: 34674
};
export default class ProductForm extends Component {
  constructor(conf = {}) {
    super(Object.assign(defaultConf, conf));

    const pf = this;
    pf.name = 'product-form';

    this.delivery = new DeliveryForm();

    $('#quantity').val(pf.conf.quantity).on('input', (e) => {
      pf.conf.quantity = parseInt(e.target.value);
      pf.delivery.conf.quantity = pf.conf.quantity;
      this.render();
    });
    this.getProduct();
    $('#d_workshop').change(() => pf.render());
    $('input[name="product_delivery"]').click(() => pf.render());
    $('div.quantity').hide();
  }

  render() {
    $('#product_name').val(this.conf.product);
    $('#price').val(this.formatN(this.conf.price));
    $('#amount').val(this.calcAmount());
    $('.quantity input[name=quantity]').val(this.conf.quantity);
    let subt = this.delivery.subtotal() + this.conf.amount;
    $('#delivery_subtotal').val(this.formatN(subt));
  }

  calcAmount() {
    this.conf.amount = Math.round(100 * this.conf.price * this.conf.quantity) / 100;
    return this.formatN(this.conf.amount);
  }

  getProduct() {
    let id = $('button[name=add-to-cart]').val() || this.conf.id;
    $.when(
      $.ajax(wp.siteurl + '/wp-json/llv/v1/products/' + id),
      $.ajax(wp.siteurl + '/wp-json/llv/v1/install_addons')
    ).then(([product], [deliveries]) => {
      let installationAddons = { workshop: [], home: [], shipping: [] };
      deliveries.forEach(d => {
        Object.keys(installationAddons).forEach(i => {
          if (d.installationType === i)
            installationAddons[i].push(d);
        });
      });
      this.conf.price = product.price;
      this.conf.product = product.name;
      this.delivery.conf.workshops = installationAddons.workshop;
      this.delivery.renderWorkshopOpts();
      this.delivery.conf.home = installationAddons.home[0];
      this.delivery.conf.shipping = installationAddons.shipping[0] || { price: 0, id: 0 };
      this.delivery.render();
      this.render();
    });
  }
}
