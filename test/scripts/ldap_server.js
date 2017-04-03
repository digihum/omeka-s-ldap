var ldap = require('ldapjs'),
    server = ldap.createServer(),
    addrbooks = {}, userinfo = {},
    ldap_port = 1389,
    basedn = "dc=example, dc=com",
    company = "Example";

const contacts = [
  { 
    username: 'tim',
    password: 'pass',
    id: 2,
    email: 'test@test.com',
    name: 'Tim Hollies'
  }
];

for (var i = 0; i < contacts.length; i++) {
  if (!addrbooks.hasOwnProperty(contacts[i].username)) {
    addrbooks[contacts[i].username] = [];
    userinfo["cn=" + contacts[i].username + ", " + basedn] = {
      abook: addrbooks[contacts[i].username],
      pwd: contacts[i].password
    };
  }

  var p = contacts[i].name.indexOf(" ");
  if (p != -1)
    contacts[i].firstname = contacts[i].name.substr(0, p);

  p = contacts[i].name.lastIndexOf(" ");
  if (p != -1)
    contacts[i].surname = contacts[i].name.substr(p + 1);

  addrbooks[contacts[i].username].push({
    dn: "cn=" + contacts[i].name + ", " + basedn,
    attributes: {
      objectclass: [ "top" ],
      cn: contacts[i].name,
      mail: contacts[i].email,
      givenname: contacts[i].firstname,
      sn: contacts[i].surname,
      ou: company
    }
  });
}

server.bind(basedn, function (req, res, next) {
  var username = req.dn.toString(),
      password = req.credentials;

  if (!userinfo.hasOwnProperty(username) ||
       userinfo[username].pwd != password) {
    return next(new ldap.InvalidCredentialsError());
  }

  res.end();
  return next();
});

server.search(basedn, function(req, res, next) {
  var binddn = req.connection.ldap.bindDN.toString();

  if (userinfo.hasOwnProperty(binddn)) {
    for (var i = 0; i < userinfo[binddn].abook.length; i++) {
      if (req.filter.matches(userinfo[binddn].abook[i].attributes))
        res.send(userinfo[binddn].abook[i]);
    }
  }
  res.end();
});

server.listen(ldap_port, function() {
  console.log("Addressbook started at %s", server.url);
});
