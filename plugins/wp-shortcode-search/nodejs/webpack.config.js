const path = require('path');
const fs = require('fs');

module.exports = {
  entry: {
    search: './src/search.jsx',
    select: './src/select.jsx',
  },
  output: {
    path: path.resolve(__dirname, '../assets/js'),
    filename: '[name].bundle.min.js'
  },
  module: {
    rules: [
      {
        test: /\.(js|jsx|mjs)$/,
        include: [
          path.resolve(__dirname, 'src'),
          fs.realpathSync(path.resolve(__dirname, './node_modules/diablo-components'))
        ],
        use: {
          loader: 'babel-loader',
          options: {
            cacheDirectory: true,
            plugins: [
              '@babel/plugin-proposal-class-properties',
              ['@babel/plugin-transform-react-jsx']
            ],
            presets: [
              ['@babel/preset-env', {
                "targets": { "browsers": ["last 2 chrome versions"] }
              }]
            ]
          }
        }
      }
    ]
  },
  resolve: {
    extensions: ['*', '.js', '.jsx'],
    /* alias: {
      'react': 'preact/compat',
      'react-dom': 'preact/compat',
      'react-dom/test-utils': 'preact/test-utils'
    } */
  },
  externals: {
    react: 'React',
    'react-dom': 'ReactDOM',
    //reactstrap: 'Reactstrap'
  },
  optimization: {
    minimize: true
  }
};