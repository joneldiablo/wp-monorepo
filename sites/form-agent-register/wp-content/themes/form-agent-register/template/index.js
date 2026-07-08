const sass = require('node-sass');
const fs = require('fs');
sass.render({
  file: 'scss/style.scss'
}, (error, result) => {
  if (error)
    console.log(error);
  else {
    console.log('write file');
    fs.writeFileSync('../style.css', result.css);
  }
  process.exit(0);
});