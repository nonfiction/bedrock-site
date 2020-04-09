// This is contains JavaScript for the theme that loads before the </body>
import './theme.css';

// Create new HTML element from string
const el = ( domstring ) => {
  const html = new DOMParser().parseFromString( domstring , 'text/html');
  return html.body.firstChild;
};

// Append HTML string to selector
const append = ( selector, domstring) => {
  document.querySelectorAll( selector ).forEach( (parent) => {
    parent.appendChild( el(domstring) ); 
  });
}

// Prepend HTML string to selector
const prepend = ( selector, domstring ) => {
  document.querySelectorAll( selector ).forEach( (parent) => {
    parent.insertBefore( el(domstring), parent.childNodes[0] );
  });
}

// Catch any SVG's that were missed
SVGInject(document.querySelectorAll('img[src$="svg"]'));



// import $ from 'jquery';
// console.log(q('svg'));
