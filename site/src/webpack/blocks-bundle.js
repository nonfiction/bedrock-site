// Import all ./blocks/*/script.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('../blocks', true, /\/script\.js$/));
