const fs = require('fs');
const buildScss = require('./build-scss').default;

const main = () => {
  let paths = fs.readdirSync('./themes');
  console.log(paths);
  paths.forEach(path => {
    let theme = require('./themes/' + path);
    buildScss(theme.slug, theme.base, theme.variables, theme.style);
  });
}

main();
