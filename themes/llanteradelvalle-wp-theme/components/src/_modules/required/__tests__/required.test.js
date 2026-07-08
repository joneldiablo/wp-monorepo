'use strict';

import Required from '../required';

describe('Required View', function() {

  beforeEach(() => {
    this.required = new Required();
  });

  it('Should run a few assertions', () => {
    expect(this.required);
  });

});
