import React from 'react';
import ReactDOM from 'react-dom';
import PriceCalculator from 'diablo-components/src/js/basic/price-calculator';

(($) => $(document).on('ready', async () => {

  const getFees = () => {
    const service = jdpcApiSettings.root + jdpcApiSettings.versionString + 'fees';
    return fetch(service, {
      headers: new Headers({
        "Content-Type": "application/json",
        "X-WP-Nonce": jdpcApiSettings.nonce
      })
    }).then(r => r.json());
  }

  const onClick = (e, price) => {
    let regularPrice = e.target.closest('.form-field,.form-group-row')
      .querySelector(selector);
    regularPrice.value = price;
  }

  const init = () => {
    let $price = document.querySelectorAll(selector);
    if (!$price.length) return;
    document.querySelectorAll('.jdpc-wrapper')
      .forEach(jdpc => ReactDOM.unmountComponentAtNode(jdpc));
    $price.forEach(p => {
      let container = p.parentElement.querySelector('.jdpc-wrapper');
      if (!container) {
        container = document.createElement('span');
        container.className = 'jdpc-wrapper description';
        p.parentElement.appendChild(container);
      }
      ReactDOM.render(<PriceCalculator fees={fees} onClick={onClick} />, container);
    });
  }

  const selector = 'input#_regular_price,input[id^=variable_regular_price]';
  const fees = await getFees();
  $(document).on('woocommerce_variations_loaded', init);
  /*  $(document).ajaxComplete((e, xhr, settings = {}) => {
     console.log('settings.data', settings.data); //encontrar todas las llamadas ajax
   }); */

  init();

}))(jQuery);

