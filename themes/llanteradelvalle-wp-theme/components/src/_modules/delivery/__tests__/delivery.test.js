'use strict';

import Delivery from '../delivery';

describe('Delivery View', function() {

  beforeEach(() => {
    this.delivery = new Delivery();
  });

  it('Should run a few assertions', () => {
    expect(this.delivery);
  });

});
