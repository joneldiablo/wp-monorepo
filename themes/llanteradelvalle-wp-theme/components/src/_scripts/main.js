import FormSize from "../_modules/form-size/form-size";
import ProductForm from "../_modules/product-form/product-form";

jQuery($ => {
  if ($('.form-size').length) {
    new FormSize();
  }
  if ($('.product-form').length) {
    new ProductForm();
  }
});
