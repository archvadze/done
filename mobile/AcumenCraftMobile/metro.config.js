const { getDefaultConfig } = require('expo/metro-config');

const config = getDefaultConfig(__dirname);

// Production optimizations
if (process.env.NODE_ENV === 'production') {
  config.transformer.minifierConfig = {
    mangle: {
      keep_fnames: true,
    },
    output: {
      ascii_only: true,
      quote_keys: true,
      wrap_iife: true,
    },
    sourceMap: {
      includeSources: false,
    },
    toplevel: false,
    warnings: false,
  };

  // Enable advanced optimizations
  config.resolver.platforms = ['native', 'android', 'ios'];
  config.transformer.enableBabelRCLookup = false;
  config.transformer.enableBabelRuntime = false;
}

// Asset configuration
config.resolver.assetExts.push(
  'db', 'mp3', 'ttf', 'obj', 'png', 'jpg', 'jpeg', 'gif', 'svg'
);

// Source map configuration
config.serializer.createModuleIdFactory = function () {
  return function (path) {
    let name = path.substr(1);
    const segments = name.split('/');
    name = segments[segments.length - 1];
    name = name.replace(/\.[^/.]+$/, '');
    return name;
  };
};

module.exports = config;
