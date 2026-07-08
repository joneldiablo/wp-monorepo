export class Component {
  constructor(conf) {
    this.update(conf);
  }
  update(conf = {}) {
    let conf1 = Object.assign({}, this.conf, conf);
    this.conf = conf1;
  }
  formatN(n, dollar = false) {
    let value = Number(n).toLocaleString('us', { minimumFractionDigits: 2 });
    return dollar ? '$ ' + value : value;
  }
  render() {
    throw 'Abstract method, rewrite!!!!';
  }
}
