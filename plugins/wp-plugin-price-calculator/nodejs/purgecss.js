const PurgeCSS = require('purgecss').default;
const fs = require('fs');

const purgeCSSReq = new PurgeCSS().purge({
  content: ['../assets/*.html', '../assets/js/*.js'],
  css: ['../assets/css/*.css']
});
purgeCSSReq.then(res => {
  res.map(s => fs.writeFileSync(s.file, s.css, 'utf8'));
});
