import React from 'react';
import { render } from 'react-dom';
import SearchService from 'diablo-components/src/js/basic/search-service';

document.addEventListener('DOMContentLoaded', async () => {
  const redir = (e, search, response) => {
    if (!Array.isArray(response)) return window.location.assign(response.route);
    let res = response;
    let vendors = res.reduce((vendors, item) => {
      if (item.type === 'vendor') vendors.push(item.id);
      return vendors;
    }, []);
    let url = new URL(dspApiSettings.shop);
    let params = new URLSearchParams(location.search);
    url.search = params;
    url.searchParams.set('s', search);
    if (vendors.length) url.searchParams.set('vendors', vendors);
    url.searchParams.set('post_type', 'product');
    window.location.assign(url);
  }
  let searchString = (new URL(window.location)).searchParams.get('s');
  let dspContainers = document.querySelectorAll('.dsp-container');
  dspContainers.forEach(dc => render(<SearchService
    service={dspApiSettings.root + dspApiSettings.versionString + 'autocomplete'}
    action={(e, search, item) => redir(e, search, item)}
    init={searchString}
  />, dc));
});