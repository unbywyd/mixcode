const path = require('path');

module.exports = {
  entry:{
      index:  './source/js/index.js',
      codemirror:  './source/js/codemirror.js',
      translates: './source/js/translates.js'
  },
  output: {
    filename: "[name].min.js",
    path: path.resolve(__dirname, "dist/js"),
    publicPath: path.resolve(__dirname, "dist"),
  },
  devServer: {
    compress: true,
    port: 9100,
    static: {
        directory: path.join(__dirname, 'dist'),
    }/*,
    devMiddleware: {
        writeToDisk: false
    }*/
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: [
          {
            loader: "babel-loader",
            options: {
              comments: false,
              presets: [
                [
                  "@babel/preset-env",
                  {
                    targets: {
                      browsers: ["last 2 versions", "ie >= 9"],
                    },
                    debug: false,
                  },
                ],
              ],
              plugins: [
                "@babel/plugin-transform-runtime",
                "@babel/plugin-transform-object-assign",
              ],
            },
          },
        ],
      },     
      {
        test: /\.html$/i,
        use: "raw-loader",
      },
      {
        test: /\.css$/i,
        use: ["style-loader", "css-loader"]  
      }    
    ],
  },
};