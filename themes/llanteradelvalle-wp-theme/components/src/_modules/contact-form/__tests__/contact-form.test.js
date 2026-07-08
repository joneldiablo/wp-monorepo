'use strict';

import ContactForm from '../contact-form';

describe('ContactForm View', function() {

  beforeEach(() => {
    this.contactForm = new ContactForm();
  });

  it('Should run a few assertions', () => {
    expect(this.contactForm);
  });

});
