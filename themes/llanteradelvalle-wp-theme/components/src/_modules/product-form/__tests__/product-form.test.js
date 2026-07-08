'use strict';

import ProductForm from '../product-form';

describe('ProductForm View', function() {

  beforeEach(() => {
    this.productForm = new ProductForm();
  });

  it('Should run a few assertions', () => {
    expect(this.productForm);
  });

});
