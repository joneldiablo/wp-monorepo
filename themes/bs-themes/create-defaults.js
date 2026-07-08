const buildScss = require('./build-scss').default;
const baseFiles = [
  'cerulean', 'cosmo', 'cyborg', 'darkly',
  'flatly', 'journal', 'litera',
  'lumen', 'lux', 'materia', 'minty',
  'pulse', 'sandstone', 'simplex',
  'sketchy', 'slate', 'solar',
  'spacelab', 'superhero', 'united', 'yeti'];

buildScss('bs');
baseFiles.forEach(bf => {
  buildScss(bf, bf);
});
