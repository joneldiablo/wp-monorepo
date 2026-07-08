'use strict';
import wp from "../../_scripts/wp";
import { Component } from "../index";
const $ = jQuery;
const defaultConf = {
  ancho: false,
  serie: false,
  diametro: false,
  sizes: [],
  sizesAncho: [],
  sizesSerie: [],
  sizesDiametro: []
};
export default class FormSize extends Component {
  constructor(conf = {}) {
    super(Object.assign(defaultConf, conf));
    let fs = this;
    this.name = 'form-size';
    $.ajax(wp.siteurl + "/wp-json/llv/v1/ancho")
      .done((n) => {
        fs.conf.sizes = n;
        let setAncho = new Set();
        n.forEach(s => {
          let ancho = s.match(/^(\d{3})/);
          if (ancho && ancho[0])
            setAncho.add(ancho[0]);
        });
        fs.conf.sizesAncho = Array.from(setAncho);
        let opts = fs.conf.sizesAncho.map(r => $("<option>", { value: r }).text(r));
        $("#ancho").append(opts);
      });
    $("#ancho").change(e => {
      let ancho = e.target.value;
      let opts = fs.conf.sizes.reduce((filtered, size) => {
        if (size.startsWith(ancho)) {
          let serie = size.match(/^\d{3}\/(\d+)[rR]\d+/);
          filtered.add(serie ? serie[1] : false);
        }
        return filtered;
      }, new Set());
      $("#serie").empty();
      $("#serie").append($('<option>', {
        value: false,
        selected: true,
        disabled: true
      }).text('Serie'));
      $("#serie").append($('<option>', { disabled: true }).text('--------'));
      $("#diametro").empty();
      $("#diametro").append($('<option>', {
        value: false,
        selected: true,
        disabled: true
      }).text('Diametro'));
      $("#diametro").append($('<option>', { disabled: true }).text('--------'));
      opts.forEach(opt => {
        let $opt = $('<option>', { value: opt }).text(opt);
        if (!opt) {
          $opt.text('Sin serie definida');
        }
        $("#serie").append($opt);
      });
      fs.conf.serie = false;
      fs.conf.diametro = false;
      fs.render();
    });
    $('#serie').change(e => {
      let ancho = fs.conf.ancho;
      let serie = e.target.value === 'false' ? false : e.target.value;
      let starts = ancho + (serie ? '/' + serie : '') + 'R';
      let opts = fs.conf.sizes.reduce((filtered, size) => {
        if (size.startsWith(starts)) {
          filtered.add(size);
        }
        return filtered;
      }, new Set());
      $("#diametro").empty();
      $("#diametro").append($('<option>', {
        value: false,
        selected: true,
        disabled: true
      }).text('Diametro'));
      $("#diametro").append($('<option>', { disabled: true }).text('--------'));
      opts.forEach(o => {
        let opt = o.match(/\d+$/);
        let $opt = $('<option>', { value: opt }).text(opt);
        $("#diametro").append($opt);
      });
      fs.conf.diametro = false;
      fs.render();
    });
    $('.form-size form select').change(e => {
      $(e.target).addClass('is-valid').removeClass('is-invalid');
      fs.conf[e.target.name] = $(e.target).find('option:selected').val();
      if (fs.conf[e.target.name] === 'false') {
        fs.conf[e.target.name] = false;
      }
      fs.render();
    });
    $('.form-size form').submit(e => {
      e.preventDefault();
      if (!this.conf.ancho)
        return $('#ancho').addClass('is-invalid');
      if (!this.conf.diametro)
        return $('#diametro').addClass('is-invalid');
      let p = [
        this.conf.ancho,
        this.conf.serie ? '-' + this.conf.serie : '',
        'R',
        this.conf.diametro
      ].join('');
      window.location.href = wp.siteurl + '/categoria-producto/medida/' + p;
    });
  }
  render() {
    let p = [
      this.conf.ancho || '',
      this.conf.serie ? '/' + this.conf.serie : '',
      'R',
      this.conf.diametro || ''
    ].join('');
    $('.form-size p.medida').text(p);
  }
}
