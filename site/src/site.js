// This is the site entry file for webpack--do not edit!
// - for CSS, edit assets/site.css
// - for JS, edit assets/site.js

import '../assets/site.css';
import '../assets/site.js';

if (module.hot) {
  module.hot.accept('../assets/site.css', function() {
    console.log('Accepting the site.css module!');
  });
  module.hot.accept('../assets/site.js', function() {
    console.log('Accepting the site.js module!');
  });
}
