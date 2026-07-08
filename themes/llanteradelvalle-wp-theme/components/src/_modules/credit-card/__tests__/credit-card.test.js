'use strict';

import CreditCard from '../credit-card';

describe('CreditCard View', function() {

  beforeEach(() => {
    this.creditCard = new CreditCard();
  });

  it('Should run a few assertions', () => {
    expect(this.creditCard);
  });

});
