const fs = require('fs');
const sass = require('node-sass');
const packageImporter = require('node-sass-package-importer');

exports.default = (output, base = false, variables = {}, style = '') => {
  let data = Object.keys(variables)
    .map((key) => `$${key}: ${variables[key]};`).join('\n') +
    `
    ${base ? `@import "./base/${base}/variables";` : ''}
    @import "~bootstrap/scss/bootstrap";
    ${base ? `@import "./base/${base}/bootswatch";` : ''}
    ${style}`;

  sass.render({
    data,
    importer: packageImporter(),
    outputStyle: process.env.NODE_ENV === 'production' ? 'compressed' : 'nested',
    sourceMapEmbed: false
  }, (error, result) => {
    if (error) return console.error(error);
    let outputFile = `./build/${output}.css`;
    fs.writeFileSync(outputFile, result.css);
    console.log('done', outputFile);
  });
}

