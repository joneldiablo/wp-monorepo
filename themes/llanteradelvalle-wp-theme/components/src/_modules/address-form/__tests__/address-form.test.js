'use strict';

import AddressForm from '../address-form';

describe('AddressForm View', function() {

  beforeEach(() => {
    this.addressForm = new AddressForm();
  });

  it('Should run a few assertions', () => {
    expect(this.addressForm);
  });

});
