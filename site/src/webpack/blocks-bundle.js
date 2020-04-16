// Import all ./blocks/*/*.js files (that aren't index.js)
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('../blocks', true, /^((?!index).)*\.js$/));
