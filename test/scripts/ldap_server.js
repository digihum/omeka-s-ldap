var ldap = require('ldapjs');
 
var server = ldap.createServer();
 
server.search('dc=example', function(req, res, next) {
  var obj = {
    dn: req.dn.toString(),
    attributes: {
      objectclass: ['organization', 'top'],
      o: 'example'
    }
  };
 
  if (req.filter.matches(obj.attributes))
  res.send(obj);
 
  res.end();
});
 
server.listen(1389, function() {
  console.log('ldapjs listening at ' + server.url);
});
