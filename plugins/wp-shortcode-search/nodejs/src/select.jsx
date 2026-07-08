import React from 'react';
import { render } from 'react-dom';
import FilterSelect from 'diablo-components/src/js/basic/filter-select';

document.addEventListener('DOMContentLoaded', async () => {
  const onChange = (e, zoneId) => {
    let url = new URL(dfcApiSettings.shop);
    let params = new URLSearchParams(location.search);
    url.search = params;
    url.searchParams.set('zone', zoneId);
    if (zoneId === null) url.searchParams.delete('zone');
    window.location.assign(url);
  }
  let options = [];
  let dfcContainers = document.querySelectorAll('.dfc-container');
  dfcContainers.forEach(dfc => {
    let thisOptions;
    if (dfc.dataset.options) {
      let zones = dfc.dataset.options.split('|');
      thisOptions = zones.map(c => {
        let [label, value] = c.split(':');
        return { label, value };
      });
    }
    let zoneId = (new URL(window.location)).searchParams.get('zone');
    return render(<FilterSelect
      options={thisOptions}
      onChange={onChange}
      init={zoneId}
      label='Todo México'
    />, dfc)
  });
});