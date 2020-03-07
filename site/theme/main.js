import './styles/main.css';
import './scripts/main.js';

if (module.hot) {
  module.hot.accept('./scripts/main.js', function() {
    console.log('Accepting the main.js  module!');
  });
  module.hot.accept('./styles/main.css', function() {
    console.log('Accepting the main.css  module!');
  });
}