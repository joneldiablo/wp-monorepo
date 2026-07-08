const fs = require('fs');
const express = require('express');
const cors = require('cors')
const app = express();
const publicPath = './build';
const port = process.env.PORT || 3300;
app.use(cors());
app.use(express.static(publicPath));
app.get('/', (req, res) => {
  let paths = fs.readdirSync('./build');
  res.json(paths)
});
app.listen(port, () => {
  console.log('Server is up at ' + port);
});
