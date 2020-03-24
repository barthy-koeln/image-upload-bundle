const Encore = require('@symfony/webpack-encore')

Encore
  .enableBuildNotifications(true)

  .enableSingleRuntimeChunk()

  .setOutputPath('src/Resources/public/build')
  .setPublicPath('bundles/barthyimageupload/build/')
  .setManifestKeyPrefix('')

  .cleanupOutputBeforeBuild()

  .enableSourceMaps(!Encore.isProduction())

  .addEntry('js/main', './src/Resources/assets/js/BarthyImageUpload.js')
  .addStyleEntry('css/main', './src/Resources/assets/scss/barthy-image-upload.scss')

  .enableSassLoader()
  .enablePostCssLoader()

  .configureBabel(function (babelConfig) {

  }, {
    useBuiltIns: 'usage',
    corejs: '3'
  })

  .configureTerserPlugin(function (options) {
    options.extractComments = false
    options.terserOptions   = {
      output: {
        comments: false
      }
    }
  })

const config = Encore.getWebpackConfig()

if (Encore.isProduction()) {
  config.optimization = {
    minimize: true,
  }
}

module.exports = config
